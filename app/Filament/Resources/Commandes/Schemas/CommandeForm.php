<?php

namespace App\Filament\Resources\Commandes\Schemas;

use App\Models\Client;
use App\Models\Commande;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommandeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Commande')
                    ->columns(2)
                    ->components([
                        TextInput::make('numero')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30),
                        Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'email')
                            ->getOptionLabelFromRecordUsing(fn (Client $client) => "{$client->prenom} {$client->nom} ({$client->email})")
                            ->searchable(['prenom', 'nom', 'email'])
                            ->required(),
                        Select::make('statut')
                            ->options(Commande::STATUTS_LABELS)
                            ->default('en_attente')
                            ->required(),
                        Select::make('statut_paiement')
                            ->label('Statut du paiement')
                            ->options(Commande::STATUTS_PAIEMENT_LABELS)
                            ->default('en_attente')
                            ->required(),
                        TextInput::make('total')
                            ->label('Total (FCFA)')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                        Select::make('mode_paiement')
                            ->label('Mode de paiement')
                            ->options([
                                'carte' => 'Carte bancaire',
                                'mobile_money' => 'Mobile Money',
                                'livraison' => 'Paiement à la livraison',
                            ]),
                        TextInput::make('reference_paiement')
                            ->label('Référence de transaction')
                            ->maxLength(100),
                        Textarea::make('adresse_livraison')
                            ->label('Adresse de livraison')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
