<?php

namespace App\Filament\Pages;

use App\Models\CodePromo;
use App\Models\ContenuAccueil;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Écran unique de gestion des contenus éditables de la page d'accueil
 * (Hero, « Pourquoi choisir Nubelle », « À propos »). Les modifications sont
 * enregistrées en base et visibles immédiatement sur le front.
 */
class GererAccueil extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = "Page d'accueil";

    protected static ?string $title = "Contenus de la page d'accueil";

    protected string $view = 'filament.pages.gerer-accueil';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(ContenuAccueil::instance()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Section::make('Hero (bandeau principal)')
                    ->description('Grande image d’en-tête et son message.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('hero_image')
                            ->label('Image de fond')
                            ->image()
                            ->disk('public')
                            ->directory('accueil')
                            ->imageEditor()
                            ->helperText('Laisser vide pour conserver l’image actuelle.')
                            ->columnSpanFull(),
                        TextInput::make('hero_sous_titre')->label('Sur-titre (petit texte)')->maxLength(150),
                        TextInput::make('hero_titre')->label('Titre principal')->maxLength(150),
                        TextInput::make('hero_bouton_texte')->label('Texte du bouton')->maxLength(60),
                        TextInput::make('hero_bouton_lien')->label('Lien du bouton')->maxLength(255)->placeholder('/produits'),
                    ]),

                Section::make('Pourquoi choisir Nubelle')
                    ->description('Le petit titre et le titre de la section, ainsi que l’image et le bouton du bloc mis en avant.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('pourquoi_image')
                            ->label('Image mise en avant')
                            ->image()
                            ->disk('public')
                            ->directory('accueil')
                            ->imageEditor()
                            ->helperText('Laisser vide pour conserver l’image actuelle.')
                            ->columnSpanFull(),
                        TextInput::make('pourquoi_eyebrow')->label('Petit titre')->maxLength(150),
                        TextInput::make('pourquoi_titre')->label('Titre principal')->maxLength(150),
                        TextInput::make('pourquoi_bouton_texte')->label('Texte du bouton')->maxLength(60),
                        TextInput::make('pourquoi_bouton_lien')->label('Lien du bouton')->maxLength(255)->placeholder('/produits'),
                    ]),

                Section::make('À propos de Nubelle')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('apropos_image')
                            ->label('Photo')
                            ->image()
                            ->disk('public')
                            ->directory('accueil')
                            ->imageEditor()
                            ->helperText('Laisser vide pour conserver la photo actuelle.')
                            ->columnSpanFull(),
                        TextInput::make('apropos_sous_titre')->label('Petit titre')->maxLength(150),
                        TextInput::make('apropos_titre')->label('Titre principal')->maxLength(150),
                        Textarea::make('apropos_texte')->label('Texte de présentation')->rows(6)->columnSpanFull(),
                        TextInput::make('apropos_bouton_texte')->label('Texte du bouton')->maxLength(60),
                        TextInput::make('apropos_bouton_lien')->label('Lien du bouton')->maxLength(255)->placeholder('/a-propos'),
                    ]),

                Section::make('Pop-up marketing (acquisition)')
                    ->description('Affiche un code promo de bienvenue aux nouveaux visiteurs. Le code est récupéré depuis le module Promotions (type, montant, dates et conditions sont automatiques).')
                    ->columns(2)
                    ->schema([
                        Toggle::make('popup_actif')
                            ->label('Afficher la pop-up')
                            ->helperText('Désactivée par défaut. Activez-la pour lancer la campagne.')
                            ->columnSpanFull(),
                        FileUpload::make('popup_image')
                            ->label('Image')
                            ->image()
                            ->disk('public')
                            ->directory('accueil')
                            ->imageEditor()
                            ->helperText('Laisser vide pour conserver l’image actuelle.')
                            ->columnSpanFull(),
                        TextInput::make('popup_badge')->label('Badge (ex. « OFFRE DE BIENVENUE »)')->maxLength(60),
                        Select::make('popup_cible')
                            ->label('Afficher aux…')
                            ->options([
                                'non_connectes' => 'Visiteurs non connectés',
                                'nouveaux_inscrits' => 'Nouveaux inscrits (sans commande)',
                                'jamais_commande' => 'Clients n’ayant jamais commandé',
                            ])
                            ->default('non_connectes')
                            ->required(),
                        TextInput::make('popup_titre')->label('Titre principal')->maxLength(150),
                        TextInput::make('popup_sous_titre')->label('Sous-titre')->maxLength(200),
                        Select::make('popup_code_promo_id')
                            ->label('Code promo de bienvenue')
                            ->options(fn () => CodePromo::query()
                                ->manuels()
                                ->orderBy('code')
                                ->get()
                                ->mapWithKeys(fn (CodePromo $c) => [$c->id => $c->code.' — '.$c->libelleReduction()]))
                            ->searchable()
                            ->placeholder('Aucun (pop-up sans code)')
                            ->helperText('Choisir un code existant — il n’est jamais dupliqué.')
                            ->columnSpanFull(),
                        TextInput::make('popup_bouton_texte')->label('Texte du bouton')->maxLength(60),
                        TextInput::make('popup_bouton_lien')->label('Lien du bouton')->maxLength(255)->placeholder('/connexion'),
                    ]),
            ]);
    }

    public function save(): void
    {
        ContenuAccueil::instance()->update($this->form->getState());

        Notification::make()
            ->title('Contenus enregistrés — visibles immédiatement sur la page d’accueil.')
            ->success()
            ->send();
    }
}
