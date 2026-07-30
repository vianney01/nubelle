<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Une entrée de la timeline d'une commande. Le libellé et l'icône affichés
 * découlent du champ « type » (voir les tableaux ci-dessous), réutilisés par
 * la fiche commande Filament et la facture.
 */
class HistoriqueCommande extends Model
{
    protected $table = 'historique_commandes';

    protected $fillable = [
        'commande_id',
        'user_id',
        'type',
        'statut',
        'commentaire',
    ];

    public const LIBELLES = [
        'creation' => 'Commande créée',
        'paiement' => 'Paiement reçu',
        'preparation' => 'En préparation',
        'expedition' => 'Commande expédiée',
        'livraison' => 'Commande livrée',
        'annulation' => 'Commande annulée',
        'remboursement' => 'Commande remboursée',
        'statut' => 'Statut mis à jour',
        'note' => 'Note interne',
    ];

    public const ICONES = [
        'creation' => 'heroicon-o-shopping-bag',
        'paiement' => 'heroicon-o-banknotes',
        'preparation' => 'heroicon-o-cube',
        'expedition' => 'heroicon-o-truck',
        'livraison' => 'heroicon-o-check-badge',
        'annulation' => 'heroicon-o-x-circle',
        'remboursement' => 'heroicon-o-arrow-uturn-left',
        'statut' => 'heroicon-o-arrow-path',
        'note' => 'heroicon-o-chat-bubble-left-ellipsis',
    ];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function libelle(): string
    {
        return self::LIBELLES[$this->type] ?? ucfirst($this->type);
    }

    public function icone(): string
    {
        return self::ICONES[$this->type] ?? 'heroicon-o-clock';
    }
}
