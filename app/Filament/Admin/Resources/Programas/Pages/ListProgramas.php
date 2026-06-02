<?php

namespace App\Filament\Admin\Resources\Programas\Pages;

use App\Filament\Admin\Resources\Programas\ProgramasResource;
use App\Filament\Concerns\HasInstallOffAction;
use App\Filament\Concerns\HasPedidosOffAction;
use App\Filament\Concerns\HasProgramasOsTabs;
use App\Filament\Concerns\PersistsTableColumnsForAuthenticatedUser;
use App\Models\AppSetting;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListProgramas extends ListRecords
{
    use HasInstallOffAction;
    use HasPedidosOffAction;
    use HasProgramasOsTabs;
    use PersistsTableColumnsForAuthenticatedUser;

    protected static string $resource = ProgramasResource::class;

    protected function shouldIncludeProgramasTodosTab(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('viewPrice')
                ->label('ViewPrice')
                ->icon('heroicon-o-eye')
                ->color('success')
                ->action(function (): void {
                    $isEnabled = AppSetting::getBool(AppSetting::CLIENTES_VIEW_PRICE, false);
                    $next = ! $isEnabled;

                    AppSetting::setBool(AppSetting::CLIENTES_VIEW_PRICE, $next);

                    Notification::make()
                        ->title('ViewPrice actualizado')
                        ->body($next
                            ? 'Precios visibles para Clientes y Tienda.'
                            : 'Precios ocultos para Clientes y Tienda.')
                        ->success()
                        ->send();
                }),
            $this->makeInstallOffAction(),
            $this->makePedidosOffAction(),
        ];
    }
}
