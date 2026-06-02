<?php

namespace App\Filament\Admin\Resources\Descargas;

use App\Filament\Admin\Resources\Descargas\Pages\EditDescarga;
use App\Filament\Admin\Resources\Descargas\Pages\ListDescargas;
use App\Models\Descarga;
use App\Support\DisplayTimezone;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DescargasResource extends Resource
{
    protected static ?string $model = Descarga::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationLabel = 'Descargas';

    protected static ?string $modelLabel = 'Descarga';

    protected static ?string $pluralModelLabel = 'Descargas';

    protected static ?string $slug = 'descargas';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Registro de descarga')
                    ->schema([
                        Select::make('user_id')
                            ->label('Usuario')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('Sin usuario identificado'),

                        Select::make('programas_id')
                            ->label('Programa')
                            ->relationship(
                                'programa',
                                'progname',
                                fn (Builder $query): Builder => $query->orderBy('progname'),
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        DateTimePicker::make('downloaded_at')
                            ->label('Fecha y hora de descarga')
                            ->required()
                            ->seconds(false)
                            ->native(false)
                            ->default(now()),

                        TextInput::make('precio')
                            ->label('Precio')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('€'),

                        Toggle::make('pagado')
                            ->label('Pago / No pago')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sin usuario')
                    ->formatStateUsing(fn (?string $state): string => mb_strtoupper((string) $state))
                    ->weight(FontWeight::Bold),

                TextColumn::make('downloaded_at')
                    ->label('Fecha')
                    ->formatStateUsing(
                        fn ($state): string => DisplayTimezone::formatDate($state),
                    )
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('downloaded_time')
                    ->label('Hora')
                    ->state(
                        fn (Descarga $record): ?string => DisplayTimezone::formatTime($record->downloaded_at),
                    )
                    ->badge()
                    ->color('success'),

                TextColumn::make('programa.progname')
                    ->label('Programas descargados')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn (?string $state): string => mb_strtoupper((string) $state))
                    ->placeholder('—')
                    ->extraCellAttributes(['class' => 'descarga-programa-cell'])
                    ->extraHeaderAttributes(['class' => 'descarga-programa-cell']),

                TextInputColumn::make('precio')
                    ->label('Precio')
                    ->type('number')
                    ->inputMode('decimal')
                    ->step('0.01')
                    ->rules(['nullable', 'numeric', 'min:0'])
                    ->suffix('€')
                    ->alignCenter()
                    ->extraCellAttributes(['class' => 'descarga-precio-cell'])
                    ->extraHeaderAttributes(['class' => 'descarga-precio-cell'])
                    ->extraInputAttributes(['class' => 'descarga-precio-input']),

                ToggleColumn::make('pagado')
                    ->label('Pago/NoPago')
                    ->sortable()
                    ->onColor('success')
                    ->offColor('danger')
                    ->alignCenter(),
            ])
            ->defaultSort('downloaded_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay descargas registradas')
            ->emptyStateDescription('Cada vez que un cliente descarga un programa desde Pedidos o el catálogo, aparecerá aquí.');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'programa']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDescargas::route('/'),
            'edit' => EditDescarga::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
