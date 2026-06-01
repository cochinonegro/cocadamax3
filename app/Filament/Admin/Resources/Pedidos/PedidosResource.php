<?php

namespace App\Filament\Admin\Resources\Pedidos;

use App\Filament\Admin\Resources\Pedidos\Pages\ListPedidos;
use App\Filament\Support\ProgramaCategories;
use App\Filament\Support\ProgramasTableColumns;
use App\Models\Programas;
use App\Support\DisplayTimezone;
use App\Support\PedidosVisibility;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
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

    protected static ?string $modelLabel = 'Programa en Pedidos';

    protected static ?string $slug = 'pedidos';

    protected static ?int $navigationSort = 3;

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
                    ->color(fn (Programas $record): string => filled($record->url) ? 'success' : 'gray')
                    ->state(fn (Programas $record): string => filled($record->url)
                        ? 'Descarga aquí el programa'
                        : 'Sin enlace')
                    ->url(fn (Programas $record): ?string => ProgramasTableColumns::downloadUrl($record->url))
                    ->openUrlInNewTab()
                    ->wrap()
                    ->alignCenter(),

                TextColumn::make('os_required')
                    ->label('Sistema operativo')
                    ->badge()
                    ->colors([
                        'info' => 'windows',
                        'danger' => 'mac',
                        'gray' => 'win-mac',
                    ])
                    ->formatStateUsing(fn (?string $state): string => ProgramasTableColumns::osRequiredLabel($state))
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

                TextColumn::make('pedidos_visible_until')
                    ->label('Visible hasta')
                    ->dateTime('d/m/Y H:i')
                    ->timezone(DisplayTimezone::name())
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options(ProgramaCategories::options())
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('eliminar')
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Quitar de Pedidos')
                    ->modalDescription('El programa dejará de mostrarse en la lista de Pedidos de los clientes. No se borra del catálogo.')
                    ->modalSubmitActionLabel('Quitar de Pedidos')
                    ->action(function (Programas $record): void {
                        PedidosVisibility::disableFor($record);

                        Notification::make()
                            ->title('Programa quitado de Pedidos')
                            ->body('Los clientes ya no lo verán en su tabla de Pedidos.')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordUrl(
                fn (Programas $record): ?string => ProgramasTableColumns::downloadUrl($record->url),
                shouldOpenInNewTab: true,
            )
            ->defaultSort('pedidos_visible_until', 'desc')
            ->emptyStateHeading('No hay programas visibles en Pedidos')
            ->emptyStateDescription('Activa programas desde Cards o Programas, o acepta solicitudes por Telegram.');
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

    public static function canDelete($record): bool
    {
        return false;
    }
}
