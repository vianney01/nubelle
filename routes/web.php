<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\ProduitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes NUBELLE Cosmetics — front-office
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Fichiers uploadés (disque "public") — voir MediaController pour le pourquoi
Route::get('/media/{path}', [MediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.show');

// Catalogue
Route::get('/produits', [ProduitController::class, 'index'])->name('produits.index');
Route::get('/produit/{slug}', [ProduitController::class, 'show'])->name('produits.show');
Route::get('/categorie/{slug}', [ProduitController::class, 'categorie'])->name('categorie.show');
Route::get('/marque/{slug}', [ProduitController::class, 'marque'])->name('marque.show');
Route::get('/recherche', [ProduitController::class, 'recherche'])->name('recherche');
Route::get('/recherche/suggestions', [ProduitController::class, 'suggestions'])->name('recherche.suggestions');

// Panier & commande
Route::get('/panier', [PanierController::class, 'index'])->name('panier.index');
Route::post('/panier/ajouter/{produit:slug}', [PanierController::class, 'ajouter'])->name('panier.ajouter');

// Code promo (déclaré avant les routes {produit:slug} pour éviter toute collision)
Route::post('/panier/code-promo', [PanierController::class, 'appliquerCode'])->name('panier.code.appliquer');
Route::delete('/panier/code-promo', [PanierController::class, 'retirerCode'])->name('panier.code.retirer');

Route::patch('/panier/{produit:slug}', [PanierController::class, 'modifier'])->name('panier.modifier');
Route::delete('/panier/{produit:slug}', [PanierController::class, 'supprimer'])->name('panier.supprimer');
Route::delete('/panier', [PanierController::class, 'vider'])->name('panier.vider');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'valider'])->name('checkout.valider');
Route::get('/checkout/confirmation/{commande:numero}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

// Compte
Route::get('/connexion', [AuthController::class, 'login'])->name('connexion');
Route::get('/compte', [CompteController::class, 'index'])->name('compte.index');

// Facture imprimable (back-office — administrateurs authentifiés)
Route::get('/admin/commandes/{commande}/facture', [FactureController::class, 'show'])
    ->middleware('auth')
    ->name('admin.commande.facture');

// Pages d'information
Route::get('/a-propos', [PageController::class, 'apropos'])->name('pages.apropos');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::get('/faq', [PageController::class, 'faq'])->name('pages.faq');
Route::get('/confidentialite', [PageController::class, 'confidentialite'])->name('pages.confidentialite');
Route::get('/conditions', [PageController::class, 'conditions'])->name('pages.conditions');
Route::get('/livraison', [PageController::class, 'livraison'])->name('pages.livraison');
Route::get('/retours', [PageController::class, 'retours'])->name('pages.retours');
