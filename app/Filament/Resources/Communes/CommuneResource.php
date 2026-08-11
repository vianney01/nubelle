<?php

namespace App\Filament\Resources\Communes;

use App\Filament\Resources\Communes\Pages\ManageCommunes;
use App\Models\Commune;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CommuneResource extends Resource
{
    protected static ?string $model = Commune::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Ventes';

    protected static ?string $navigationLabel = 'Communes (livraison)';

    protected static ?string $modelLabel = 'commune';

    protected static ?string $pluralModelLabel = 'communes';

    protected static ?string $recordTitleAttribute = 'nom';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nom')
                    ->label('Nom de la commune')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(120),
                TextInput::make('prix')
                    ->label('Prix de livraison (FCFA)')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->default(0),
                Toggle::make('actif')
                    ->label('Active')
                    ->helperText('Décochez pour la retirer du choix au checkout.')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('nom')
            ->columns([
                TextColumn::make('nom')->searchable()->sortable(),
                TextColumn::make('prix')
                    ->label('Prix de livraison')
                    ->money('XOF', divideBy: 1)
                    ->sortable(),
                IconColumn::make('actif')->label('Active')->boolean(),
                TextColumn::make('updated_at')->dateTime('d/m/Y')->label('Modifiée le')->sortable()->toggleable(),
            ])
            ->filters([
                //
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
            'index' => ManageCommunes::route('/'),
        ];
    }
}
