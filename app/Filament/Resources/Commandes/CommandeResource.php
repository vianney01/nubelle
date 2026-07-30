<?php

namespace App\Filament\Resources\Commandes;

use App\Filament\Resources\Commandes\Pages\CreateCommande;
use App\Filament\Resources\Commandes\Pages\EditCommande;
use App\Filament\Resources\Commandes\Pages\ListCommandes;
use App\Filament\Resources\Commandes\Pages\ViewCommande;
use App\Filament\Resources\Commandes\Schemas\CommandeForm;
use App\Filament\Resources\Commandes\Schemas\CommandeInfolist;
use App\Filament\Resources\Commandes\Tables\CommandesTable;
use App\Models\Commande;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CommandeResource extends Resource
{
    protected static ?string $model = Commande::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static string|UnitEnum|null $navigationGroup = 'Ventes';

    protected static ?string $navigationLabel = 'Commandes';

    protected static ?string $modelLabel = 'commande';

    protected static ?string $pluralModelLabel = 'commandes';

    protected static ?string $recordTitleAttribute = 'numero';

    public static function form(Schema $schema): Schema
    {
        return CommandeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommandeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommandesTable::configure($table);
    }

    /**
     * Précharge client et code promo (colonnes de liste + entêtes de fiche)
     * pour éviter les requêtes N+1.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['client', 'codePromo']);
    }

    public static function getRelations(): array
    {
        // Les produits commandés sont désormais présentés en pleine largeur au
        // cœur de la fiche (CommandeInfolist) plutôt qu'en relation manager,
        // pour respecter l'ordre visuel demandé (cartes → produits → cartes).
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommandes::route('/'),
            'create' => CreateCommande::route('/create'),
            'view' => ViewCommande::route('/{record}'),
            'edit' => EditCommande::route('/{record}/edit'),
        ];
    }
}
