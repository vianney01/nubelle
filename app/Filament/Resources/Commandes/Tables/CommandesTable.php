<?php

namespace App\Filament\Resources\Commandes\Tables;

use App\Models\Commande;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CommandesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('numero')->label('N° commande')->searchable()->sortable(),
                TextColumn::make('client.prenom')
                    ->label('Client')
                    ->formatStateUsing(fn ($record) => $record->client ? "{$record->client->prenom} {$record->client->nom}" : '—')
                    ->searchable(['prenom', 'nom']),
                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Commande::STATUTS_LABELS[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'en_attente' => 'gray',
                        'en_preparation' => 'info',
                        'expediee' => 'warning',
                        'livree' => 'success',
                        'annulee' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('statut_paiement')
                    ->label('Paiement')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => Commande::STATUTS_PAIEMENT_LABELS[$state] ?? $state)
                    ->color(fn (?string $state) => match ($state) {
                        'paye' => 'success',
                        'rembourse' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('total')->label('Total')->money('XOF', divideBy: 1)->sortable(),
                TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->options(Commande::STATUTS_LABELS),
            ])
            ->recordActions([
                ViewAction::make(),
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
