<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avis extends Model
{
    use HasFactory;

    protected $table = 'avis';

    protected $fillable = [
        'client_id',
        'produit_id',
        'note',
        'message',
        'visible',
        'reponse_admin',
    ];

    protected $casts = [
        'note' => 'integer',
        'visible' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }

    /**
     * Alias vers "message", pour rester compatible avec les vues qui
     * attendaient jusqu'ici une clé "texte" (données de démonstration).
     * Pensez à charger la relation "client" en amont (with('client'))
     * pour éviter une requête N+1.
     */
    protected function texte(): Attribute
    {
        return Attribute::get(fn () => $this->message);
    }

    /**
     * Nom complet du client à l'origine de l'avis, avec repli si l'avis
     * n'est pas rattaché à un compte client.
     */
    protected function auteur(): Attribute
    {
        return Attribute::get(fn () => $this->client?->nomComplet() ?? 'Client Nubelle');
    }

    /**
     * Photo associée à l'avis : l'URL résolue de l'image du produit concerné
     * (via l'accessor Produit::image, qui gère uploads et fichiers de démo),
     * faute d'un champ image dédié sur les avis. Pensez à charger la relation
     * "produit" en amont (with('produit')) pour éviter une requête N+1.
     */
    protected function image(): Attribute
    {
        return Attribute::get(fn () => $this->produit?->image);
    }
}
