<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Un même enregistrement porte deux natures de promotion :
 * - code promo manuel (automatique = false) : saisi par le client au panier ;
 * - remise membre automatique (automatique = true) : appliquée sans code dès
 *   que les conditions (restriction_client, dates, montant min…) sont réunies.
 *
 * Toute la logique de validation et de calcul vit dans PromotionService ;
 * ce modèle n'expose que les relations, les casts et des scopes de sélection.
 */
class CodePromo extends Model
{
    use HasFactory;

    public const TYPE_POURCENTAGE = 'pourcentage';
    public const TYPE_MONTANT_FIXE = 'montant_fixe';

    public const RESTRICTION_TOUS = 'tous';
    public const RESTRICTION_INSCRITS = 'inscrits';
    public const RESTRICTION_NOUVEAUX = 'nouveaux';
    public const RESTRICTION_SELECTION = 'selection';

    protected $table = 'codes_promo';

    protected $fillable = [
        'nom',
        'code',
        'description',
        'type_reduction',
        'valeur',
        'date_debut',
        'date_fin',
        'max_utilisations',
        'max_utilisations_client',
        'montant_min',
        'livraison_gratuite',
        'restriction_client',
        'priorite',
        'cumulable',
        'automatique',
        'actif',
        // Champs historiques conservés pour compatibilité.
        'reduction',
        'date_expiration',
        'conditions',
    ];

    protected $casts = [
        'valeur' => 'decimal:2',
        'montant_min' => 'decimal:2',
        'reduction' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_expiration' => 'date',
        'max_utilisations' => 'integer',
        'max_utilisations_client' => 'integer',
        'priorite' => 'integer',
        'livraison_gratuite' => 'boolean',
        'cumulable' => 'boolean',
        'automatique' => 'boolean',
        'actif' => 'boolean',
    ];

    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produit::class, 'code_promo_produit');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Categorie::class, 'code_promo_categorie');
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'code_promo_client');
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class);
    }

    /**
     * Codes manuels saisis au panier (par opposition aux remises automatiques).
     */
    public function scopeManuels(Builder $query): Builder
    {
        return $query->where('automatique', false);
    }

    /**
     * Remises membres appliquées automatiquement, sans code.
     */
    public function scopeAutomatiques(Builder $query): Builder
    {
        return $query->where('automatique', true);
    }

    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('actif', true);
    }

    public function estPourcentage(): bool
    {
        return $this->type_reduction === self::TYPE_POURCENTAGE;
    }

    /**
     * Libellé court de la réduction (ex : « 10 % » ou « 2 500 FCFA »).
     */
    public function libelleReduction(): string
    {
        return $this->estPourcentage()
            ? rtrim(rtrim(number_format((float) $this->valeur, 2, ',', ' '), '0'), ',').' %'
            : number_format((float) $this->valeur, 0, ',', ' ').' FCFA';
    }

    /**
     * Le code est-il affichable/valable à l'instant présent (actif, dans sa
     * fenêtre de validité et sous son plafond global d'utilisation) ?
     * Sert notamment à la pop-up marketing : dès que le code expire ou atteint
     * sa limite, la pop-up cesse de l'afficher.
     */
    public function estValideMaintenant(): bool
    {
        if (! $this->actif) {
            return false;
        }

        $aujourdhui = \Illuminate\Support\Carbon::today();
        if ($this->date_debut && $aujourdhui->lt($this->date_debut)) {
            return false;
        }
        if ($this->date_fin && $aujourdhui->gt($this->date_fin)) {
            return false;
        }

        if ($this->max_utilisations !== null && $this->commandes()->count() >= $this->max_utilisations) {
            return false;
        }

        return true;
    }

    /**
     * Texte des conditions d'utilisation, dérivé de la base : description
     * saisie si présente, sinon phrase générée à partir des restrictions
     * (public visé, montant minimum, date de fin).
     */
    public function conditionsTexte(): string
    {
        if (filled($this->description)) {
            return $this->description;
        }

        $parties = [];

        $parties[] = match ($this->restriction_client) {
            self::RESTRICTION_NOUVEAUX => 'Valable pour toute première commande',
            self::RESTRICTION_INSCRITS => 'Réservé à nos clients inscrits',
            default => 'Valable sur votre commande',
        };

        if ($this->montant_min) {
            $parties[] = 'dès '.number_format((float) $this->montant_min, 0, ',', ' ').' FCFA d’achat';
        }
        if ($this->date_fin) {
            $parties[] = "jusqu'au ".$this->date_fin->format('d/m/Y');
        }

        return ucfirst(implode(', ', $parties)).'.';
    }
}
