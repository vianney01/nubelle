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
            // Liens des réseaux sociaux, gérés depuis le back-office et affichés
            // dans le pied de page + le menu latéral. TikTok en premier (réseau
            // principal de la marque). Une URL vide masque l'icône.
            $table->string('tiktok_url')->nullable()->after('popup_cible');
            $table->string('facebook_url')->nullable()->after('tiktok_url');
            $table->string('instagram_url')->nullable()->after('facebook_url');
        });

        // Pré-remplit l'enregistrement unique existant avec des valeurs de
        // départ (modifiables dans l'admin) pour que le footer ne soit pas vide.
        DB::table('contenu_accueil')->update([
            'tiktok_url' => 'https://www.tiktok.com/@nubellecosmetics',
            'facebook_url' => 'https://www.facebook.com/nubellecosmetics',
            'instagram_url' => 'https://www.instagram.com/nubellecosmetics',
        ]);
    }

    public function down(): void
    {
        Schema::table('contenu_accueil', function (Blueprint $table) {
            $table->dropColumn(['tiktok_url', 'facebook_url', 'instagram_url']);
        });
    }
};
