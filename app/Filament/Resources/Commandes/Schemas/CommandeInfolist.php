<?php

namespace App\Filament\Resources\Commandes\Schemas;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\Commande;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

/**
 * Fiche commande en grille 2 colonnes uniforme (d'après la maquette fournie) :
 *
 *   ┌────────────────┬────────────────────┐
 *   │ Récapitulatif  │ Produits commandés │
 *   │ Client         │ Résumé financier   │
 *   │ Livraison      │ Paiement           │
 *   │ Historique     │ Notes internes     │
 *   └────────────────┴────────────────────┘
 *
 * Colonne de gauche : récap, client, livraison, historique.
 * Colonne de droite : produits, résumé financier, paiement, notes.
 * Sur tablette/mobile, les cartes s'empilent sur une seule colonne.
 */
class CommandeInfolist
{
    private const COULEURS_STATUT = [
        'en_attente' => 'gray',
        'en_preparation' => 'info',
        'expediee' => 'warning',
        'livree' => 'success',
        'annulee' => 'danger',
    ];

    private const COULEURS_PAIEMENT = [
        'en_attente' => 'warning',
        'paye' => 'success',
        'rembourse' => 'danger',
    ];

    private const LABELS_PAIEMENT_MODE = [
        'carte' => 'Carte bancaire',
        'mobile_money' => 'Mobile Money',
        'livraison' => 'Paiement à la livraison',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // ==================== Bandeau récapitulatif (pleine largeur) ====================
            Section::make('Récapitulatif')
                ->icon('heroicon-o-clipboard-document-list')
                ->columns(['default' => 2, 'sm' => 3, 'xl' => 6])
                ->schema([
                    TextEntry::make('numero')->label('N° de commande')->weight('bold')->copyable(),
                    TextEntry::make('created_at')->label('Date')->dateTime('d/m/Y à H:i'),
                    TextEntry::make('statut')
                        ->label('Statut')
                        ->badge()
                        ->formatStateUsing(fn (string $state) => Commande::STATUTS_LABELS[$state] ?? $state)
                        ->color(fn (string $state) => self::COULEURS_STATUT[$state] ?? 'gray'),
                    TextEntry::make('mode_paiement')
                        ->label('Mode de paiement')
                        ->formatStateUsing(fn (?string $state) => self::LABELS_PAIEMENT_MODE[$state] ?? '—')
                        ->placeholder('—'),
                    TextEntry::make('methode_livraison')
                        ->label('Méthode de livraison')
                        ->state(fn (Commande $record) => $record->methodeLivraison()),
                    TextEntry::make('total')
                        ->label('Total')
                        ->money('XOF', divideBy: 1)
                        ->weight('bold')
                        ->color('primary')
                        ->size(TextSize::Large),
                ]),

            // ================ Produits commandés (pleine largeur, sa propre ligne) ================
            Section::make('Produits commandés')
                ->icon('heroicon-o-shopping-bag')
                ->schema([
                    ViewEntry::make('lignes')
                        ->hiddenLabel()
                        ->view('filament.commande.produits-table'),
                ]),

            // ================ Informations en grille 2 colonnes ================
            Grid::make(['default' => 1, 'lg' => 2])->schema([

                // ---------- Ligne 1 : Client | Résumé financier ----------
                Section::make('Client')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        TextEntry::make('client_avatar')
                            ->hiddenLabel()
                            ->html()
                            ->state(function (Commande $record) {
                                $c = $record->client;
                                $initiales = $c
                                    ? mb_strtoupper(mb_substr($c->prenom, 0, 1).mb_substr($c->nom, 0, 1))
                                    : '—';
                                $nom = $c ? e(trim("{$c->prenom} {$c->nom}")) : 'Client supprimé';

                                return '<div style="display:flex;align-items:center;gap:.75rem;">'
                                    .'<span style="display:inline-flex;height:44px;width:44px;flex:none;align-items:center;justify-content:center;border-radius:9999px;background:#aa4c00;color:#fff;font-weight:700;">'.$initiales.'</span>'
                                    .'<span style="font-weight:700;font-size:.95rem;">'.$nom.'</span>'
                                    .'</div>';
                            }),
                        TextEntry::make('client.email')
                            ->label('Email')
                            ->placeholder('—')
                            ->copyable()
                            ->url(fn (Commande $record) => $record->client?->email ? 'mailto:'.$record->client->email : null),
                        TextEntry::make('client.telephone')
                            ->label('Téléphone')
                            ->placeholder('—')
                            ->url(fn (Commande $record) => $record->client?->telephone ? 'tel:'.$record->client->telephone : null),
                        TextEntry::make('client_nb_commandes')
                            ->label('Commandes passées')
                            ->badge()
                            ->state(fn (Commande $record) => $record->client?->commandes()->count() ?? 0),
                        TextEntry::make('client_total_depense')
                            ->label('Montant total dépensé')
                            ->money('XOF', divideBy: 1)
                            ->weight('bold')
                            ->state(fn (Commande $record) => (float) ($record->client?->commandes()->sum('total') ?? 0)),
                        TextEntry::make('voir_client')
                            ->hiddenLabel()
                            ->state('Ouvrir la fiche client →')
                            ->color('primary')
                            ->weight('bold')
                            ->visible(fn (Commande $record) => (bool) $record->client)
                            ->url(fn (Commande $record) => $record->client
                                ? ClientResource::getUrl('index').'?tableSearch='.urlencode($record->client->email)
                                : null),
                    ]),

                Section::make('Résumé financier')
                    ->icon('heroicon-o-calculator')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('total_avant_remise')->label('Sous-total')->money('XOF', divideBy: 1),
                        TextEntry::make('reduction_montant')->label('Réductions')->money('XOF', divideBy: 1)->color('success'),
                        TextEntry::make('remise_membre')->label('Remise membre')->money('XOF', divideBy: 1)->placeholder('0'),
                        TextEntry::make('codePromo.code')->label('Code promo')->badge()->placeholder('Aucun'),
                        TextEntry::make('frais_livraison')->label('Frais de livraison')->money('XOF', divideBy: 1),
                        TextEntry::make('total')
                            ->label('Total final')
                            ->money('XOF', divideBy: 1)
                            ->weight('bold')
                            ->color('primary'),
                    ]),

                // ---------- Ligne 3 : Livraison | Paiement ----------
                Section::make('Livraison')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('destinataire')
                            ->label('Destinataire')
                            ->state(fn (Commande $record) => $record->client
                                ? trim("{$record->client->prenom} {$record->client->nom}")
                                : '—'),
                        TextEntry::make('methode_livraison_2')
                            ->label('Méthode')
                            ->state(fn (Commande $record) => $record->methodeLivraison()),
                        TextEntry::make('adresse_livraison')->label('Adresse')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('client.ville')->label('Commune / Ville')->placeholder('—'),
                        TextEntry::make('pays')->label('Pays')->state("Côte d'Ivoire"),
                    ]),

                Section::make('Paiement')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('mode_paiement')
                            ->label('Mode')
                            ->formatStateUsing(fn (?string $state) => self::LABELS_PAIEMENT_MODE[$state] ?? '—')
                            ->placeholder('—'),
                        TextEntry::make('statut_paiement')
                            ->label('Statut')
                            ->badge()
                            ->formatStateUsing(fn (?string $state) => Commande::STATUTS_PAIEMENT_LABELS[$state] ?? $state)
                            ->color(fn (?string $state) => self::COULEURS_PAIEMENT[$state] ?? 'gray'),
                        TextEntry::make('reference_paiement')->label('Référence de transaction')->placeholder('Non renseignée')->copyable()->columnSpanFull(),
                    ]),

                // ---------- Ligne 4 : Historique | Notes internes ----------
                Section::make('Historique de la commande')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        ViewEntry::make('historiques')
                            ->hiddenLabel()
                            ->view('filament.commande.historique-timeline'),
                    ]),

                Section::make('Notes internes')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([
                        ViewEntry::make('notes')
                            ->hiddenLabel()
                            ->view('filament.commande.notes'),
                    ]),
            ]),
        ]);
    }
}
