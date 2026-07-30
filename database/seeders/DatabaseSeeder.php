<?php

namespace Database\Seeders;

use App\Models\Avis;
use App\Models\Categorie;
use App\Models\Client;
use App\Models\CodePromo;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Peuple la base avec des données de démonstration cohérentes pour le
     * front-office ET le back-office (dashboard, widgets, resources).
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@nubelle-cosmetics.com'],
            ['name' => 'Admin Nubelle', 'password' => bcrypt('password')]
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        $categories = collect([
            ['nom' => 'Visage', 'slug' => 'visage', 'image' => 'visage.jpg', 'description' => "Sérums, crèmes et soins ciblés pour révéler l'éclat naturel de votre peau."],
            ['nom' => 'Corps', 'slug' => 'corps', 'image' => 'corp.jpg', 'description' => 'Huiles, laits et gommages pour une peau nourrie et sublimée au quotidien.'],
            ['nom' => 'Soins', 'slug' => 'soins', 'image' => 'tete.jpg', 'description' => 'Rituels de soins intensifs pensés pour chaque type de peau.'],
            ['nom' => 'Besoins', 'slug' => 'besoins', 'image' => 'pied.jpg', 'description' => 'Nos indispensables beauté : parfums, soins capillaires et essentiels.'],
        ])->mapWithKeys(fn ($c) => [$c['slug'] => Categorie::firstOrCreate(['slug' => $c['slug']], $c)]);

        $produitsDefinitions = [
            ['slug' => 'serum-eclat', 'nom' => 'Sérum éclat', 'categorie' => 'visage', 'image_principale' => 'produit.jpeg', 'description' => 'Hydrate & illumine le teint', 'prix' => 12000, 'ancien_prix' => null, 'stock' => 18, 'nouveaute' => true, 'best_seller' => false, 'stock_limite' => false],
            ['slug' => 'pack-eclat', 'nom' => 'Pack éclat', 'categorie' => 'visage', 'image_principale' => 'Produit2.jpeg', 'description' => 'Nettoie + nourrit + protège', 'prix' => 18000, 'ancien_prix' => null, 'stock' => 9, 'nouveaute' => false, 'best_seller' => true, 'stock_limite' => false],
            ['slug' => 'creme-apaisante', 'nom' => 'Crème apaisante', 'categorie' => 'visage', 'image_principale' => 'produit3.jpeg', 'description' => 'Apaise rougeurs & tiraillements', 'prix' => 15000, 'ancien_prix' => null, 'stock' => 0, 'nouveaute' => false, 'best_seller' => false, 'stock_limite' => false],
            ['slug' => 'baume-levres-caramel', 'nom' => 'Baume lèvres caramel', 'categorie' => 'visage', 'image_principale' => 'produit4.jpeg', 'description' => 'Hydrate et adoucit', 'prix' => 9000, 'ancien_prix' => null, 'stock' => 25, 'nouveaute' => false, 'best_seller' => false, 'stock_limite' => false],
            ['slug' => 'huile-corps-nourrissante', 'nom' => 'Huile corps nourrissante', 'categorie' => 'corps', 'image_principale' => 'produit3.jpeg', 'description' => 'Nourrit et sublime la peau', 'prix' => 14000, 'ancien_prix' => 19000, 'stock' => 14, 'nouveaute' => false, 'best_seller' => false, 'stock_limite' => false],
            ['slug' => 'gommage-corps-douceur', 'nom' => 'Gommage corps douceur', 'categorie' => 'corps', 'image_principale' => 'produit4.jpeg', 'description' => 'Exfolie en douceur', 'prix' => 11000, 'ancien_prix' => null, 'stock' => 20, 'nouveaute' => false, 'best_seller' => false, 'stock_limite' => false],
            ['slug' => 'lait-corps-hydratant', 'nom' => 'Lait corps hydratant', 'categorie' => 'corps', 'image_principale' => 'produit.jpeg', 'description' => 'Hydratation 48h', 'prix' => 13000, 'ancien_prix' => null, 'stock' => 30, 'nouveaute' => true, 'best_seller' => false, 'stock_limite' => false],
            ['slug' => 'serum-anti-age', 'nom' => 'Sérum anti-âge intense', 'categorie' => 'soins', 'image_principale' => 'Produit2.jpeg', 'description' => 'Repulpe et lisse les traits', 'prix' => 22000, 'ancien_prix' => 27000, 'stock' => 6, 'nouveaute' => false, 'best_seller' => true, 'stock_limite' => true],
            ['slug' => 'masque-argile-purifiant', 'nom' => 'Masque argile purifiant', 'categorie' => 'soins', 'image_principale' => 'produit3.jpeg', 'description' => 'Purifie et resserre les pores', 'prix' => 10000, 'ancien_prix' => null, 'stock' => 40, 'nouveaute' => false, 'best_seller' => false, 'stock_limite' => false],
            ['slug' => 'huile-cheveux-brillance', 'nom' => 'Huile cheveux brillance', 'categorie' => 'besoins', 'image_principale' => 'produit4.jpeg', 'description' => 'Nourrit et fait briller', 'prix' => 16000, 'ancien_prix' => null, 'stock' => 12, 'nouveaute' => true, 'best_seller' => false, 'stock_limite' => false],
            ['slug' => 'parfum-signature-nubelle', 'nom' => 'Parfum signature Nubelle', 'categorie' => 'besoins', 'image_principale' => 'parfum.png', 'description' => 'Une fragrance florale et boisée', 'prix' => 25000, 'ancien_prix' => null, 'stock' => 8, 'nouveaute' => false, 'best_seller' => true, 'stock_limite' => false],
        ];

        $produits = collect($produitsDefinitions)->map(function ($p) use ($categories) {
            $categorie = $categories[$p['categorie']];
            unset($p['categorie']);

            return Produit::firstOrCreate(
                ['slug' => $p['slug']],
                [...$p, 'categorie_id' => $categorie->id, 'actif' => true]
            );
        });

        $clientsDefinitions = [
            ['prenom' => 'Aïcha', 'nom' => 'Koné', 'email' => 'aicha.kone@example.com', 'telephone' => '+225 07 00 00 00 01', 'ville' => 'Abidjan'],
            ['prenom' => 'Kouamé', 'nom' => 'Bertrand', 'email' => 'kouame.bertrand@example.com', 'telephone' => '+225 07 00 00 00 02', 'ville' => 'Abidjan'],
            ['prenom' => 'Rachel', 'nom' => 'Yao', 'email' => 'rachel.yao@example.com', 'telephone' => '+225 07 00 00 00 03', 'ville' => 'Bouaké'],
            ['prenom' => 'Guy', 'nom' => 'Kacou', 'email' => 'guy.kacou@example.com', 'telephone' => '+225 07 00 00 00 04', 'ville' => 'Bouaké'],
            ['prenom' => 'Fatou', 'nom' => 'Diabaté', 'email' => 'fatou.diabate@example.com', 'telephone' => '+225 07 00 00 00 05', 'ville' => 'Yamoussoukro'],
        ];

        $clients = collect($clientsDefinitions)->map(fn ($c) => Client::firstOrCreate(['email' => $c['email']], $c));

        if (Commande::count() === 0) {
            $statuts = ['livree', 'livree', 'expediee', 'en_attente', 'livree', 'annulee'];

            foreach (range(1, 18) as $i) {
                $client = $clients->random();
                $date = Carbon::now()->subDays(random_int(0, 150));
                $lignesDef = $produits->random(random_int(1, 3));

                $commande = Commande::create([
                    'numero' => 'NB-'.str_pad((string) (10200 + $i), 5, '0', STR_PAD_LEFT),
                    'client_id' => $client->id,
                    'statut' => $statuts[array_rand($statuts)],
                    'total' => 0,
                    'mode_paiement' => ['carte', 'mobile_money', 'livraison'][array_rand(['carte', 'mobile_money', 'livraison'])],
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $total = 0;
                foreach ($lignesDef as $produit) {
                    $quantite = random_int(1, 3);
                    LigneCommande::create([
                        'commande_id' => $commande->id,
                        'produit_id' => $produit->id,
                        'quantite' => $quantite,
                        'prix_unitaire' => $produit->prix,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                    $total += $quantite * $produit->prix;
                }

                $commande->update(['total' => $total]);
            }
        }

        CodePromo::firstOrCreate(['code' => 'NUBELLE10'], [
            'reduction' => 10,
            'date_expiration' => Carbon::now()->addMonths(2),
            'conditions' => 'Valable dès 15 000 FCFA d\'achat.',
            'actif' => true,
        ]);
        CodePromo::firstOrCreate(['code' => 'BIENVENUE'], [
            'reduction' => 15,
            'date_expiration' => Carbon::now()->addMonth(),
            'conditions' => 'Réservé au premier achat.',
            'actif' => true,
        ]);
        CodePromo::firstOrCreate(['code' => 'SOLDES2025'], [
            'reduction' => 20,
            'date_expiration' => Carbon::now()->subMonth(),
            'conditions' => 'Offre soldes expirée.',
            'actif' => false,
        ]);

        if (Avis::count() === 0) {
            $avisTextes = [
                ['note' => 5, 'message' => 'Les produits Nubelle sont incroyables, je suis ravi de mon achat.'],
                ['note' => 4, 'message' => 'Très satisfaite de ma crème hydratante, merci pour votre réactivité.'],
                ['note' => 3, 'message' => 'Merci pour la livraison rapide depuis Bouaké, je commanderai encore.'],
                ['note' => 5, 'message' => "J'adore ce produit ! Ma peau est éclatante."],
                ['note' => 4, 'message' => 'Très satisfaite, je recommande à 100%.'],
            ];

            foreach ($avisTextes as $a) {
                Avis::create([
                    'client_id' => $clients->random()->id,
                    'produit_id' => $produits->random()->id,
                    'note' => $a['note'],
                    'message' => $a['message'],
                    'visible' => true,
                ]);
            }
        }

        if (MouvementStock::count() === 0) {
            foreach ($produits->take(6) as $produit) {
                MouvementStock::create([
                    'produit_id' => $produit->id,
                    'type' => 'entree',
                    'quantite' => random_int(10, 40),
                    'motif' => 'Réassort fournisseur',
                ]);
                MouvementStock::create([
                    'produit_id' => $produit->id,
                    'type' => 'sortie',
                    'quantite' => random_int(2, 15),
                    'motif' => 'Ventes en ligne',
                ]);
            }
        }

        $this->command?->info("Compte admin : {$admin->email} / password");
    }
}
