<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Journal des événements d'une commande (timeline) : création, paiement,
 * préparation, expédition, livraison, annulation, remboursement, notes.
 * Chaque entrée mémorise l'auteur (utilisateur admin ou null si action
 * client/système) et un commentaire éventuel.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique_commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('statut');
            $table->string('statut')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });

        // Événement « création » rétroactif pour les commandes déjà existantes,
        // afin que leur timeline ne soit pas vide.
        $now = now();
        DB::table('commandes')->orderBy('id')->select('id', 'created_at')->chunkById(200, function ($commandes) use ($now) {
            $lignes = [];
            foreach ($commandes as $commande) {
                $lignes[] = [
                    'commande_id' => $commande->id,
                    'user_id' => null,
                    'type' => 'creation',
                    'statut' => 'en_attente',
                    'commentaire' => 'Commande créée.',
                    'created_at' => $commande->created_at ?? $now,
                    'updated_at' => $commande->created_at ?? $now,
                ];
            }
            if ($lignes) {
                DB::table('historique_commandes')->insert($lignes);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_commandes');
    }
};
