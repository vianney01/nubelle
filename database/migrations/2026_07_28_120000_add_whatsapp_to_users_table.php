<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Numéro WhatsApp du client, obligatoire à l'inscription et unique.
 * La colonne est nullable en base pour ne pas invalider les comptes
 * administrateurs existants (créés avant WhatsApp) ; l'obligation est
 * appliquée à l'inscription/mise à jour côté application. MySQL autorise
 * plusieurs NULL sous un index unique.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp')->nullable()->unique()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['whatsapp']);
            $table->dropColumn('whatsapp');
        });
    }
};
