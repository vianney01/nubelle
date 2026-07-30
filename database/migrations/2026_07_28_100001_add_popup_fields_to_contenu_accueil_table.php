<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pop-up marketing d'acquisition : contenu éditable + code promo de bienvenue
 * sélectionné dans le module Promotions (référence, pas de duplication) +
 * ciblage de l'audience.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contenu_accueil', function (Blueprint $table) {
            $table->boolean('popup_actif')->default(false);
            $table->string('popup_image')->nullable();
            $table->string('popup_badge')->nullable();
            $table->string('popup_titre')->nullable();
            $table->string('popup_sous_titre')->nullable();
            $table->foreignId('popup_code_promo_id')->nullable()->constrained('codes_promo')->nullOnDelete();
            $table->string('popup_bouton_texte')->nullable();
            $table->string('popup_bouton_lien')->nullable();
            $table->string('popup_cible')->default('non_connectes');
        });

        // Valeurs par défaut sur l'enregistrement unique existant.
        DB::table('contenu_accueil')->update([
            'popup_badge' => 'Offre de bienvenue',
            'popup_titre' => 'Bienvenue chez Nubelle',
            'popup_sous_titre' => 'Profitez d’un avantage sur votre première commande.',
            'popup_bouton_texte' => 'Créer mon compte',
            'popup_bouton_lien' => '/connexion',
            'popup_cible' => 'non_connectes',
        ]);
    }

    public function down(): void
    {
        Schema::table('contenu_accueil', function (Blueprint $table) {
            $table->dropConstrainedForeignId('popup_code_promo_id');
            $table->dropColumn([
                'popup_actif', 'popup_image', 'popup_badge', 'popup_titre',
                'popup_sous_titre', 'popup_bouton_texte', 'popup_bouton_lien', 'popup_cible',
            ]);
        });
    }
};
