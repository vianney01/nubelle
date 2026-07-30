<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Étend le système de codes promo pour couvrir : type de réduction
 * (pourcentage / montant fixe), fenêtre de validité, plafonds d'utilisation
 * (global + par client), montant minimum de panier, livraison gratuite,
 * restriction client, priorité, cumul, et remises membres automatiques
 * (sans code). Le même modèle CodePromo porte les deux natures (manuel /
 * automatique) via le drapeau « automatique ».
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('codes_promo', function (Blueprint $table) {
            $table->string('nom')->nullable()->after('id');
            $table->string('code')->nullable()->change();
            $table->text('description')->nullable()->after('code');

            $table->string('type_reduction')->default('pourcentage')->after('description');
            $table->decimal('valeur', 10, 2)->default(0)->after('type_reduction');

            $table->date('date_debut')->nullable()->after('valeur');
            $table->date('date_fin')->nullable()->after('date_debut');

            $table->unsignedInteger('max_utilisations')->nullable()->after('date_fin');
            $table->unsignedInteger('max_utilisations_client')->nullable()->after('max_utilisations');
            $table->decimal('montant_min', 10, 2)->nullable()->after('max_utilisations_client');
            $table->boolean('livraison_gratuite')->default(false)->after('montant_min');

            $table->string('restriction_client')->default('tous')->after('livraison_gratuite');

            $table->integer('priorite')->default(0)->after('restriction_client');
            $table->boolean('cumulable')->default(false)->after('priorite');
            $table->boolean('automatique')->default(false)->after('cumulable');
        });

        // Reprise des données existantes : l'ancienne colonne « reduction »
        // (pourcentage) alimente valeur/type, et « date_expiration » la date de fin.
        DB::table('codes_promo')->update([
            'valeur' => DB::raw('reduction'),
            'type_reduction' => 'pourcentage',
            'date_fin' => DB::raw('date_expiration'),
        ]);
    }

    public function down(): void
    {
        Schema::table('codes_promo', function (Blueprint $table) {
            $table->dropColumn([
                'nom', 'description', 'type_reduction', 'valeur',
                'date_debut', 'date_fin', 'max_utilisations', 'max_utilisations_client',
                'montant_min', 'livraison_gratuite', 'restriction_client',
                'priorite', 'cumulable', 'automatique',
            ]);
        });
    }
};
