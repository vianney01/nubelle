<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trace complète de la promotion appliquée à une commande (section 8) :
 * code utilisé, type et montant de réduction, remise membre automatique,
 * total avant/après remise et frais de livraison. « total » (déjà présent)
 * reste le total final effectivement payé.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->foreignId('code_promo_id')->nullable()->after('client_id')
                ->constrained('codes_promo')->nullOnDelete();
            $table->decimal('total_avant_remise', 10, 2)->default(0)->after('total');
            $table->decimal('reduction_montant', 10, 2)->default(0)->after('total_avant_remise');
            $table->string('reduction_type')->nullable()->after('reduction_montant');
            $table->decimal('remise_membre', 10, 2)->default(0)->after('reduction_type');
            $table->decimal('frais_livraison', 10, 2)->default(0)->after('remise_membre');
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('code_promo_id');
            $table->dropColumn([
                'total_avant_remise', 'reduction_montant', 'reduction_type',
                'remise_membre', 'frais_livraison',
            ]);
        });
    }
};
