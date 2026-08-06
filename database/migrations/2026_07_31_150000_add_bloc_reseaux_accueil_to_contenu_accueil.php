<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contenu_accueil', function (Blueprint $table) {
            // Bloc « Suivez-nous » de la page d'accueil : titres éditables et
            // mur d'images géré depuis le back-office (remplace la grille
            // Instagram codée en dur).
            $table->string('reseaux_eyebrow')->nullable()->after('instagram_url');
            $table->string('reseaux_titre')->nullable()->after('reseaux_eyebrow');
            $table->json('reseaux_images')->nullable()->after('reseaux_titre');
        });

        // Reprend les valeurs actuelles (titre + images de démonstration) pour
        // que l'affichage reste identique tant qu'aucune modification n'est faite.
        DB::table('contenu_accueil')->update([
            'reseaux_eyebrow' => '@nubellecosmetics',
            'reseaux_titre' => 'Suivez-nous sur nos réseaux sociaux',
            'reseaux_images' => json_encode([
                'produit.jpeg', 'Produit2.jpeg', 'produit3.jpeg',
                'produit4.jpeg', 'accueil.jpg', 'createur.jpg',
            ]),
        ]);
    }

    public function down(): void
    {
        Schema::table('contenu_accueil', function (Blueprint $table) {
            $table->dropColumn(['reseaux_eyebrow', 'reseaux_titre', 'reseaux_images']);
        });
    }
};
