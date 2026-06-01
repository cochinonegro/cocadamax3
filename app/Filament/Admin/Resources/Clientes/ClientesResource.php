<?php

namespace App\Filament\Admin\Resources\Clientes;

use App\Filament\Admin\Resources\Clientes\Pages\CreateClientes;
use App\Filament\Admin\Resources\Clientes\Pages\EditClientes;
use App\Filament\Admin\Resources\Clientes\Pages\ListClientes;
use App\Filament\Admin\Resources\Clientes\Schemas\ClientesInfolist;
use App\Filament\Support\ClienteFormatting;
use App\Models\Clientes;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientesResource extends Resource
{
    protected static ?string $model = Clientes::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Datos del cliente')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('email')
                            ->label('Correo')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('nombre_whatsapp')
                            ->label('Nombre WhatsApp')
                            ->maxLength(255),

                        TextInput::make('ciudad')
                            ->label('Ciudad')
                            ->maxLength(100),
                    ]),

                Section::make('Programa solicitado')
                    ->schema([
                        TextInput::make('required_prog')
                            ->label('Programa requerido')
                            ->maxLength(255),

                        Select::make('os_required')
                            ->label('Sistema operativo')
                            ->options([
                                'windows' => 'Windows',
                                'mac' => 'Mac',
                                'win-mac' => 'Win & Mac',
                            ])
                            ->native(false),

                        Select::make('category')
                            ->label('Categoría')
                            ->options([
                                'aplicaciones' => 'Aplicaciones',
                                'diseño grafico' => 'Diseño gráfico',
                                'arquitectura' => 'Arquitectura',
                                'music' => 'Música',
                                'video' => 'Video',
                                'kontakt' => 'Kontakt',
                            ])
                            ->native(false),

                        TextInput::make('company')
                            ->label('Empresa / Marca')
                            ->maxLength(255),

                        TextInput::make('referencia')
                            ->label('Referencia')
                            ->maxLength(255),
                    ]),

                Section::make('Seguimiento')
                    ->schema([
                        DatePicker::make('date')
                            ->label('Fecha')
                            ->default(now())
                            ->native(false),

                        TextInput::make('publicidad')
                            ->label('Publicidad / origen')
                            ->maxLength(255),

                        TextInput::make('result_client')
                            ->label('Resultado')
                            ->maxLength(255),

                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('comentario_info_cliente')
                            ->label('Comentario interno')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(query: function ($query, string $search) {
                        $digits = ClienteFormatting::phoneDigits($search);

                        if ($digits === '') {
                            return $query;
                        }

                        return $query->where('phone', 'like', "%{$digits}%");
                    })
                    ->html()
                    ->formatStateUsing(fn (?string $state) => ClienteFormatting::phoneHtml($state)),

                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('required_prog')
                    ->label('Programa')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('os_required')
                    ->label('SO')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'windows' => 'Windows',
                        'mac' => 'Mac',
                        'win-mac' => 'Win & Mac',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'windows' => 'info',
                        'mac' => 'danger',
                        'win-mac' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('category')
                    ->label('Categoría')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Fecha registro')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('registration_time')
                    ->label('Hora')
                    ->state(
                        fn (Clientes $record): ?string => $record->created_at
                            ?->timezone(config('app.timezone'))
                            ->format('H:i'),
                    )
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('os_required')
                    ->label('Sistema operativo')
                    ->options([
                        'windows' => 'Windows',
                        'mac' => 'Mac',
                        'win-mac' => 'Win & Mac',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (Clientes $record): string => $record->name)
                    ->modalWidth(Width::ThreeExtraLarge),

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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientes::route('/'),
            'create' => CreateClientes::route('/create'),
            'edit' => EditClientes::route('/{record}/edit'),
        ];
    }
}
