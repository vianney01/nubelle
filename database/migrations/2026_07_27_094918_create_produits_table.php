<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->text('description_longue')->nullable();
            $table->text('composition')->nullable();
            $table->text('conseils')->nullable();
            $table->string('image_principale')->nullable();
            $table->decimal('prix', 10, 2);
            $table->decimal('ancien_prix', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('nouveaute')->default(false);
            $table->boolean('best_seller')->default(false);
            $table->boolean('stock_limite')->default(false);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
