<?php

namespace App\Filament\Admin\Resources\Programas\Pages;

use App\Filament\Admin\Resources\Programas\ProgramasResource;
use App\Filament\Concerns\HasProgramasOsTabs;
use App\Models\Programas;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListProgramas extends ListRecords
{
    use HasProgramasOsTabs;

    protected static string $resource = ProgramasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Install_Off')
                ->label('Install_Off')
                ->icon('heroicon-o-eye-slash')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Ocultar instaladores')
                ->modalDescription('Se desactivará el instalador en todos los programas. Los clientes dejarán de ver el procedimiento de instalación.')
                ->action(function (): void {
                    Programas::query()->update(['show_instalador' => false]);

                    Notification::make()
                        ->title('Instaladores ocultados')
                        ->body('El procedimiento de instalación quedó desactivado en todos los programas.')
                        ->success()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
