<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La colonne historique « reduction » (pourcentage) est désormais remplacée
 * par le couple type_reduction / valeur. On la rend nullable pour ne plus
 * bloquer la création de promotions (notamment les remises automatiques et
 * les réductions en montant fixe).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('codes_promo', function (Blueprint $table) {
            $table->decimal('reduction', 5, 2)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('codes_promo', function (Blueprint $table) {
            $table->decimal('reduction', 5, 2)->nullable(false)->default(0)->change();
        });
    }
};
