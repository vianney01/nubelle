<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Commune de livraison et son tarif (mode « livraison normale »). Gérée depuis
 * le back-office ; le client choisit sa commune au checkout et le prix affilié
 * s'affiche automatiquement.
 */
class Commune extends Model
{
    protected $table = 'communes';

    protected $fillable = ['nom', 'prix', 'actif'];

    protected $casts = [
        'prix' => 'decimal:2',
        'actif' => 'boolean',
    ];

    public function scopeActives(Builder $query): Builder
    {
        return $query->where('actif', true);
    }
}
