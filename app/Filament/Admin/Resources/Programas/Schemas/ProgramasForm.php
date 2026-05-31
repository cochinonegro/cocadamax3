<?php

namespace App\Filament\Admin\Resources\Programas\Schemas;

use App\Filament\Support\ProgramaCategories;
use App\Filament\Support\ProgramaImageUpload;
use App\Filament\Support\ProgramasTableColumns;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Livewire\Component;

class ProgramasForm
{
    public static function syncOsRequired(Get $get, Set $set): void
    {
        $windows = (bool) $get('os_windows');
        $mac = (bool) $get('os_mac');

        $set('os_required', match (true) {
            $windows && $mac => 'win-mac',
            $windows => 'windows',
            $mac => 'mac',
            default => null,
        });
    }

    /** @param  array<string, mixed>  $data */
    public static function hydrateOsCheckboxes(array $data): array
    {
        $os = $data['os_required'] ?? null;
        $data['os_windows'] = in_array($os, ['windows', 'win-mac'], true);
        $data['os_mac'] = in_array($os, ['mac', 'win-mac'], true);

        return $data;
    }

    /** @param  array<string, mixed>  $data */
    public static function stripVirtualOsFields(array $data): array
    {
        unset($data['os_windows'], $data['os_mac']);

        return $data;
    }

    public static function wizard(): Wizard
    {
        return Wizard::make([
            self::detallesStep(),
            self::descripcionStep(),
            self::galeriaStep(),
            self::instalacionStep(),
        ])
            ->persistStepInQueryString('paso')
            ->columnSpanFull()
            ->nextAction(fn ($action) => $action->label('Guardar y continuar'))
            ->extraAttributes(['class' => 'programa-wizard']);
    }

    protected static function persistAfterValidation(): \Closure
    {
        return function (Component $livewire): void {
            if (method_exists($livewire, 'persistProgramaWizardStep')) {
                $livewire->persistProgramaWizardStep();
            }
        };
    }

