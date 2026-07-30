<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produit extends Model
{
    use HasFactory;

    protected $table = 'produits';

    protected $fillable = [
        'categorie_id',
        'nom',
        'slug',
        'description',
        'description_longue',
        'composition',
        'conseils',
        'image_principale',
        'prix',
        'ancien_prix',
        'stock',
        'nouveaute',
        'best_seller',
        'stock_limite',
        'actif',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'ancien_prix' => 'decimal:2',
        'stock' => 'integer',
        'nouveaute' => 'boolean',
        'best_seller' => 'boolean',
        'stock_limite' => 'boolean',
        'actif' => 'boolean',
    ];

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function lignesCommande(): HasMany
    {
        return $this->hasMany(LigneCommande::class);
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class);
    }

    public function mouvementsStock(): HasMany
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function enRupture(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Ne garder que les produits publiés côté front-office.
     */
    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('actif', true);
    }

    /**
     * Précharge la note moyenne et le nombre d'avis visibles (avis_avg_note /
     * avis_count) en une seule requête agrégée, pour éviter le N+1 quand on
     * affiche des grilles de produits avec étoiles.
     */
    public function scopeAvecAvis(Builder $query): Builder
    {
        return $query
            ->withAvg(['avis' => fn (Builder $q) => $q->where('visible', true)], 'note')
            ->withCount(['avis' => fn (Builder $q) => $q->where('visible', true)]);
    }

    /**
     * URL utilisable de l'image principale — gère à la fois :
     * - les anciens noms de fichiers de démonstration (ex: "produit.jpeg"),
     *   stockés directement dans public/images/ ;
     * - les fichiers réellement uploadés depuis Filament (ex: "produits/abc123.png"),
     *   servis via MediaController plutôt que via le lien symbolique
     *   public/storage (sur Windows, `storage:link` crée une Junction que le
     *   serveur de développement PHP intégré sert mal — 403 Forbidden).
     */
    protected function image(): Attribute
    {
        return Attribute::get(function () {
            if (blank($this->image_principale)) {
                return null;
            }

            return str_contains($this->image_principale, '/')
                ? route('media.show', ['path' => $this->image_principale])
                : asset('images/'.$this->image_principale);
        });
    }

    /**
     * Étiquette calculée à partir des données réelles (stock, promotion,
     * meilleure vente, nouveauté) — remplace le champ "badge" fixe utilisé
     * par les anciennes données de démonstration.
     */
    protected function badge(): Attribute
    {
        return Attribute::get(function () {
            if ($this->stock <= 0) {
                return 'rupture';
            }
            if (! is_null($this->ancien_prix)) {
                return 'promo';
            }
            if ($this->best_seller) {
                return 'meilleur vendeur';
            }
            if ($this->nouveaute) {
                return 'nouveau';
            }

            return null;
        });
    }

    /**
     * Note moyenne arrondie (0 à 5), calculée depuis les avis visibles
     * lorsqu'ils ont été préchargés via le scope avecAvis().
     */
    protected function etoiles(): Attribute
    {
        return Attribute::get(function () {
            if (! array_key_exists('avis_avg_note', $this->attributes)) {
                return 0;
            }

            return (int) round((float) $this->attributes['avis_avg_note']);
        });
    }
}
