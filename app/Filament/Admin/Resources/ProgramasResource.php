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
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class ProgramasResource extends Resource
{
    protected static ?string $model = Programas::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Programas';
    protected static ?string $pluralModelLabel = 'Programas';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
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

                Forms\Components\Section::make('Detalles del Programa')
                    ->schema([
                        TextInput::make('progname')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

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
                                'kontakt' => 'Kontakt',
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

                TextColumn::make('id')
                    ->label('Código')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $padded = str_pad($state, 4, '0', STR_PAD_LEFT);
                        return substr($padded, 0, 2) . ' ' . substr($padded, 2, 2);
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('progname')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable()
                    ->limit(40),

                TextColumn::make('size')
                    ->badge()
                    ->label('Tamaño')
                    ->color('success')
                    ->sortable(),

                TextColumn::make('tipo_descarga')
                    ->label('DESCARGAR')
                    ->badge()
                    ->state(fn ($record) => $record->disk_name ? 'LOCAL USB' : 'URL WEB')
                    ->color(fn ($state) => $state === 'LOCAL USB' ? 'success' : 'pink')
                    ->icon(fn ($state) => $state === 'LOCAL USB' ? 'heroicon-o-server' : 'heroicon-o-globe-alt')
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab()
                    ->alignCenter(),

                BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'pink' => 'diseño grafico',
                        'info' => 'musica',
                        'orange' => 'kontakt',
                        'gray' => fn ($state) => ! in_array($state, ['diseño grafico', 'music', 'kontakt']),
                    ])
                    ->sortable(),
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
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->label('')
                    ->tooltip('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('')
                    ->tooltip('Eliminar'),

                Tables\Actions\Action::make('generar_link')
                    ->label('Link Cliente')
                    ->icon('heroicon-o-key')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Enlace Seguro')
                    ->modalDescription('El cliente descargará desde tu web, sin ver el enlace real.')
                    ->form([
                        Forms\Components\Select::make('duracion')
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
                        $dias = (int) $data['duracion'];
                        $fechaCaducidad = now()->addDays($dias);

                        $urlSegura = URL::temporarySignedRoute(
                            'cliente.descarga', 
                            $fechaCaducidad, 
                            ['id' => $record->id]
                        );

                        $htmlBody = <<<HTML
                            <div style="margin-top: 10px;">
                                <p style="margin-bottom: 5px; color: #aaa;">Caduca en $dias días:</p>
                                <div style="display: flex; gap: 8px;">
                                    <input type="text" 
                                           value="$urlSegura" 
                                           id="linkGenerado" 
                                           readonly 
                                           style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #444; background: #222; color: #fff; font-size: 13px;">
                                    
                                    <button type="button"
                                            onclick="
                                                var copyText = document.getElementById('linkGenerado');
                                                copyText.select();
                                                copyText.setSelectionRange(0, 99999);
                                                navigator.clipboard.writeText(copyText.value);
                                                alert('✅ ¡Copiado!');
                                            "
                                            style="background-color: #10b981; color: white; border: none; padding: 0 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">
                                        COPIAR
                                    </button>
                                </div>
                            </div>
                        HTML;

                        Notification::make()
                            ->title('✅ Enlace Generado')
                            ->success()
                            ->persistent()
                            ->body(new HtmlString($htmlBody))
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