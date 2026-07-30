<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Suivi du paiement d'une commande, distinct de son statut logistique :
 * statut_paiement (en_attente / paye / rembourse) et référence de
 * transaction saisie lors de la confirmation du paiement.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('statut_paiement')->default('en_attente')->after('statut');
            $table->string('reference_paiement')->nullable()->after('mode_paiement');
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn(['statut_paiement', 'reference_paiement']);
        });
    }
};
