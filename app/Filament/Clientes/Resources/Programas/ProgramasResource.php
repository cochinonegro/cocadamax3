<?php

namespace App\Filament\Clientes\Resources\Programas;

use App\Filament\Clientes\Resources\Programas\Pages\ListProgramas;
use App\Filament\Clientes\Resources\Programas\Pages\ViewProgramas;
use App\Models\Programas;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use App\Filament\Support\ProgramasTableColumns;
use App\Filament\Support\ProgramaCategories;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProgramasResource extends Resource
{
    protected static ?string $model = Programas::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'Programas';

    protected static ?string $pluralModelLabel = 'Programas';

    protected static ?string $modelLabel = 'Programa';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                View::make('filament.clientes.infolists.producto-detalle')
                    ->columnSpanFull(),

                Section::make('Instalación')
                    ->columnSpanFull()
                    ->visible(fn (Programas $record): bool => $record->isInstaladorVisibleToClients())
                    ->extraAttributes(['class' => 'cliente-instalacion-section'])
                    ->schema([
                        View::make('filament.clientes.infolists.instalacion')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->active();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(ProgramasTableColumns::make(
                withWebOficial: true,
                withDownloadColumn: false,
            ))
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options(ProgramaCategories::options())
                    ->searchable()
                    ->preload(),
            ])
            ->recordUrl(fn (Programas $record): string => static::getUrl('view', ['record' => $record]))
            ->defaultSort('id', 'desc')
            ->emptyStateHeading('No hay programas disponibles')
            ->emptyStateDescription('Los programas aparecerán aquí cuando el STATUS esté activo en administración.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProgramas::route('/'),
            'view' => ViewProgramas::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }
}
