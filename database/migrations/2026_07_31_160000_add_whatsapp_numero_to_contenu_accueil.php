<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contenu_accueil', function (Blueprint $table) {
            // Numéro WhatsApp de la boutique (forme canonique +225XXXXXXXXXX),
            // utilisé par le bouton flottant, le menu latéral et la page Contact.
            $table->string('whatsapp_numero')->nullable()->after('instagram_url');
        });

        DB::table('contenu_accueil')->update([
            'whatsapp_numero' => '+2250700000000',
        ]);
    }

    public function down(): void
    {
        Schema::table('contenu_accueil', function (Blueprint $table) {
            $table->dropColumn('whatsapp_numero');
        });
    }
};
