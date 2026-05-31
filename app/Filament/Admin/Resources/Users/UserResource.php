<?php

namespace App\Filament\Admin\Resources\Users;

use App\Enums\UserRole;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
{
    return $schema
        ->components([
            Section::make('Datos del usuario')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('password')
                        ->label('Contraseña')
                        ->password()
                        ->revealable()
                        ->maxLength(255)
                        ->required(fn (string $operation) => $operation === 'create')
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null)
                        ->dehydrated(fn ($state) => filled($state)),

                    Select::make('role')
                        ->label('Tipo de usuario')
                        ->options([
                            UserRole::Administrador->value => 'Administrador',
                            UserRole::Invitado->value => 'Invitado (cliente)',
                        ])
                        ->required()
                        ->native(false)
                        ->dehydrated(true),
                ])
                ->columns(2),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('name')
                ->label('Nombre')
                ->searchable()
                ->sortable(),

            TextColumn::make('email')
                ->label('Email')
                ->searchable()
                ->sortable(),

            TextColumn::make('roles.name')
                ->label('Tipo')
                ->badge()
                ->formatStateUsing(fn (?string $state) => match ($state) {
                    UserRole::Administrador->value => 'Administrador',
                    UserRole::Invitado->value => 'Invitado',
                    default => $state ?? 'Sin rol',
                })
                ->color(fn (?string $state) => match ($state) {
                    UserRole::Administrador->value => 'success',
                    UserRole::Invitado->value => 'info',
                    default => 'gray',
                }),

            TextColumn::make('created_at')
                ->label('Creado')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
