<?php

namespace App\Http\Controllers;

use App\Mail\NouvelleCommandeMail;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Commune;
use App\Models\ContenuAccueil;
use App\Models\LigneCommande;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\User;
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
     * Modes de livraison acceptés. « normale » est tarifée à la commune ;
     * « express » et « expedition » n'ont pas de prix fixe (convenu sur
     * WhatsApp). Toutes les commandes sont ensuite finalisées sur WhatsApp.
     */
    private const MODES_LIVRAISON = ['express', 'normale', 'expedition'];

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

        // Aperçu des remises pour le client connecté ; les frais réels dépendent
        // du mode choisi et sont recalculés, de façon faisant foi, à la validation.
        $promo = $this->promotions->evaluer(
            $lignes,
            Panier::codePromo(),
            $this->promotions->contexteDepuisEmail(auth()->user()?->email),
        );

        return view('checkout.index', [
            'lignes' => $lignes,
            'promo' => $promo,
            'sousTotal' => $promo['sous_total'],
            // Communes tarifées pour la « livraison normale ».
            'communes' => Commune::query()->actives()->orderBy('nom')->get(['id', 'nom', 'prix']),
            // Fiche adresse existante du compte (préremplissage du formulaire).
            'client' => Client::firstWhere('email', auth()->user()->email),
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
            'telephone' => ['required', 'string', 'max:30'],
            'commune_id' => ['required', 'exists:communes,id'],
            'ville' => ['required', 'string', 'max:100'],
            'code_postal' => ['nullable', 'string', 'max:20'],
            'mode_livraison' => ['required', 'in:'.implode(',', self::MODES_LIVRAISON)],
            'mode_paiement' => ['required', 'in:carte,mobile_money,livraison'],
        ], [
            'commune_id.required' => 'Veuillez choisir votre commune.',
        ]);

        // Le tunnel étant protégé par le middleware auth, l'utilisateur est
        // toujours connecté : c'est lui qui identifie la commande (user_id).
        $user = $request->user();

        // La commune (obligatoire) sert d'adresse principale. Seule la livraison
        // normale la facture ; express/expédition sont convenues sur WhatsApp.
        $commune = Commune::query()->actives()->find($donnees['commune_id']);

        if ($commune === null) {
            return back()->withInput()->with('erreur', 'La commune sélectionnée n\'est plus disponible.');
        }

        $communeNom = $commune->nom;
        $fraisLivraison = $donnees['mode_livraison'] === 'normale' ? (float) $commune->prix : 0.0;

        try {
            $commande = DB::transaction(fn () => $this->creerCommande($donnees, $fraisLivraison, $communeNom, $user));
        } catch (RuntimeException $e) {
            return back()->withInput()->with('erreur', $e->getMessage());
        }

        Panier::vider();

        $this->notifierAdmin($commande);

        // Redirection vers la page de confirmation, qui ouvre WhatsApp avec le
        // récapitulatif pré-rempli (toutes les commandes se finalisent là-bas).
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

    public function confirmation(Request $request, Commande $commande): \Illuminate\View\View
    {
        // Une commande n'est visible que par son propriétaire (ou un admin).
        abort_unless(
            $commande->user_id === $request->user()->id || $request->user()->estAdmin(),
            403,
        );

        $commande->load(['lignes.produit', 'client', 'codePromo']);

        return view('checkout.confirmation', [
            'commande' => $commande,
            'whatsappUrl' => $this->lienWhatsapp($commande),
        ]);
    }

    /**
     * Lien wa.me pré-rempli vers le WhatsApp de la boutique (numéro géré en
     * back-office). Null si aucun numéro n'est configuré.
     */
    private function lienWhatsapp(Commande $commande): ?string
    {
        $numero = ContenuAccueil::instance()->whatsapp_lien;

        if (blank($numero)) {
            return null;
        }

        return 'https://wa.me/'.$numero.'?text='.rawurlencode($this->messageWhatsapp($commande));
    }

    /**
     * Récapitulatif texte de la commande, envoyé sur WhatsApp par le client
     * pour finaliser (produits, montants, mode de livraison, coordonnées).
     */
    private function messageWhatsapp(Commande $commande): string
    {
        $fmt = fn ($montant) => number_format((float) $montant, 0, ',', ' ').' FCFA';

        $lignes = [
            'Bonjour NUBELLE,',
            "Je souhaite passer la commande *{$commande->numero}*.",
            '',
            'Produits :',
        ];

        foreach ($commande->lignes as $ligne) {
            $nom = $ligne->produit->nom ?? 'Produit';
            $lignes[] = "- {$nom} x{$ligne->quantite} : ".$fmt($ligne->prix_unitaire * $ligne->quantite);
        }

        $modeLabel = Commande::MODES_LIVRAISON_LABELS[$commande->mode_livraison] ?? 'Livraison';
        $ligneLivraison = $commande->mode_livraison === 'normale'
            ? "{$modeLabel} - {$commande->commune} : ".$fmt($commande->frais_livraison)
            : "{$modeLabel} (prix à convenir)";

        $lignes[] = '';
        $lignes[] = 'Sous-total : '.$fmt($commande->total_avant_remise);
        if ((float) $commande->reduction_montant > 0) {
            $lignes[] = 'Réduction : -'.$fmt($commande->reduction_montant);
        }
        $lignes[] = 'Livraison : '.$ligneLivraison;
        $lignes[] = '*Total : '.$fmt($commande->total).'*';

        $client = $commande->client;
        $nomClient = $client ? trim("{$client->prenom} {$client->nom}") : '';

        $lignes[] = '';
        if ($nomClient !== '') {
            $lignes[] = 'Client : '.$nomClient;
        }
        if ($client?->telephone) {
            $lignes[] = 'Téléphone : '.$client->telephone;
        }
        if ($commande->adresse_livraison) {
            $lignes[] = 'Adresse : '.$commande->adresse_livraison;
        }

        return implode("\n", $lignes);
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
    private function creerCommande(array $donnees, float $fraisLivraison, ?string $communeNom, User $user): Commande
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
        $contexte = $this->promotions->contexteDepuisEmail($user->email);
        $promo = $this->promotions->evaluer(
            collect($lignesValidees),
            Panier::codePromo(),
            $contexte,
            $fraisLivraison,
        );

        // Fiche client (carnet d'adresses) rattachée à l'e-mail du compte —
        // conserve la compatibilité avec les promotions et le back-office.
        $client = Client::updateOrCreate(
            ['email' => $user->email],
            [
                'prenom' => $donnees['prenom'],
                'nom' => $donnees['nom'],
                'telephone' => $donnees['telephone'],
                'whatsapp' => $user->whatsapp,
                'adresse' => $communeNom,
                'ville' => $donnees['ville'],
                'code_postal' => $donnees['code_postal'] ?? null,
            ]
        );

        $commande = Commande::create([
            'numero' => $this->genererNumeroUnique(),
            'client_id' => $client->id,
            'user_id' => $user->id,
            'code_promo_id' => $promo['code_promo']?->id,
            'statut' => 'en_attente',
            'total' => $promo['total'],
            'total_avant_remise' => $promo['sous_total'],
            'reduction_montant' => $promo['reduction_totale'],
            'reduction_type' => $promo['reduction_type'],
            'remise_membre' => $promo['reduction_membre'],
            'frais_livraison' => $promo['frais_livraison'],
            'mode_livraison' => $donnees['mode_livraison'],
            'commune' => $communeNom,
            'adresse_livraison' => trim("{$communeNom}, {$donnees['ville']} ".($donnees['code_postal'] ?? '')),
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
