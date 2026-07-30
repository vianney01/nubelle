<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Produits\ProduitResource;
use App\Models\Produit;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class AlertesStockWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    protected function getTableHeading(): string
    {
        return 'Alertes de stock';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Produit::query()
                    ->where('stock', '<=', 10)
                    ->orderBy('stock')
                    ->limit(8)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('nom')->label('Produit'),
                TextColumn::make('categorie.nom')->label('Catégorie'),
                TextColumn::make('stock')
                    ->badge()
                    ->color(fn (int $state) => $state <= 0 ? 'danger' : 'warning')
                    ->formatStateUsing(fn (int $state) => $state <= 0 ? 'Rupture' : "{$state} restants"),
            ])
            ->recordActions([
                Action::make('gerer')
                    ->label('Gérer')
                    ->url(fn (Produit $record) => ProduitResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
