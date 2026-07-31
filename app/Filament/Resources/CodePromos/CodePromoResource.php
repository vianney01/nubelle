<?php

namespace App\Filament\Resources\CodePromos;

use App\Filament\Resources\CodePromos\Pages\ManageCodePromos;
use App\Models\CodePromo;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use UnitEnum;

class CodePromoResource extends Resource
{
    protected static ?string $model = CodePromo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Promotions';

    protected static ?string $modelLabel = 'promotion';

    protected static ?string $pluralModelLabel = 'promotions';

    protected static ?string $recordTitleAttribute = 'nom';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations')
                    ->icon(Heroicon::OutlinedTag)
                    ->description('Nom, code et description de la promotion.')
                    ->columns(2)
                    ->components([
                        Toggle::make('automatique')
                            ->label('Remise membre automatique (sans code)')
                            ->helperText('Appliquée automatiquement selon le profil client, sans saisie de code.')
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('code')
                            ->label('Code')
                            ->placeholder('Ex : BIENVENUE10')
                            ->maxLength(50)
                            ->alphaDash()
                            ->visible(fn ($get) => ! $get('automatique'))
                            ->required(fn ($get) => ! $get('automatique'))
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? strtoupper($state) : null)
                            ->rule(fn ($get, $record) => $get('automatique')
                                ? null
                                : Rule::unique('codes_promo', 'code')->ignore($record?->id)),
                        Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Réduction')
                    ->icon(Heroicon::OutlinedReceiptPercent)
                    ->description('Type et montant de l\'avantage accordé.')
                    ->columns(3)
                    ->components([
                        Select::make('type_reduction')
                            ->label('Type de réduction')
                            ->options([
                                CodePromo::TYPE_POURCENTAGE => 'Pourcentage (%)',
                                CodePromo::TYPE_MONTANT_FIXE => 'Montant fixe (FCFA)',
                            ])
                            ->default(CodePromo::TYPE_POURCENTAGE)
                            ->required()
                            ->live(),
                        TextInput::make('valeur')
                            ->label(fn ($get) => $get('type_reduction') === CodePromo::TYPE_MONTANT_FIXE ? 'Montant (FCFA)' : 'Pourcentage (%)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(fn ($get) => $get('type_reduction') === CodePromo::TYPE_POURCENTAGE ? 100 : null),
                        Toggle::make('livraison_gratuite')
                            ->label('Livraison gratuite')
                            ->inline(false),
                    ]),

                Section::make('Validité & limites')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->columns(3)
                    ->components([
                        DatePicker::make('date_debut')->label('Date de début'),
                        DatePicker::make('date_fin')->label('Date de fin'),
                        Toggle::make('actif')->label('Actif')->default(true)->inline(false),
                        TextInput::make('montant_min')
                            ->label('Montant minimum du panier (FCFA)')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Laisser vide pour aucun minimum.'),
                        TextInput::make('max_utilisations')
                            ->label('Utilisations max (global)')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Vide = illimité.'),
                        TextInput::make('max_utilisations_client')
                            ->label('Utilisations max par client')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Vide = illimité.'),
                    ]),

                Section::make('Priorité & cumul')
                    ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                    ->columns(2)
                    ->components([
                        TextInput::make('priorite')
                            ->label('Priorité')
                            ->numeric()
                            ->default(0)
                            ->helperText('Plus élevée = appliquée en premier.'),
                        Toggle::make('cumulable')
                            ->label('Cumulable avec d\'autres promotions')
                            ->inline(false),
                    ]),

                Section::make('Produits éligibles')
                    ->icon(Heroicon::OutlinedShoppingBag)
                    ->description('Sans sélection, la réduction s\'applique à tout le panier.')
                    ->columns(2)
                    ->collapsible()
                    ->components([
                        Select::make('produits')
                            ->label('Produits spécifiques')
                            ->relationship('produits', 'nom')
                            ->getOptionLabelFromRecordUsing(fn (\App\Models\Produit $record) =>
                                '<div style="display:flex;align-items:center;gap:.55rem;width:100%;min-width:0;">'
                                .'<img src="'.e($record->image).'" alt="" style="height:28px;width:28px;border-radius:.4rem;object-fit:cover;background:#f3f4f6;flex:none;">'
                                .'<span style="flex:1 1 auto;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:500;">'.e($record->nom).'</span>'
                                .'<span style="flex:none;opacity:.6;font-size:.8rem;white-space:nowrap;">'.number_format((float) $record->prix, 0, ',', ' ').' FCFA</span>'
                                .'</div>')
                            ->allowHtml()
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Select::make('categories')
                            ->label('Catégories')
                            ->relationship('categories', 'nom')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),

                Section::make('Clients éligibles')
                    ->icon(Heroicon::OutlinedUserGroup)
                    ->description('À qui la promotion est réservée.')
                    ->columns(1)
                    ->collapsible()
                    ->components([
                        Select::make('restriction_client')
                            ->label('Clients concernés')
                            ->options([
                                CodePromo::RESTRICTION_TOUS => 'Tous les clients',
                                CodePromo::RESTRICTION_INSCRITS => 'Clients déjà inscrits (ont déjà commandé)',
                                CodePromo::RESTRICTION_NOUVEAUX => 'Nouveaux clients (première commande)',
                                CodePromo::RESTRICTION_SELECTION => 'Sélection de clients',
                            ])
                            ->default(CodePromo::RESTRICTION_TOUS)
                            ->required()
                            ->live(),
                        Select::make('clients')
                            ->label('Clients autorisés')
                            ->relationship('clients', 'email')
                            ->getOptionLabelFromRecordUsing(fn ($record) => trim("{$record->prenom} {$record->nom}")." ({$record->email})")
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->visible(fn ($get) => $get('restriction_client') === CodePromo::RESTRICTION_SELECTION),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('priorite', 'desc')
            ->columns([
                TextColumn::make('nom')->searchable()->sortable()->weight('bold'),
                TextColumn::make('code')
                    ->badge()
                    ->placeholder('— auto')
                    ->searchable(),
                IconColumn::make('automatique')->label('Auto')->boolean(),
                TextColumn::make('valeur')
                    ->label('Réduction')
                    ->formatStateUsing(fn ($record) => $record->libelleReduction()),
                TextColumn::make('restriction_client')
                    ->label('Clients')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        CodePromo::RESTRICTION_INSCRITS => 'Inscrits',
                        CodePromo::RESTRICTION_NOUVEAUX => 'Nouveaux',
                        CodePromo::RESTRICTION_SELECTION => 'Sélection',
                        default => 'Tous',
                    }),
                TextColumn::make('commandes_count')
                    ->counts('commandes')
                    ->label('Utilisations')
                    ->badge()
                    ->color('info'),
                TextColumn::make('commandes_sum_total')
                    ->sum('commandes', 'total')
                    ->label('CA généré')
                    ->money('XOF', divideBy: 1)
                    ->placeholder('0'),
                TextColumn::make('priorite')->label('Priorité')->sortable()->toggleable(),
                TextColumn::make('date_fin')->label('Fin')->date('d/m/Y')->placeholder('—')->toggleable(),
                ToggleColumn::make('actif')->label('Actif'),
            ])
            ->filters([
                TernaryFilter::make('actif'),
                TernaryFilter::make('automatique')->label('Remise automatique'),
                SelectFilter::make('type_reduction')
                    ->label('Type')
                    ->options([
                        CodePromo::TYPE_POURCENTAGE => 'Pourcentage',
                        CodePromo::TYPE_MONTANT_FIXE => 'Montant fixe',
                    ]),
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
            'index' => ManageCodePromos::route('/'),
        ];
    }
}
