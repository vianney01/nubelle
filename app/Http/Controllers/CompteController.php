<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Commande;
use App\Models\Produit;

class CompteController extends Controller
{
    public function index()
    {
        $client = ($clientId = session('client_id')) ? Client::find($clientId) : null;

        $commandes = $client
            ? Commande::where('client_id', $client->id)->with('lignes')->latest()->get()
            : collect();

        // Adresse déduite du dernier passage en caisse du client. Il n'existe
        // pas (encore) de table d'adresses multiples dédiée : on présente la
        // seule adresse enregistrée sur la fiche client.
        $adresses = ($client && filled($client->adresse))
            ? [[
                'libelle' => 'Adresse de livraison',
                'nom' => $client->nomComplet(),
                'details' => trim("{$client->adresse}, {$client->ville} ".($client->code_postal ?? '')),
                'defaut' => true,
            ]]
            : [];

        // Favoris : pas de table dédiée -> suggestions réelles de la boutique
        // (produits actifs récents) plutôt que des données statiques.
        $favoris = Produit::query()
            ->actifs()
            ->avecAvis()
            ->with('categorie')
            ->latest()
            ->take(4)
            ->get();

        return view('compte.index', [
            'client' => $client,
            'commandes' => $commandes,
            'adresses' => $adresses,
            'favoris' => $favoris,
        ]);
    }
}
