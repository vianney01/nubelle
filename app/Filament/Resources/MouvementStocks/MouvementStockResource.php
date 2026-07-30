<?php

namespace App\Filament\Resources\MouvementStocks;

use App\Filament\Resources\MouvementStocks\Pages\ManageMouvementStocks;
use App\Models\MouvementStock;
use App\Models\Produit;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MouvementStockResource extends Resource
{
    protected static ?string $model = MouvementStock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBoxArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'Stock';

    protected static ?string $navigationLabel = 'Mouvements de stock';

    protected static ?string $modelLabel = 'mouvement de stock';

    protected static ?string $pluralModelLabel = 'mouvements de stock';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('produit_id')
                    ->label('Produit')
                    ->relationship('produit', 'nom')
                    ->searchable()
                    ->required(),
                Select::make('type')
                    ->options([
                        'entree' => 'Entrée',
                        'sortie' => 'Sortie',
                    ])
                    ->required(),
                TextInput::make('quantite')->numeric()->required()->minValue(1),
                TextInput::make('motif')->maxLength(150)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('produit.nom')->label('Produit')->searchable()->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'entree' ? 'Entrée' : 'Sortie')
                    ->color(fn (string $state) => $state === 'entree' ? 'success' : 'danger'),
                TextColumn::make('quantite')->sortable(),
                TextColumn::make('motif'),
                TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')->options(['entree' => 'Entrée', 'sortie' => 'Sortie']),
                SelectFilter::make('produit_id')
                    ->label('Produit')
                    ->options(fn () => Produit::query()->pluck('nom', 'id')),
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

    public static function getPages(): array
    {
        return [
            'index' => ManageMouvementStocks::route('/'),
        ];
    }
}
