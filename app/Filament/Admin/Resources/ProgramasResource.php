<?php

namespace App\Filament\Admin\Resources;

use App\Models\Programas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\ProgramasResource\Pages;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\URL;


class ProgramasResource extends Resource
{
    protected static ?string $model = Programas::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Programas';
    protected static ?string $pluralModelLabel = 'Programas';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3) // Usamos 3 columnas para que se adapte mejor
            ->schema([

                // --- SECCIÓN 1: UBICACIÓN (Aquí está la lógica nueva) ---
                Forms\Components\Section::make('Ubicación del Archivo')
                    ->description('Selecciona si el archivo está en tus discos locales o en una web externa.')
                    ->schema([
                        Select::make('disk_name')
                            ->label('Disco Local')
                            ->options([
                                'disco_sibi' => 'Disco SIBI',
                                'disco_laila' => 'Disco LAILA',
                                'disco_hdd' => 'Disco HDD',
                                'disco_data' => 'Disco DATA',
                            ])
                            ->placeholder('Selecciona un disco...')
                            ->native(false)
                            ->reactive(),

                        TextInput::make('file_path')
                            ->label('Ruta del Archivo')
                            ->placeholder('Ej: AA PROGRAMAS/Installer.zip')
                            ->hint('Copia la ruta exacta dentro del disco')
                            ->required(fn (Forms\Get $get) => $get('disk_name') !== null)
                            ->maxLength(255),

                        TextInput::make('url')
                            ->label('Link Externo (Alternativo)')
                            ->placeholder('https://mega.nz/...')
                            ->url()
                            ->helperText('Solo si NO usas discos locales'),
                    ])->columns(2),

                // --- SECCIÓN 2: DETALLES (Aquí moví tus campos antiguos) ---
                Forms\Components\Section::make('Detalles del Programa')
                    ->schema([
                        TextInput::make('progname')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2), // Ocupa 2 espacios para verse mejor

                        TextInput::make('program_id')
                            ->label('Código')
                            ->required(),

                        TextInput::make('size')
                            ->label('Tamaño')
                            ->required()
                            ->maxLength(50),

                        Select::make('category')
                            ->label('Categoría')
                            ->required()
                            ->options([
                                'aplicaciones' => 'Aplicaciones',
                                'diseño grafico' => 'Diseño gráfico',
                                'arquitectura' => 'Arquitectura',
                                'music' => 'Música',
                                'video' => 'Video',
                                'kontakt' => 'kontakt',
                            ])
                            ->native(false),

                        TextInput::make('working')
                            ->label('Subcategoría')
                            ->required()
                            ->maxLength(255),

                        Select::make('os_required')
                            ->label('Sistema Operativo')
                            ->required()
                            ->options([
                                'windows' => 'Windows',
                                'mac' => 'Mac',
                                'win-mac' => 'Win & Mac',
                            ])
                            ->native(false),

                        TextInput::make('year_prog')
                            ->label('Año del programa')
                            ->required()
                            ->numeric()
                            ->minValue(1990)
                            ->maxValue(date('Y')),

                        TextInput::make('level_inst')
                            ->label('Tags Referencia')
                            ->required(),

                        DatePicker::make('date_add')
                            ->label('Fecha de Alta')
                            ->default(now())
                            ->required(),
                    ])->columns(3),

                // --- SECCIÓN 3: DESCRIPCIÓN ---
                Forms\Components\Section::make()
                    ->schema([
                        MarkdownEditor::make('description')
                            ->label('Descripción del Producto')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                // 1. STATUS (Switch)
                ToggleColumn::make('show')
                    ->label('STATUS')
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            $record->update(['show_until' => now()->addMinutes(10)]);
                        } else {
                            $record->update(['show_until' => null]);
                        }
                    }),

                // 2. SISTEMA OPERATIVO
                BadgeColumn::make('os_required')
                    ->label('Sist.Op')
                    ->colors([
                        'info' => 'windows',
                        'danger' => 'mac',
                        'gray' => 'win-mac',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'windows' => 'Windows',
                        'mac' => 'Mac',
                        'win-mac' => 'Win & Mac',
                        default => strtoupper($state),
                    }),

                // 3. CÓDIGO
                TextColumn::make('id')
                    ->label('Código')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $padded = str_pad($state, 4, '0', STR_PAD_LEFT);
                        return substr($padded, 0, 2) . ' ' . substr($padded, 2, 2);
                    })
                    ->badge()
                    ->color('info'),

                // 4. NOMBRE
                TextColumn::make('progname')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable()
                    ->limit(40),

                // 5. TAMAÑO
                TextColumn::make('size')
                    ->badge()
                    ->label('Tamaño')
                    ->color('success')
                    ->sortable(),

                // --- AQUÍ ESTÁ EL CAMBIO IMPORTANTE: EL BOTÓN DE DESCARGA ---
               TextColumn::make('tipo_descarga')
                    ->label('DESCARGAR')
                    ->badge()
                    // Definimos manualmente el "Estado" (el texto que se ve)
                    ->state(fn ($record) => $record->disk_name ? 'LOCAL USB' : 'URL WEB')

                    // Colores según el texto que acabamos de definir
                    ->color(fn ($state) => $state === 'LOCAL USB' ? 'success' : 'pink')

                    // Iconos
                    ->icon(fn ($state) => $state === 'LOCAL USB' ? 'heroicon-o-server' : 'heroicon-o-globe-alt')

                    // La acción de clic (Mantenemos tu lógica perfecta)
                    ->url(fn ($record) => $record->url)
                    //->url(fn ($record) => route('download.local', ['id' => $record->id]))
                    ->openUrlInNewTab()
                    ->alignCenter(),
                // 6. CATEGORÍA
                BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'pink' => 'diseño grafico',
                        'info' => 'musica',
                        'orange' => 'kontakt',
                        'gray' => fn ($state) => ! in_array($state, ['diseño grafico', 'music', 'kontakt']),
                    ])
                    ->sortable(),

                // Ocultamos fecha y año por defecto para no saturar, pero se pueden activar
                BadgeColumn::make('year_prog')
                    ->label('Año')
                    ->color('orange')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('date_add')
                    ->label('Fecha Alta')
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->actions([
                // --- TUS BOTONES ORIGINALES ---
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->label('')
                    ->tooltip('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('')
                    ->tooltip('Eliminar'),

                // --- NUEVO BOTÓN: GENERAR LINK CLIENTE ---
                \Filament\Tables\Actions\Action::make('generar_link')
                    ->label('Link Cliente')
                    ->icon('heroicon-o-key')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Enlace Seguro')
                    ->modalDescription('El cliente descargará desde tu web, sin ver el enlace real.')
                    ->form([
                        \Filament\Forms\Components\Select::make('duracion')
                            ->label('Caducidad del enlace')
                            ->options([
                                '1' => '24 Horas',
                                '3' => '3 Días',
                                '7' => '1 Semana',
                            ])
                            ->default('3')
                            ->required(),
                    ])
                   ->action(function ($record, array $data) {
                        // 1. Calcular fecha
                        $dias = (int) $data['duracion'];
                        $fechaCaducidad = now()->addDays($dias);

                        // 2. Generar la URL firmada que apunta a TU web.php
                        $urlSegura = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                            'cliente.descarga', 
                            $fechaCaducidad, 
                            ['id' => $record->id]
                        );

                        // 3. Notificación (Corregida para copiar bien)
                        \Filament\Notifications\Notification::make()
                            ->title('✅ Enlace Oculto Generado')
                            ->body("Este enlace caduca en $dias días.")
                            ->success()
                            ->persistent()
                            ->actions([
                                \Filament\Notifications\Actions\Action::make('copiar')
                                    ->label('Copiar URL')
                                    ->button()
                                    ->close()
                                    // EL TRUCO ESTÁ AQUÍ: Pasamos la variable $urlSegura dentro de las comillas
                                    ->extraAttributes(['onclick' => "window.navigator.clipboard.writeText('$urlSegura'); alert('Copiado');"]),
                            ])
                            ->send();
                    }),
                    
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProgramas::route('/'),
            'create' => Pages\CreateProgramas::route('/create'),
            'edit' => Pages\EditProgramas::route('/{record}/edit'),
        ];
    }
}
