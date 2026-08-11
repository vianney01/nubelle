<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            // Mode de livraison choisi : express | normale | expedition.
            // Toutes les commandes sont ensuite finalisées sur WhatsApp.
            $table->string('mode_livraison')->nullable()->after('frais_livraison');
            // Commune (nom figé au moment de la commande) pour la livraison normale.
            $table->string('commune')->nullable()->after('mode_livraison');
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn(['mode_livraison', 'commune']);
        });
    }
};
