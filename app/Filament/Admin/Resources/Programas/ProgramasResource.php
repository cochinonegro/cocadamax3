<?php

namespace App\Filament\Admin\Resources\Programas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use App\Filament\Admin\Resources\Programas\Pages\ListProgramas;
use App\Filament\Admin\Resources\Programas\Pages\CreateProgramas;
use App\Filament\Admin\Resources\Programas\Pages\EditProgramas;
use App\Models\Programas;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\ProgramasResource\Pages;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Filament\Support\ProgramasTableColumns;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class ProgramasResource extends Resource
{
    protected static ?string $model = Programas::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Programas';
    protected static ?string $pluralModelLabel = 'Programas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Ubicación del Archivo')
                    ->columnSpanFull()
                    ->collapsed()
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
                            ->required(fn (Get $get) => $get('disk_name') !== null)
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Detalles del Programa')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('progname')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('url')
                            ->label('Link Descarga')
                            ->placeholder('https://mega.nz/...')
                            ->url()
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

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

                        TextInput::make('company')
                            ->label('Marca')
                            ->maxLength(255),

                        TextInput::make('web_oficial')
                            ->label('Web Oficial')
                            ->placeholder('https://www.ejemplo.com')
                            ->maxLength(255),

                        TextInput::make('required')
                            ->label('Requerido')
                            ->placeholder('Ej: Windows 11')
                            ->maxLength(255),

                        Select::make('idioma')
                            ->label('Idioma')
                            ->options([
                                'multi' => 'Multi',
                                'es' => 'Español',
                                'en' => 'Inglés',
                                'fr' => 'Francés',
                                'de' => 'Alemán',
                            ])
                            ->native(false),

                        TextInput::make('level_inst')
                            ->label('Tipo / Archivo')
                            ->placeholder('Ej: zip file')
                            ->maxLength(255),

                        DatePicker::make('date_add')
                            ->label('Fecha')
                            ->default(now())
                            ->required(),

                        Toggle::make('show')
                            ->label('Visible para clientes')
                            ->default(true)
                            ->dehydrated()
                            ->live()
                            ->columnSpanFull(),

                        DateTimePicker::make('show_until')
                            ->label('Visible hasta')
                            ->default(now()->addYear())
                            ->required(fn (Get $get) => (bool) $get('show'))
                            ->visible(fn (Get $get) => (bool) $get('show'))
                            ->minDate(now())
                            ->columnSpanFull(),
                    ])->columns(3),

                Section::make('Descripción')
                    ->columnSpanFull()
                    ->schema([
                        MarkdownEditor::make('description')
                            ->label('Descripción')
                            ->columnSpanFull(),
                    ]),

                Section::make('Galería del producto')
                    ->columnSpanFull()
                    ->description('Hasta 4 imágenes que verá el cliente a la izquierda de la ficha.')
                    ->schema([
                        FileUpload::make('gallery_images')
                            ->label('Imágenes')
                            ->image()
                            ->multiple()
                            ->maxFiles(4)
                            ->reorderable()
                            ->disk('public')
                            ->directory('programas/gallery')
                            ->columnSpanFull(),
                    ]),

                Section::make('INSTALACION')
                    ->columnSpanFull()
                    ->collapsed()
                    ->schema([
                        MarkdownEditor::make('info_install')
                            ->label('Información sobre esta instalación')
                            ->columnSpanFull(),

                        Repeater::make('installation_steps')
                            ->label('Pasos de instalación')
                            ->maxItems(4)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar paso')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Foto')
                                    ->image()
                                    ->disk('public')
                                    ->directory('programas/instalacion')
                                    ->required(),
                                Textarea::make('text')
                                    ->label('Instrucciones')
                                    ->rows(5)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(ProgramasTableColumns::make(withStatus: true))
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
            ->recordActions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->label('')
                    ->tooltip('Editar'),
                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('')
                    ->tooltip('Eliminar'),

                Action::make('generar_link')
                    ->label('Link Cliente')
                    ->icon('heroicon-o-key')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generar Enlace Seguro')
                    ->modalDescription('El cliente descargará desde tu web, sin ver el enlace real.')
                    ->schema([
                        Select::make('duracion')
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

                        $urlSeguraJS = addslashes($urlSegura);

                        Notification::make()
                            ->title('✅ Enlace Generado')
                            ->success()
                            ->persistent()
                            ->body(new HtmlString("
                                <div x-data=\"{ url: '{$urlSeguraJS}' }\" style='margin-top: 10px;'>
                                    <p style='margin-bottom: 8px; color: #aaa; font-size: 0.9em;'>Caduca en {$dias} días:</p>
                                    <div style='display: flex; gap: 8px;'>
                                        <input type='text' 
                                               x-model='url' 
                                               readonly 
                                               style='width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #444; background: #222; color: #fff; font-size: 13px;'>
                                        
                                        <button type='button'
                                                x-on:click=\"
                                                    window.navigator.clipboard.writeText(url);
                                                    alert('✅ ¡Enlace copiado al portapapeles!');
                                                \"
                                                style='background-color: #10b981; color: white; border: none; padding: 0 15px; border-radius: 6px; cursor: pointer; font-weight: bold; white-space: nowrap;'>
                                            COPIAR
                                        </button>
                                    </div>
                                </div>
                            "))
                            ->send();
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProgramas::route('/'),
            'create' => CreateProgramas::route('/create'),
            'edit' => EditProgramas::route('/{record}/edit'),
        ];
    }
}