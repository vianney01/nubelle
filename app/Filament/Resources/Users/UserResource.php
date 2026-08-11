<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Paramètres';

    protected static ?string $navigationLabel = 'Administrateurs';

    protected static ?string $modelLabel = 'administrateur';

    protected static ?string $pluralModelLabel = 'administrateurs';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nom')->required()->maxLength(150),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                Select::make('role')
                    ->label('Rôle')
                    ->options([
                        User::ROLE_ADMIN => 'Administrateur',
                        User::ROLE_CLIENT => 'Client',
                    ])
                    ->default(User::ROLE_ADMIN)
                    ->required()
                    ->native(false)
                    ->helperText('« Administrateur » = accès au back-office. « Client » = accès boutique uniquement (le compte quitte cette liste).'),
                TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create')
                    ->helperText('Laisser vide pour ne pas modifier le mot de passe.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('role')
                    ->label('Rôle')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state === User::ROLE_ADMIN ? 'Administrateur' : 'Client')
                    ->color(fn (?string $state) => $state === User::ROLE_ADMIN ? 'success' : 'gray'),
                TextColumn::make('created_at')->label('Créé le')->date('d/m/Y')->sortable(),
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

    /**
     * Cet écran ne gère que les administrateurs (accès back-office). Les comptes
     * clients relèvent de la boutique et n'apparaissent donc pas ici. Passer un
     * compte en « Client » depuis le formulaire le retire de cette liste.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', User::ROLE_ADMIN);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
