<?php

namespace App\Filament\Concerns;

use App\Models\Programas;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

trait HasInstallOffAction
{
    protected function makeInstallOffAction(): Action
    {
        return Action::make('Install_Off')
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
            });
    }
}
