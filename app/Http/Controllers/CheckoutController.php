<?php

namespace App\Http\Controllers;

use App\Mail\NouvelleCommandeMail;
use App\Models\Client;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Services\PromotionService;
use App\Support\Panier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CheckoutController extends Controller
{
    /**
     * Coûts de livraison disponibles — partagés entre l'affichage (index)
     * et le calcul serveur faisant foi (valider).
     */
    private const COUTS_LIVRAISON = [
        'gratuite' => 0,
        'express' => 2500,
    ];

    public function __construct(private readonly PromotionService $promotions)
    {
    }

    public function index(): RedirectResponse|\Illuminate\View\View
    {
        $lignes = Panier::lignes();

        if ($lignes->isEmpty()) {
            return redirect()->route('panier.index')
                ->with('erreur', 'Votre panier est vide — ajoutez des produits avant de passer commande.');
        }

        // Aperçu des remises (le code éventuel + les remises membres déductibles
        // du client en session) ; les frais réels dépendent du mode choisi et
        // sont recalculés côté client puis, de façon faisant foi, à la validation.
        $promo = $this->promotions->evaluer(
            $lignes,
            Panier::codePromo(),
            $this->promotions->contexteDepuisEmail(null, session('client_id')),
        );

        return view('checkout.index', [
            'lignes' => $lignes,
            'promo' => $promo,
            'sousTotal' => $promo['sous_total'],
            'coutsLivraison' => self::COUTS_LIVRAISON,
        ]);
    }

    public function valider(Request $request): RedirectResponse
    {
        if (Panier::estVide()) {
            return redirect()->route('panier.index')->with('erreur', 'Votre panier est vide.');
        }

        $donnees = $request->validate([
            'prenom' => ['required', 'string', 'max:100'],
            'nom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'telephone' => ['required', 'string', 'max:30'],
            'adresse' => ['required', 'string', 'max:255'],
            'ville' => ['required', 'string', 'max:100'],
            'code_postal' => ['nullable', 'string', 'max:20'],
            'mode_livraison' => ['required', 'in:gratuite,express'],
            'mode_paiement' => ['required', 'in:carte,mobile_money,livraison'],
        ]);

        $fraisLivraison = (float) self::COUTS_LIVRAISON[$donnees['mode_livraison']];

        try {
            $commande = DB::transaction(fn () => $this->creerCommande($donnees, $fraisLivraison));
        } catch (RuntimeException $e) {
            return back()->withInput()->with('erreur', $e->getMessage());
        }

        Panier::vider();
        session(['client_id' => $commande->client_id]);

        $this->notifierAdmin($commande);

        return redirect()
            ->route('checkout.confirmation', $commande)
            ->with('succes', "Votre commande {$commande->numero} a bien été enregistrée !");
    }

    /**
     * Prévient l'administrateur par e-mail qu'une nouvelle commande a été
     * passée. L'échec d'envoi (SMTP indisponible, etc.) est journalisé mais ne
     * doit jamais interrompre le parcours d'achat déjà validé.
     */
    private function notifierAdmin(Commande $commande): void
    {
        $destinataire = config('nubelle.admin_email');

        if (blank($destinataire)) {
            return;
        }

        try {
            $commande->loadMissing(['lignes.produit', 'client', 'codePromo']);
            Mail::to($destinataire)->send(new NouvelleCommandeMail($commande));
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function confirmation(Commande $commande): \Illuminate\View\View
    {
        $commande->load(['lignes.produit', 'client', 'codePromo']);

        return view('checkout.confirmation', ['commande' => $commande]);
    }

    /**
     * Construit la commande dans une transaction : reverrouille et
     * revérifie le stock de chaque produit au moment T (protège contre les
     * changements survenus entre l'affichage du panier et la validation),
     * crée/actualise le client, la commande, ses lignes, décrémente le
     * stock et journalise chaque sortie de stock.
     *
     * @param  array<string, mixed>  $donnees
     */
    private function creerCommande(array $donnees, float $fraisLivraison): Commande
    {
        $panierBrut = Panier::contenu();

        if (empty($panierBrut)) {
            throw new RuntimeException('Votre panier est vide.');
        }

        $lignesValidees = [];
        $problemes = [];

        foreach ($panierBrut as $produitId => $quantite) {
            $produit = Produit::query()->lockForUpdate()->find($produitId);

            if (! $produit || ! $produit->actif) {
                $problemes[] = 'Un produit de votre panier n\'est plus disponible.';

                continue;
            }

            if ($produit->stock < $quantite) {
                $problemes[] = "Stock insuffisant pour « {$produit->nom} » ({$produit->stock} disponible(s), {$quantite} demandé(s)).";

                continue;
            }

            $lignesValidees[] = ['produit' => $produit, 'quantite' => $quantite];
        }

        if (! empty($problemes)) {
            throw new RuntimeException(implode(' ', $problemes));
        }

        // Calcul des promotions faisant foi : profil client déduit AVANT de
        // (re)créer le client — de sorte qu'une remise « nouveaux clients » ne
        // se déclenche que sur une première commande — puis chiffrage central
        // sur les produits réellement validés (prix/stock verrouillés).
        $contexte = $this->promotions->contexteDepuisEmail($donnees['email']);
        $promo = $this->promotions->evaluer(
            collect($lignesValidees),
            Panier::codePromo(),
            $contexte,
            $fraisLivraison,
        );

        $client = Client::updateOrCreate(
            ['email' => $donnees['email']],
            [
                'prenom' => $donnees['prenom'],
                'nom' => $donnees['nom'],
                'telephone' => $donnees['telephone'],
                'adresse' => $donnees['adresse'],
                'ville' => $donnees['ville'],
                'code_postal' => $donnees['code_postal'] ?? null,
            ]
        );

        $commande = Commande::create([
            'numero' => $this->genererNumeroUnique(),
            'client_id' => $client->id,
            'code_promo_id' => $promo['code_promo']?->id,
            'statut' => 'en_attente',
            'total' => $promo['total'],
            'total_avant_remise' => $promo['sous_total'],
            'reduction_montant' => $promo['reduction_totale'],
            'reduction_type' => $promo['reduction_type'],
            'remise_membre' => $promo['reduction_membre'],
            'frais_livraison' => $promo['frais_livraison'],
            'adresse_livraison' => trim("{$donnees['adresse']}, {$donnees['ville']} ".($donnees['code_postal'] ?? '')),
            'mode_paiement' => $donnees['mode_paiement'],
        ]);

        $commande->journaliser(
            'creation',
            'en_attente',
            'Commande passée depuis la boutique en ligne.',
            auteurConnecte: false,
        );

        foreach ($lignesValidees as $ligne) {
            $produit = $ligne['produit'];
            $quantite = $ligne['quantite'];

            LigneCommande::create([
                'commande_id' => $commande->id,
                'produit_id' => $produit->id,
                'quantite' => $quantite,
                'prix_unitaire' => $produit->prix,
            ]);

            $produit->decrement('stock', $quantite);

            MouvementStock::create([
                'produit_id' => $produit->id,
                'type' => 'sortie',
                'quantite' => $quantite,
                'motif' => "Commande {$commande->numero}",
            ]);
        }

        return $commande;
    }

    private function genererNumeroUnique(): string
    {
        do {
            $numero = 'NB-'.now()->format('ymd').strtoupper(Str::random(5));
        } while (Commande::where('numero', $numero)->exists());

        return $numero;
    }
}
