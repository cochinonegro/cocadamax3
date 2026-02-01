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

class ProgramasResource extends Resource
{
    protected static ?string $model = Programas::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Programas';
    protected static ?string $pluralModelLabel = 'Programas';

    public static function form(Form $form): Form
    {
        return $form
        ->columns(5)
            ->schema([
                TextInput::make('progname')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('url')
                    ->label('Link Descarga')
                    ->url()
                    ->required()
                    ->maxLength(255),


                TextInput::make('working')
                    ->label('Subcategoría')
                    ->required()
                    ->maxLength(255),

                TextInput::make('program_id')
                    ->label('Código')
                    ->required(),

                TextInput::make('level_inst')
                    ->label('Tags Referencia')
                    ->required(),

                Select::make('os_required')
                    ->label('Sistema Operativo')
                    ->required()
                    ->options([
                        'windows' => 'Windows',
                        'mac' => 'Mac',
                        'win-mac' => 'Win & Mac',
                    ])
                    ->native(false),

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

                TextInput::make('year_prog')
                    ->label('Año del programa')
                    ->required()
                    ->numeric()
                    ->minValue(1990)
                    ->maxValue(date('Y')),

                TextInput::make('size')
                    ->label('Tamaño')
                    ->required()
                    ->maxLength(50),

                //TextInput::make('description')
                //    ->required(),

                DatePicker::make('date_add')
                    ->label('Fecha de Alta')
                    ->default(now())
                    ->required(),

                MarkdownEditor::make('description')
                    ->label('Descripción del Producto')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('program_id')->label('Código'),

                ToggleColumn::make('show')
                    ->label('STATUS')
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            $record->update([
                                'show_until' => now()->addMinutes(10),
                            ]);
                        } else {
                            $record->update([
                                'show_until' => null,
                            ]);
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
                        $padded = str_pad($state, 4, '0', STR_PAD_LEFT); // Ej: 0007
                        return substr($padded, 0, 2) . ' ' . substr($padded, 2, 2); // Ej: 00 07
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('progname')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable()
                    ->limit(40), // corta el texto,

                TextColumn::make('size')
                    ->badge()
                    ->label('Tamaño')
                    ->color('success')
                    ->sortable(),

                TextColumn::make('url')
                    ->label('DESCARGAR')
                    ->badge()
                    ->color('pink')
                    ->formatStateUsing(fn () => 'DESCARGAR')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyMessage('¡Enlace copiado!')
                    ->copyMessageDuration(1500)
                    ->url(fn ($record) => $record->url, true), // nueva pestaña



                BadgeColumn::make('category')
                    ->label('Categoría')
                    ->colors([
                        'pink' => 'diseño grafico',
                        'info' => 'musica',
                        'orange' => 'kontakt',
                        'gray' => fn ($state) => ! in_array($state, ['diseño grafico', 'music', 'kontakt']),
                    ])
                    ->sortable(),

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
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->label('')
                    ->tooltip('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('')
                    ->tooltip('Eliminar'),
            ])
            // ->actionsPosition(ActionsPosition::After)
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
