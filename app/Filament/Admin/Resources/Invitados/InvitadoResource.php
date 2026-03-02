<?php

namespace App\Filament\Admin\Resources\Invitados;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Admin\Resources\Invitados\Pages\ListInvitados;
use App\Filament\Admin\Resources\Invitados\Pages\CreateInvitado;
use App\Filament\Admin\Resources\Invitados\Pages\EditInvitado;
use App\Filament\Admin\Resources\InvitadoResource\Pages;
use App\Filament\Admin\Resources\InvitadoResource\RelationManagers;
use App\Models\Invitado;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvitadoResource extends Resource
{
    protected static ?string $model = Invitado::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
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
            'index' => ListInvitados::route('/'),
            'create' => CreateInvitado::route('/create'),
            'edit' => EditInvitado::route('/{record}/edit'),
        ];
    }
}
