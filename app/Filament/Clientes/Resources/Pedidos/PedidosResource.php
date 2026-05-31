<?php

namespace App\Filament\Clientes\Resources\Pedidos;

use App\Filament\Clientes\Resources\Pedidos\Pages\ListPedidos;
use App\Filament\Support\ProgramaCategories;
use App\Filament\Support\ProgramasTableColumns;
use App\Models\Programas;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PedidosResource extends Resource
{
    protected static ?string $model = Programas::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Pedidos';

    protected static ?string $pluralModelLabel = 'Pedidos';

    protected static ?string $modelLabel = 'Pedido';

    protected static ?string $slug = 'pedidos';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->active()->visibleInPedidos();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('progname')
                    ->label('Programa')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('descargas')
                    ->label('DESCARGAS')
                    ->badge()
                    ->color(fn (Programas $record) => filled($record->url) ? 'success' : 'gray')
                    ->state(fn (Programas $record) => filled($record->url) ? 'DESCARGAR' : 'Sin enlace')
                    ->url(fn (Programas $record): ?string => ProgramasTableColumns::downloadUrl($record->url))
                    ->openUrlInNewTab()
                    ->alignCenter(),

                TextColumn::make('os_required')
                    ->label('Sistema operativo')
                    ->badge()
                    ->colors([
                        'info' => 'windows',
                        'danger' => 'mac',
                        'gray' => 'win-mac',
                    ])
                    ->formatStateUsing(fn (?string $state) => ProgramasTableColumns::osRequiredLabel($state))
                    ->sortable(),

                TextColumn::make('required')
                    ->label('SO requerido')
                    ->placeholder('-')
                    ->sortable()
                    ->wrap(),

                TextColumn::make('year_prog')
                    ->label('Año')
                    ->badge()
                    ->color('fuchsia')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options(ProgramaCategories::options())
                    ->searchable()
                    ->preload(),
            ])
            ->recordUrl(
                fn (Programas $record): ?string => ProgramasTableColumns::downloadUrl($record->url),
                shouldOpenInNewTab: true,
            )
            ->defaultSort('id', 'desc')
            ->emptyStateHeading('No hay pedidos disponibles')
            ->emptyStateDescription('Los programas aparecerán aquí cuando un administrador los active en Cards Programas (30 minutos) o cuando no esté activo el botón OFF.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPedidos::route('/'),
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
