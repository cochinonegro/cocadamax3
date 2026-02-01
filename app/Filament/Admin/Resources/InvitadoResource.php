<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InvitadoResource\Pages;
use App\Filament\Admin\Resources\InvitadoResource\RelationManagers;
use App\Models\Invitado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvitadoResource extends Resource
{
    protected static ?string $model = Invitado::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListInvitados::route('/'),
            'create' => Pages\CreateInvitado::route('/create'),
            'edit' => Pages\EditInvitado::route('/{record}/edit'),
        ];
    }
}
