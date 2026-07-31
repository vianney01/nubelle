<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Toute nouvelle commande est obligatoirement rattachée à un compte client
 * (user_id). La colonne est nullable pour ne pas invalider d'éventuelles
 * anciennes commandes invité ; le tunnel d'achat, lui, exige désormais un
 * utilisateur connecté et renseigne toujours user_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('client_id')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
