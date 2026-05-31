<?php

namespace App\Filament\Clientes\Resources\Pedidos;

use App\Filament\Clientes\Resources\Pedidos\Pages\ListPedidos;
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
        return parent::getEloquentQuery()->active();
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

                TextColumn::make('company')
                    ->label('Marca')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('os_required')
                    ->label('Sistema')
                    ->badge()
                    ->colors([
                        'info' => 'windows',
                        'danger' => 'mac',
                        'gray' => 'win-mac',
                    ])
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'windows' => 'Windows',
                        'mac' => 'Mac',
                        'win-mac' => 'Win & Mac',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('size')
                    ->label('Tamaño')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('category')
                    ->label('Categoría')
                    ->formatStateUsing(fn (?string $state) => ucfirst($state ?? '-'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('descargas')
                    ->label('DESCARGAS')
                    ->badge()
                    ->color(fn (Programas $record) => filled($record->url) ? 'success' : 'gray')
                    ->state(fn (Programas $record) => filled($record->url) ? 'DESCARGAR' : 'Sin enlace')
                    ->url(fn (Programas $record): ?string => filled($record->url) ? $record->url : null)
                    ->openUrlInNewTab()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'aplicaciones' => 'Aplicaciones',
                        'diseño grafico' => 'Diseño gráfico',
                        'arquitectura' => 'Arquitectura',
                        'music' => 'Música',
                        'video' => 'Video',
                        'kontakt' => 'Kontakt',
                    ])
                    ->searchable()
                    ->preload(),
            ])
            ->recordUrl(
                fn (Programas $record): ?string => filled($record->url) ? $record->url : null,
                shouldOpenInNewTab: true,
            )
            ->defaultSort('id', 'desc')
            ->emptyStateHeading('No hay pedidos disponibles')
            ->emptyStateDescription('Los programas aparecerán aquí cuando el STATUS esté activo en administración.');
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
