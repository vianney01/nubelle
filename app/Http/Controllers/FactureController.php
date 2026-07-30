<?php

namespace App\Http\Controllers;

use App\Models\Commande;

class FactureController extends Controller
{
    /**
     * Facture imprimable d'une commande (back-office). La page est optimisée
     * pour l'impression / l'export PDF via le navigateur (Ctrl+P → PDF),
     * ce qui évite d'introduire une dépendance PDF côté serveur.
     * Accès réservé aux administrateurs authentifiés (middleware auth).
     */
    public function show(Commande $commande)
    {
        $commande->load(['lignes.produit', 'client', 'codePromo']);

        return view('admin.facture', ['commande' => $commande]);
    }
}