    protected static function detallesStep(): Step
    {
        return Step::make('Detalles')
            ->key('detalles')
            ->icon('heroicon-o-clipboard-document-list')
            ->description('Datos principales del programa')
            ->afterValidation(self::persistAfterValidation())
            ->schema([
                Section::make('Detalles del programa')
                    ->extraAttributes(['class' => 'programa-detalles-section'])
                    ->schema([
                        Section::make('Identificación')
                            ->extraAttributes(['class' => 'programa-detalles-group'])
                            ->schema([
                                TextInput::make('progname')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                TextInput::make('url')
                                    ->label('Link descarga')
                                    ->placeholder('amazon.com')
                                    ->helperText('Solo el dominio o enlace. No hace falta escribir https://')
                                    ->required()
                                    ->maxLength(255)
                                    ->dehydrateStateUsing(fn (?string $state): ?string => ProgramasTableColumns::downloadUrl($state))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),

                        Section::make('Clasificación')
                            ->extraAttributes(['class' => 'programa-detalles-group'])
                            ->schema([
                                Select::make('category')
                                    ->label('Categoría')
                                    ->required()
                                    ->options(ProgramaCategories::options())
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('working', null))
                                    ->native(false)
                                    ->searchable(),

                                Select::make('working')
                                    ->label('Subcategoría')
                                    ->placeholder('Selecciona…')
                                    ->options(fn (Get $get): array => ProgramaCategories::subcategoryOptions($get('category')) ?? [])
                                    ->visible(fn (Get $get): bool => ProgramaCategories::hasSubcategories($get('category')))
                                    ->required(fn (Get $get): bool => ProgramaCategories::hasSubcategories($get('category')))
                                    ->native(false),

                                TextInput::make('working')
                                    ->label('Subcategoría')
                                    ->placeholder('Opcional')
                                    ->visible(fn (Get $get): bool => ! ProgramaCategories::hasSubcategories($get('category')))
                                    ->maxLength(255),

                                Hidden::make('os_required')
                                    ->default('windows')
                                    ->dehydrated()
                                    ->required(),

                                Checkbox::make('os_windows')
                                    ->label('Windows')
                                    ->default(true)
                                    ->dehydrated(false)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::syncOsRequired($get, $set)),

                                Checkbox::make('os_mac')
                                    ->label('Mac')
                                    ->default(false)
                                    ->dehydrated(false)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::syncOsRequired($get, $set)),

                                Select::make('idioma')
                                    ->label('Idioma')
                                    ->placeholder('Selecciona…')
                                    ->options([
                                        'multi' => 'Multi',
                                        'es' => 'Español',
                                        'en' => 'Inglés',
                                        'fr' => 'Francés',
                                        'de' => 'Alemán',
                                    ])
                                    ->native(false),
                            ])
                            ->columns(1),

                        Section::make('Ficha técnica')
                            ->extraAttributes(['class' => 'programa-detalles-group'])
                            ->schema([
                                TextInput::make('program_id')
                                    ->label('Código')
                                    ->required(),

                                TextInput::make('size')
                                    ->label('Tamaño')
                                    ->required()
                                    ->maxLength(50),

                                TextInput::make('year_prog')
                                    ->label('Año')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1990)
                                    ->maxValue(date('Y')),

                                TextInput::make('level_inst')
                                    ->label('Tipo/archivo')
                                    ->placeholder('Ej: zip file')
                                    ->maxLength(255),

                                TextInput::make('company')
                                    ->label('Marca')
                                    ->maxLength(255),

                                TextInput::make('web_oficial')
                                    ->label('Web oficial')
                                    ->placeholder('https://www.ejemplo.com')
                                    ->maxLength(255),

                                TextInput::make('required')
                                    ->label('Requerido')
                                    ->placeholder('Ej: Windows 11')
                                    ->maxLength(255),

                                DatePicker::make('date_add')
                                    ->label('Fecha')
                                    ->default(now())
                                    ->required(),
                            ])
                            ->columns(1),

                        Section::make('Visibilidad')
                            ->extraAttributes(['class' => 'programa-detalles-group'])
                            ->schema([
                                Toggle::make('show')
                                    ->label('Visible para clientes')
                                    ->default(true)
                                    ->dehydrated()
                                    ->live(),

                                DateTimePicker::make('show_until')
                                    ->label('Visible hasta')
                                    ->default(now()->addYear())
                                    ->required(fn (Get $get) => (bool) $get('show'))
                                    ->visible(fn (Get $get) => (bool) $get('show'))
                                    ->columnSpan([
                                        'default' => 1,
                                        'sm' => 2,
                                    ]),
                            ])
                            ->columns([
                                'default' => 1,
                                'sm' => 2,
                            ])
                            ->columnSpanFull(),

                        Section::make('Ubicación del archivo')
                            ->extraAttributes(['class' => 'programa-detalles-group programa-detalles-group--last'])
                            ->collapsed()
                            ->description('Opcional. Solo si el archivo está en un disco local.')
                            ->schema([
                                Select::make('disk_name')
                                    ->label('Disco local')
                                    ->options([
                                        'disco_sibi' => 'Disco SIBI',
                                        'disco_laila' => 'Disco LAILA',
                                        'disco_hdd' => 'Disco HDD',
                                        'disco_data' => 'Disco DATA',
                                    ])
                                    ->placeholder('Selecciona un disco…')
                                    ->native(false)
                                    ->live(),

                                TextInput::make('file_path')
                                    ->label('Ruta del archivo')
                                    ->placeholder('Ej: AA PROGRAMAS/Installer.zip')
                                    ->required(fn (Get $get) => filled($get('disk_name')))
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
            ]);
    }

    protected static function descripcionStep(): Step
    {
        return Step::make('Descripción')
            ->key('descripcion')
            ->icon('heroicon-o-document-text')
            ->description('Texto que verá el cliente')
            ->afterValidation(self::persistAfterValidation())
            ->schema([
                MarkdownEditor::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
            ]);
    }

    protected static function galeriaStep(): Step
    {
        return Step::make('Galería')
            ->key('galeria')
            ->icon('heroicon-o-photo')
            ->description('Imágenes del producto (máx. 4)')
            ->afterValidation(self::persistAfterValidation())
            ->schema([
                Section::make('Galería del producto')
                    ->description('Espera a que termine la subida antes de continuar, sobre todo desde el móvil.')
                    ->schema([
                        ProgramaImageUpload::gallery(),
                    ]),
            ]);
    }

    protected static function instalacionStep(): Step
    {
        return Step::make('Instalación')
            ->key('instalacion')
            ->icon('heroicon-o-wrench-screwdriver')
            ->description('Guía de instalación para clientes')
            ->schema([
                Section::make('Instalación')
                    ->schema([
                        Toggle::make('show_instalador')
                            ->label('Instalador visible para clientes')
                            ->default(false)
                            ->dehydrated()
                            ->live()
                            ->columnSpanFull(),

                        MarkdownEditor::make('info_install')
                            ->label('Información general')
                            ->columnSpanFull(),

                        ProgramaImageUpload::installerPhoto()
                            ->helperText('Visible en la guía de instalación para clientes.')
                            ->columnSpanFull(),

                        Repeater::make('installation_steps')
                            ->label('Pasos de instalación')
                            ->defaultItems(0)
                            ->addActionLabel('Agregar paso')
                            ->reorderable()
                            ->itemLabel(fn (array $state): ?string => filled($state['title'] ?? null)
                                ? $state['title']
                                : 'Nuevo paso')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Título')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Textarea::make('description')
                                    ->label('Descripción')
                                    ->rows(4)
                                    ->required()
                                    ->columnSpanFull(),
                                ProgramaImageUpload::installationStep()
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->columnSpanFull(),

                        TextInput::make('video_instalador')
                            ->label('Video instalador')
                            ->placeholder('youtube.com/watch?v=... o amazon.com')
                            ->helperText('Enlace al video de instalación. No hace falta escribir https://')
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn (?string $state): ?string => ProgramasTableColumns::downloadUrl($state))
                            ->visible(fn (Get $get): bool => (bool) $get('show_instalador'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
