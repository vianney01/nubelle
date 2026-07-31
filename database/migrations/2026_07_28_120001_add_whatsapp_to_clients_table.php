<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Copie du numéro WhatsApp sur la fiche client (carnet d'adresses), renseignée
 * lors du passage en caisse à partir du compte : permet au service client de
 * contacter rapidement l'acheteur depuis le back-office (liste clients,
 * fiche client, fiche commande).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('whatsapp')->nullable()->after('telephone');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('whatsapp');
        });
    }
};
