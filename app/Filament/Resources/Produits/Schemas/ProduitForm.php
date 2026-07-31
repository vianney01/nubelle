<?php

namespace App\Filament\Resources\Produits\Schemas;

use App\Models\Categorie;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProduitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->columns(2)
                    ->components([
                        TextInput::make('nom')
                            ->required()
                            ->live(onBlur: true)
                            ->maxLength(150)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state)))
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(150),
                        Select::make('categorie_id')
                            ->label('Catégorie')
                            ->options(fn () => Categorie::query()->pluck('nom', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('description')
                            ->label('Description courte')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        FileUpload::make('image_principale')
                            ->label('Image principale')
                            ->image()
                            ->disk('public')
                            ->directory('produits')
                            ->imageEditor()
                            ->columnSpanFull(),
                        FileUpload::make('galerie')
                            ->label('Galerie (images supplémentaires)')
                            ->helperText('Glissez-déposez plusieurs images. Réorganisez-les par glisser-déposer.')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->disk('public')
                            ->directory('produits')
                            ->imageEditor()
                            ->panelLayout('grid')
                            ->columnSpanFull(),
                    ]),

                Section::make('Prix & stock')
                    ->columns(3)
                    ->components([
                        TextInput::make('prix')
                            ->label('Prix (FCFA)')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                        TextInput::make('ancien_prix')
                            ->label('Ancien prix (FCFA)')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Laisser vide si aucune promotion.'),
                        TextInput::make('stock')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0),
                    ]),

                Section::make('Détails produit')
                    ->columns(1)
                    ->collapsible()
                    ->components([
                        Textarea::make('description_longue')->label('Description longue')->rows(3),
                        Textarea::make('composition')->rows(3),
                        Textarea::make('conseils')->label("Conseils d'utilisation")->rows(3),
                    ]),

                Section::make('Visibilité')
                    ->columns(4)
                    ->components([
                        Toggle::make('nouveaute')->label('Nouveauté'),
                        Toggle::make('best_seller')->label('Meilleure vente'),
                        Toggle::make('stock_limite')->label('Stock limité'),
                        Toggle::make('actif')->label('Actif')->default(true),
                    ]),
            ]);
    }
}
