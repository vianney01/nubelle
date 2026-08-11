<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->decimal('prix', 10, 2)->default(0); // frais de « livraison normale »
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Exemples de communes d'Abidjan (prix à ajuster dans le back-office).
        $maintenant = now();
        $exemples = [
            'Cocody' => 1500, 'Yopougon' => 2000, 'Abobo' => 2000, 'Adjamé' => 1000,
            'Plateau' => 1000, 'Treichville' => 1000, 'Marcory' => 1500, 'Koumassi' => 1500,
            'Port-Bouët' => 2000, 'Attécoubé' => 1500, 'Bingerville' => 2500, 'Anyama' => 2500,
        ];

        DB::table('communes')->insert(array_map(
            fn (string $nom, int $prix) => [
                'nom' => $nom,
                'prix' => $prix,
                'actif' => true,
                'created_at' => $maintenant,
                'updated_at' => $maintenant,
            ],
            array_keys($exemples),
            array_values($exemples),
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('communes');
    }
};
