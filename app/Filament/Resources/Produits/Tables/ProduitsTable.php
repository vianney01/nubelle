<?php

namespace App\Filament\Resources\Produits\Tables;

use App\Models\Categorie;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProduitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->getStateUsing(fn (\App\Models\Produit $record) => $record->image),
                TextColumn::make('nom')->searchable()->sortable(),
                TextColumn::make('categorie.nom')->label('Catégorie')->badge()->sortable(),
                TextColumn::make('prix')
                    ->label('Prix')
                    ->money('XOF', divideBy: 1)
                    ->sortable(),
                TextColumn::make('stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state) => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
                IconColumn::make('nouveaute')->label('Nouveau')->boolean()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('best_seller')->label('Best-seller')->boolean()->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('actif')->boolean(),
                TextColumn::make('created_at')->label('Créé le')->dateTime('d/m/Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categorie_id')
                    ->label('Catégorie')
                    ->options(fn () => Categorie::query()->pluck('nom', 'id')),
                TernaryFilter::make('actif'),
                TernaryFilter::make('stock_limite')->label('Stock limité'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
