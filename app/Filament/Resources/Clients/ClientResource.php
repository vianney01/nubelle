<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\ManageClients;
use App\Models\Client;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Clients';

    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $modelLabel = 'client';

    protected static ?string $pluralModelLabel = 'clients';

    protected static ?string $recordTitleAttribute = 'email';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité')
                    ->columns(2)
                    ->components([
                        TextInput::make('prenom')->required()->maxLength(100),
                        TextInput::make('nom')->required()->maxLength(100),
                        TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                        TextInput::make('telephone')->tel()->maxLength(30),
                        TextInput::make('whatsapp')->label('WhatsApp')->tel()->maxLength(30)
                            ->helperText('Ex. : 0556400246 ou +2250556400246'),
                    ]),
                Section::make('Adresse')
                    ->columns(2)
                    ->components([
                        Textarea::make('adresse')->rows(2)->columnSpanFull(),
                        TextInput::make('ville')->maxLength(100),
                        TextInput::make('code_postal')->label('Code postal')->maxLength(20),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('prenom')
                    ->label('Client')
                    ->formatStateUsing(fn ($record) => "{$record->prenom} {$record->nom}")
                    ->searchable(['prenom', 'nom']),
                TextColumn::make('email')->searchable(),
                TextColumn::make('telephone'),
                TextColumn::make('whatsapp')->label('WhatsApp')->searchable()->copyable()->placeholder('—'),
                TextColumn::make('ville'),
                TextColumn::make('commandes_count')->counts('commandes')->label('Commandes')->badge(),
                TextColumn::make('created_at')->label('Client depuis')->date('d/m/Y')->sortable(),
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
            'index' => ManageClients::route('/'),
        ];
    }
}
