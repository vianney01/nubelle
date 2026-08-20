<?php

namespace App\Filament\Resources\Commandes\RelationManagers;

use App\Filament\Resources\Produits\ProduitResource;
use App\Models\LigneCommande;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Tableau des produits d'une commande, en lecture seule : image, nom,
 * référence, catégorie, prix, quantité, sous-total, promotion et stock
 * restant. Chaque ligne renvoie vers la fiche produit du back-office.
 */
class LignesRelationManager extends RelationManager
{
    protected static string $relationship = 'lignes';

    protected static ?string $title = 'Produits commandés';

    protected static string|BackedEnum|null $icon = 'heroicon-o-shopping-bag';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->paginated(false)
            ->modifyQueryUsing(fn ($query) => $query->with('produit.categorie'))
            ->recordUrl(fn (LigneCommande $record) => $record->produit
                ? ProduitResource::getUrl('edit', ['record' => $record->produit_id])
                : null)
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->height(52)
                    ->square()
                    ->getStateUsing(fn (LigneCommande $record) => ($u = $record->produit?->image) ? url($u) : null),
                TextColumn::make('produit.nom')
                    ->label('Produit')
                    ->weight('bold')
                    ->wrap()
                    ->placeholder('Produit supprimé'),
                TextColumn::make('reference')
                    ->label('Référence')
                    ->badge()
                    ->color('gray')
                    ->getStateUsing(fn (LigneCommande $record) => $record->produit_id
                        ? 'REF-'.str_pad((string) $record->produit_id, 4, '0', STR_PAD_LEFT)
                        : '—'),
                TextColumn::make('produit.categorie.nom')
                    ->label('Catégorie')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('prix_unitaire')
                    ->label('Prix unitaire')
                    ->money('XOF', divideBy: 1),
                TextColumn::make('quantite')
                    ->label('Qté')
                    ->alignCenter(),
                TextColumn::make('sous_total')
                    ->label('Sous-total')
                    ->money('XOF', divideBy: 1)
                    ->weight('bold')
                    ->getStateUsing(fn (LigneCommande $record) => (float) $record->prix_unitaire * $record->quantite),
                TextColumn::make('promotion')
                    ->label('Promotion')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(fn (LigneCommande $record, $livewire) => $livewire->getOwnerRecord()->codePromo?->code ?? '—'),
                TextColumn::make('produit.stock')
                    ->label('Stock restant')
                    ->badge()
                    ->formatStateUsing(fn (?int $state) => ($state ?? 0).' u.')
                    ->color(fn (?int $state) => match (true) {
                        ($state ?? 0) <= 0 => 'danger',
                        ($state ?? 0) < 5 => 'warning',
                        default => 'success',
                    }),
            ])
            ->recordActions([
                Action::make('voir_produit')
                    ->label('Voir le produit')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (LigneCommande $record) => $record->produit
                        ? ProduitResource::getUrl('edit', ['record' => $record->produit_id])
                        : null)
                    ->openUrlInNewTab()
                    ->hidden(fn (LigneCommande $record) => ! $record->produit),
            ]);
    }
}
