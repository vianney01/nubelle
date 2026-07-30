<?php

namespace App\Filament\Resources\Avis;

use App\Filament\Resources\Avis\Pages\ManageAvis;
use App\Models\Avis;
use App\Models\Produit;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AvisResource extends Resource
{
    protected static ?string $model = Avis::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Contenu';

    protected static ?string $navigationLabel = 'Avis clients';

    protected static ?string $modelLabel = 'avis';

    protected static ?string $pluralModelLabel = 'avis';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('produit_id')
                    ->label('Produit')
                    ->relationship('produit', 'nom')
                    ->searchable()
                    ->required(),
                Select::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'email')
                    ->searchable(),
                Select::make('note')
                    ->options([1 => '1 ★', 2 => '2 ★', 3 => '3 ★', 4 => '4 ★', 5 => '5 ★'])
                    ->required(),
                Toggle::make('visible')->default(true),
                Textarea::make('message')->rows(3)->required()->columnSpanFull(),
                Textarea::make('reponse_admin')->label('Réponse de Nubelle')->rows(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('produit.nom')->label('Produit')->searchable()->sortable(),
                TextColumn::make('client.email')->label('Client')->searchable(),
                TextColumn::make('note')->formatStateUsing(fn (int $state) => str_repeat('★', $state).str_repeat('☆', 5 - $state)),
                TextColumn::make('message')->limit(50),
                IconColumn::make('visible')->boolean(),
                TextColumn::make('created_at')->label('Date')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('visible'),
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
            'index' => ManageAvis::route('/'),
        ];
    }
}
