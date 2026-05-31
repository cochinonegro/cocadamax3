<?php

namespace App\Filament\Admin\Resources\CardsProgramas;

use App\Filament\Admin\Resources\CardsProgramas\Pages\ListCardsProgramas;
use App\Models\Programas;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CardsProgramasResource extends Resource
{
    protected static ?string $model = Programas::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Cards Programas';

    protected static ?string $modelLabel = 'Card Programa';

    protected static ?string $pluralModelLabel = 'Cards Programas';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'cards-programas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCardsProgramas::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
