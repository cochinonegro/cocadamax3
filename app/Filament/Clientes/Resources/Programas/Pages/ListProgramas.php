<?php

namespace App\Filament\Clientes\Resources\Programas\Pages;

use App\Filament\Clientes\Pages\Tienda\TiendaElegirOs;
use App\Filament\Clientes\Resources\Pedidos\PedidosResource;
use App\Filament\Clientes\Resources\Programas\ProgramasResource;
use App\Filament\Concerns\HasProgramasOsTabs;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;
use Livewire\Attributes\On;

class ListProgramas extends ListRecords
{
    use HasProgramasOsTabs;

    protected static string $resource = ProgramasResource::class;

    public bool $solicitudPedidosModalHabilitado = false;

    public function getTabs(): array
    {
        return $this->buildProgramasOsTabs(includeTodos: true);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(),
                Text::make('Estás viendo sólo programas de Windows ')
                    ->color(null)
                    ->weight('bold')
                    ->visible(fn (): bool => $this->activeTab === 'windows')
                    ->extraAttributes([
                        'class' => 'programas-os-tab-notice programas-os-tab-notice--windows',
                    ]),
                Text::make('Estás viendo sólo programas para Mac ')
                    ->color(null)
                    ->weight('bold')
                    ->visible(fn (): bool => $this->activeTab === 'mac')
                    ->extraAttributes([
                        'class' => 'programas-os-tab-notice programas-os-tab-notice--mac',
                    ]),
                Text::make('Estás viendo todos los programas disponibles ')
                    ->color(null)
                    ->weight('bold')
                    ->visible(fn (): bool => $this->activeTab === 'todos')
                    ->extraAttributes([
                        'class' => 'programas-os-tab-notice programas-os-tab-notice--todos',
                    ]),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ver_tienda')
                ->label('VER TIENDA')
                ->icon('heroicon-o-building-storefront')
                ->color('primary')
                ->url(TiendaElegirOs::getUrl()),
        ];
    }

    #[On('solicitud-enviada')]
    public function abrirModalSolicitudPedidos(): void
    {
        $this->solicitudPedidosModalHabilitado = false;
        $this->mountAction('solicitudSolicitada');
    }

    public function habilitarBotonPedidosModal(): void
    {
        $this->solicitudPedidosModalHabilitado = true;
    }

    public function solicitudSolicitadaAction(): Action
    {
        return Action::make('solicitudSolicitada')
            ->modalHeading('Solicitud enviada, ESPERA LA CONFIRMACIÓN')
            ->modalContent(fn () => view('filament.clientes.modals.solicitud-pedidos-countdown'))
            ->modalSubmitAction(
                fn (): Action|bool => $this->solicitudPedidosModalHabilitado
                    ? Action::make('irPedidos')
                        ->label('DESCARGAR')
                        ->color('success')
                        ->action(fn () => $this->redirect(PedidosResource::getUrl(), navigate: true))
                    : false,
            )
            ->modalCancelAction(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalWidth('md');
    }
}
