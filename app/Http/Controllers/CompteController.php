<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Commande;
use App\Models\Produit;
use App\Models\User;
use App\Support\Whatsapp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CompteController extends Controller
{
    public function index(Request $request)
    {
        // Page protégée par le middleware auth : l'utilisateur est toujours là.
        $user = $request->user();

        // Fiche client (carnet d'adresses / profil), rattachée à l'e-mail du
        // compte ; peut être absente tant qu'aucune commande n'a été passée.
        $client = Client::firstWhere('email', $user->email);

        // Commandes du compte connecté (identifiées par user_id).
        $commandes = Commande::where('user_id', $user->id)
            ->with('lignes')
            ->latest()
            ->get();

        $adresses = ($client && filled($client->adresse))
            ? [[
                'libelle' => 'Adresse de livraison',
                'nom' => $client->nomComplet(),
                'details' => trim("{$client->adresse}, {$client->ville} ".($client->code_postal ?? '')),
                'defaut' => true,
            ]]
            : [];

        // Favoris : pas de table dédiée -> suggestions réelles de la boutique.
        $favoris = Produit::query()
            ->actifs()
            ->avecAvis()
            ->with('categorie')
            ->latest()
            ->take(4)
            ->get();

        return view('compte.index', [
            'user' => $user,
            'client' => $client,
            'commandes' => $commandes,
            'adresses' => $adresses,
            'favoris' => $favoris,
        ]);
    }

    /**
     * Mise à jour des informations du compte (nom + numéro WhatsApp).
     */
    public function updateProfil(Request $request): RedirectResponse
    {
        $user = $request->user();

        $donnees = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'whatsapp' => ['required', 'string', 'max:30'],
        ]);

        $whatsapp = Whatsapp::normaliser($donnees['whatsapp']);
        if ($whatsapp === null) {
            throw ValidationException::withMessages([
                'whatsapp' => 'Numéro WhatsApp invalide. Exemple : 0556400246 ou +2250556400246.',
            ]);
        }

        // Unicité du numéro normalisé, en s'ignorant soi-même.
        if (User::where('whatsapp', $whatsapp)->where('id', '!=', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'whatsapp' => 'Ce numéro WhatsApp est déjà associé à un autre compte.',
            ]);
        }

        $user->update(['name' => $donnees['name'], 'whatsapp' => $whatsapp]);

        // Propager sur la fiche client (carnet d'adresses) si elle existe.
        Client::where('email', $user->email)->update(['whatsapp' => $whatsapp]);

        return redirect()->route('compte.index')->with('succes', 'Vos informations ont été mises à jour.');
    }
}
