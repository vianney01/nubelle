<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Commandes\CommandeResource;
use App\Models\Commande;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class DernieresCommandesWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 2;

    protected function getTableHeading(): string
    {
        return 'Dernières commandes';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Commande::query()->latest()->limit(8))
            ->paginated(false)
            ->columns([
                TextColumn::make('numero')->label('N° commande'),
                TextColumn::make('client.prenom')
                    ->label('Client')
                    ->formatStateUsing(fn ($record) => $record->client ? "{$record->client->prenom} {$record->client->nom}" : '—'),
                TextColumn::make('statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'en_attente' => 'En attente',
                        'expediee' => 'Expédiée',
                        'livree' => 'Livrée',
                        'annulee' => 'Annulée',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'en_attente' => 'gray',
                        'expediee' => 'warning',
                        'livree' => 'success',
                        'annulee' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('total')->label('Total')->money('XOF', divideBy: 1),
                TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y'),
            ])
            ->recordActions([
                Action::make('voir')
                    ->label('Voir')
                    ->url(fn (Commande $record) => CommandeResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
