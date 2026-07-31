<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            // Images supplémentaires du produit (galerie). L'image de couverture
            // reste dans `image_principale` ; ceci ne contient que les visuels
            // additionnels, stockés comme un tableau JSON de chemins.
            $table->json('galerie')->nullable()->after('image_principale');
        });
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn('galerie');
        });
    }
};
