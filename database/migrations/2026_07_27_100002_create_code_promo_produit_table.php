<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Restriction d'un code promo à des produits précis. Sans ligne ici, le code
 * s'applique à tout le panier (sous réserve des autres restrictions).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('code_promo_produit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('code_promo_id')->constrained('codes_promo')->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->unique(['code_promo_id', 'produit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code_promo_produit');
    }
};
