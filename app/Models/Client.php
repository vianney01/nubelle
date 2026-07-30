<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'telephone',
        'adresse',
        'ville',
        'code_postal',
    ];

    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class);
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class);
    }

    public function nomComplet(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }
}
