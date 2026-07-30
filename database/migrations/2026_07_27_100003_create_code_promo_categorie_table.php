<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Restriction d'un code promo à des catégories entières. Cumulable avec la
 * restriction par produit : un produit est éligible s'il figure dans l'une
 * OU l'autre liste (quand au moins une restriction est définie).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('code_promo_categorie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('code_promo_id')->constrained('codes_promo')->cascadeOnDelete();
            $table->foreignId('categorie_id')->constrained('categories')->cascadeOnDelete();
            $table->unique(['code_promo_id', 'categorie_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code_promo_categorie');
    }
};
