<?php

use App\Models\ContenuAccueil;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Contenus éditables de la page d'accueil (table à enregistrement unique).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contenu_accueil', function (Blueprint $table) {
            $table->id();

            // Hero
            $table->string('hero_image')->nullable();
            $table->string('hero_sous_titre')->nullable();
            $table->string('hero_titre')->nullable();
            $table->string('hero_bouton_texte')->nullable();
            $table->string('hero_bouton_lien')->nullable();

            // Pourquoi choisir Nubelle (+ bloc image/bouton attenant)
            $table->string('pourquoi_image')->nullable();
            $table->string('pourquoi_eyebrow')->nullable();
            $table->string('pourquoi_titre')->nullable();
            $table->string('pourquoi_bouton_texte')->nullable();
            $table->string('pourquoi_bouton_lien')->nullable();

            // À propos de Nubelle
            $table->string('apropos_image')->nullable();
            $table->string('apropos_sous_titre')->nullable();
            $table->string('apropos_titre')->nullable();
            $table->text('apropos_texte')->nullable();
            $table->string('apropos_bouton_texte')->nullable();
            $table->string('apropos_bouton_lien')->nullable();

            $table->timestamps();
        });

        // Enregistrement initial : reprend le contenu jusqu'ici codé en dur.
        DB::table('contenu_accueil')->insert(array_merge(
            ContenuAccueil::DEFAUTS,
            ['created_at' => now(), 'updated_at' => now()],
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('contenu_accueil');
    }
};
