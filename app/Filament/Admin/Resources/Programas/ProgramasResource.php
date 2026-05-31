<?php

namespace App\Filament\Admin\Resources\Programas;

use App\Filament\Admin\Resources\Programas\Schemas\ProgramasForm;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use App\Filament\Admin\Resources\Programas\Pages\ListProgramas;
use App\Filament\Admin\Resources\Programas\Pages\CreateProgramas;
use App\Filament\Admin\Resources\Programas\Pages\EditProgramas;
use App\Models\Programas;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Support\ProgramasTableColumns;
use App\Filament\Support\ProgramaCategories;
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
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                ProgramasForm::wizard(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(ProgramasTableColumns::make(withStatus: true))
            ->reorderableColumns()
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options(ProgramaCategories::options())
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

                Action::make('win_mac')
                    ->label('WIN=MAC')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->tooltip(fn (Programas $record): string => match ($record->normalizedOsRequired()) {
                        'mac' => 'Crear duplicado para Windows',
                        'windows' => 'Crear duplicado para Mac',
                        default => 'Duplicar para el otro sistema operativo',
                    })
                    ->visible(fn (Programas $record): bool => filled($record->swappedOsRequired()))
                    ->requiresConfirmation()
                    ->modalHeading('Duplicar producto')
                    ->modalDescription(function (Programas $record): string {
                        $target = ProgramasTableColumns::osRequiredLabel($record->swappedOsRequired());

                        return "Se creará una copia idéntica con sistema {$target}. Todo lo demás permanece igual.";
                    })
                    ->action(function (Programas $record): void {
                        $duplicate = $record->duplicateWithSwappedOs();

                        $osLabel = ProgramasTableColumns::osRequiredLabel($duplicate->os_required);

                        Notification::make()
                            ->title('Producto duplicado')
                            ->body("Copia creada para {$osLabel}.")
                            ->success()
                            ->actions([
                                Action::make('editar')
                                    ->label('Editar copia')
                                    ->url(static::getUrl('edit', ['record' => $duplicate])),
                            ])
                            ->send();
                    }),

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