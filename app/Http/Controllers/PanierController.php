<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Services\PromotionService;
use App\Support\Panier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PanierController extends Controller
{
    public function index(PromotionService $promotions)
    {
        $lignes = Panier::lignes();
        $promo = $promotions->evaluer(
            $lignes,
            Panier::codePromo(),
            $promotions->contexteDepuisEmail(null, session('client_id')),
        );

        // Un code devenu invalide (panier modifié depuis) est retiré silencieusement.
        if ($promo['erreur_code'] && Panier::codePromo()) {
            Panier::retirerCodePromo();
        }

        return view('panier.index', [
            'lignes' => $lignes,
            'promo' => $promo,
            'sousTotal' => $promo['sous_total'],
        ]);
    }

    public function ajouter(Request $request, Produit $produit): JsonResponse|RedirectResponse
    {
        $quantite = max(1, (int) $request->input('quantite', 1));
        $resultat = Panier::ajouter($produit, $quantite);

        if ($request->wantsJson()) {
            return response()->json([
                ...$resultat,
                'nbPanier' => Panier::nombreArticles(),
            ], $resultat['succes'] ? 200 : 422);
        }

        return back()->with($resultat['succes'] ? 'succes' : 'erreur', $resultat['message']);
    }

    public function modifier(Request $request, Produit $produit, PromotionService $promotions): JsonResponse
    {
        $quantite = (int) $request->input('quantite', 1);
        $resultat = Panier::modifierQuantite($produit, $quantite);
        $promo = $this->recalculer($promotions);

        return response()->json([
            ...$resultat,
            'ligneTotal' => $resultat['quantite'] * (float) $produit->prix,
            'sousTotal' => $promo['sous_total'],
            'reduction' => $promo['reduction_totale'],
            'total' => $promo['total'],
            'nbPanier' => Panier::nombreArticles(),
            'estVide' => Panier::estVide(),
        ]);
    }

    public function supprimer(Produit $produit, PromotionService $promotions): JsonResponse
    {
        Panier::supprimer($produit->id);
        $promo = $this->recalculer($promotions);

        return response()->json([
            'succes' => true,
            'sousTotal' => $promo['sous_total'],
            'reduction' => $promo['reduction_totale'],
            'total' => $promo['total'],
            'nbPanier' => Panier::nombreArticles(),
            'estVide' => Panier::estVide(),
        ]);
    }

    public function appliquerCode(Request $request, PromotionService $promotions): RedirectResponse
    {
        $code = trim((string) $request->input('code', ''));

        if ($code === '') {
            return back()->with('erreur', 'Veuillez saisir un code promo.');
        }

        $lignes = Panier::lignes();

        if ($lignes->isEmpty()) {
            return back()->with('erreur', 'Votre panier est vide.');
        }

        $resultat = $promotions->evaluer(
            $lignes,
            $code,
            $promotions->contexteDepuisEmail(null, session('client_id')),
        );

        if ($resultat['erreur_code']) {
            Panier::retirerCodePromo();

            return back()->with('erreur', $resultat['erreur_code']);
        }

        Panier::definirCodePromo($code);

        return back()->with('succes', 'Code promo « '.$resultat['code_promo']->code.' » appliqué.');
    }

    public function retirerCode(): RedirectResponse
    {
        Panier::retirerCodePromo();

        return back()->with('succes', 'Code promo retiré.');
    }

    public function vider(): RedirectResponse
    {
        Panier::vider();

        return redirect()->route('panier.index')->with('succes', 'Votre panier a été vidé.');
    }

    /**
     * Recalcule les promotions après une modification du panier (le code en
     * session devenu invalide est retiré) — sans frais de livraison, choisis
     * plus tard au checkout.
     *
     * @return array<string, mixed>
     */
    private function recalculer(PromotionService $promotions): array
    {
        $promo = $promotions->evaluer(
            Panier::lignes(),
            Panier::codePromo(),
            $promotions->contexteDepuisEmail(null, session('client_id')),
        );

        if ($promo['erreur_code'] && Panier::codePromo()) {
            Panier::retirerCodePromo();
        }

        return $promo;
    }
}
