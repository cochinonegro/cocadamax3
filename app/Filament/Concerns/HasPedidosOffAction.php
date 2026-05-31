<?php

namespace App\Filament\Concerns;

use App\Support\PedidosVisibility;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

trait HasPedidosOffAction
{
    protected function makePedidosOffAction(): Action
    {
        return Action::make('OFF')
            ->label('OFF')
            ->icon('heroicon-o-eye-slash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Ocultar todos en Pedidos')
            ->modalDescription('Ningún producto aparecerá en la tabla Pedidos de los clientes hasta que actives productos individualmente desde Programas o Cards Programas (30 minutos).')
            ->action(function (): void {
                PedidosVisibility::hideAll();

                Notification::make()
                    ->title('Pedidos ocultados')
                    ->body('Ningún producto está visible en la tabla Pedidos.')
                    ->success()
                    ->send();
            });
    }
}
