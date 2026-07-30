<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Models\Categorie;
use App\Models\Commande;
use App\Models\ContenuAccueil;
use App\Models\Produit;
use App\Support\CatalogueDemo;

class HomeController extends Controller
{
    /**
     * Page d'accueil.
     *
     * Catégories, produits et avis proviennent désormais de la base de
     * données. La FAQ reste sur CatalogueDemo en attendant un module dédié
     * (aucune table dédiée pour l'instant).
     */
    public function index()
    {
        $categories = Categorie::query()
            ->withCount('produits')
            ->orderBy('nom')
            ->get();

        $requeteProduits = fn () => Produit::query()->actifs()->avecAvis();

        $produits = $requeteProduits()->latest()->take(8)->get();
        $nouveautes = $requeteProduits()->where('nouveaute', true)->latest()->take(4)->get();
        $bestSellers = $requeteProduits()->where('best_seller', true)->latest()->take(4)->get();
        $promotions = $requeteProduits()->whereNotNull('ancien_prix')->latest()->take(4)->get();

        $avis = Avis::query()
            ->where('visible', true)
            ->with(['client', 'produit'])
            ->latest()
            ->take(3)
            ->get();

        // Pop-up marketing : visible selon l'audience ciblée et la validité du
        // code promo de bienvenue (récupéré depuis le module Promotions).
        $accueil = ContenuAccueil::instance()->load('codePromoPopup');
        $clientId = session('client_id');
        $aCommande = $clientId ? Commande::where('client_id', $clientId)->exists() : false;

        $promoPopup = $accueil->codePromoPopup;
        $popupVisible = $accueil->popupVisiblePour($clientId, $aCommande);
        if ($popupVisible && $promoPopup && ! $promoPopup->estValideMaintenant()) {
            $popupVisible = false; // offre expirée / épuisée -> ne plus afficher
        }

        return view('home', [
            'accueil' => $accueil,
            'popupVisible' => $popupVisible,
            'promoPopup' => $popupVisible ? $promoPopup : null,
            'categories' => $categories,
            'produits' => $produits,
            'nouveautes' => $nouveautes,
            'bestSellers' => $bestSellers,
            'promotions' => $promotions,
            'avis' => $avis,
            'faqs' => array_slice(array_merge(...array_values(CatalogueDemo::faqsGroupees())), 0, 3),
        ]);
    }
}
