<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Liste blanche de clients pour un code promo dont restriction_client vaut
 * « selection » : seuls ces clients (identifiés par e-mail à la caisse)
 * peuvent en bénéficier.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('code_promo_client', function (Blueprint $table) {
            $table->id();
            $table->foreignId('code_promo_id')->constrained('codes_promo')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->unique(['code_promo_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code_promo_client');
    }
};
