<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Commande extends Model
{
    use HasFactory;

    public const STATUTS = ['en_attente', 'en_preparation', 'expediee', 'livree', 'annulee'];

    public const STATUTS_LABELS = [
        'en_attente' => 'En attente',
        'en_preparation' => 'En préparation',
        'expediee' => 'Expédiée',
        'livree' => 'Livrée',
        'annulee' => 'Annulée',
    ];

    public const STATUTS_PAIEMENT_LABELS = [
        'en_attente' => 'En attente',
        'paye' => 'Payé',
        'rembourse' => 'Remboursé',
    ];

    protected $table = 'commandes';

    protected $fillable = [
        'numero',
        'client_id',
        'user_id',
        'code_promo_id',
        'statut',
        'statut_paiement',
        'total',
        'total_avant_remise',
        'reduction_montant',
        'reduction_type',
        'remise_membre',
        'frais_livraison',
        'adresse_livraison',
        'mode_paiement',
        'reference_paiement',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'total_avant_remise' => 'decimal:2',
        'reduction_montant' => 'decimal:2',
        'remise_membre' => 'decimal:2',
        'frais_livraison' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function codePromo(): BelongsTo
    {
        return $this->belongsTo(CodePromo::class, 'code_promo_id');
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(HistoriqueCommande::class)->oldest();
    }

    /**
     * Ajoute un événement à la timeline de la commande. L'auteur est
     * l'utilisateur admin connecté par défaut (null pour une action
     * client/système, ex. passage de commande depuis la boutique).
     */
    public function journaliser(string $type, ?string $statut = null, ?string $commentaire = null, bool $auteurConnecte = true): HistoriqueCommande
    {
        return $this->historiques()->create([
            'user_id' => $auteurConnecte ? Auth::id() : null,
            'type' => $type,
            'statut' => $statut,
            'commentaire' => $commentaire,
        ]);
    }

    public function statutLabel(): string
    {
        return self::STATUTS_LABELS[$this->statut] ?? $this->statut;
    }

    public function statutPaiementLabel(): string
    {
        return self::STATUTS_PAIEMENT_LABELS[$this->statut_paiement] ?? $this->statut_paiement;
    }

    /**
     * Méthode de livraison déduite des frais (aucune colonne dédiée) :
     * gratuite = point relais, payante = express.
     */
    public function methodeLivraison(): string
    {
        return (float) $this->frais_livraison > 0 ? 'Livraison express (24h)' : 'Point relais (24–72h)';
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneCommande::class);
    }
}
