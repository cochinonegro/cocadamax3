<?php

namespace App\Filament\Clientes\Resources\Programas\Pages;

use App\Filament\Clientes\Pages\Tienda\TiendaElegirOs;
use App\Filament\Clientes\Resources\Programas\ProgramasResource;
use App\Filament\Concerns\HasProgramasOsTabs;
use App\Filament\Concerns\HasSolicitudPedidosCountdownModal;
use App\Filament\Support\ProgramaCategories;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class ListProgramas extends ListRecords
{
    use HasProgramasOsTabs;
    use HasSolicitudPedidosCountdownModal;

    protected static string $resource = ProgramasResource::class;

    public ?string $categoryFilter = null;

    public string $programaSearch = '';

    public function getTabs(): array
    {
        return $this->buildProgramasOsTabs(includeTodos: true);
    }

    public function updatedProgramaSearch(): void
    {
        $this->resetPage();
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'todos';
    }

    protected function loadDefaultActiveTab(): void
    {
        $this->activeTab = $this->getDefaultActiveTab();
    }

    public function updatedActiveTab(): void
    {
        $this->categoryFilter = null;
        $this->programaSearch = '';
    }

    public function toggleCategoryFilter(string $category): void
    {
        if (! array_key_exists($category, ProgramaCategories::options())) {
            return;
        }

        $this->categoryFilter = $this->categoryFilter === $category ? null : $category;
        $this->resetPage();
    }

    public function clearCategoryFilter(): void
    {
        $this->categoryFilter = null;
        $this->resetPage();
    }

    protected function applyCategoryFilterToTableQuery(Builder $query): Builder
    {
        if (blank($this->categoryFilter)) {
            return $query;
        }

        return $query->where('category', $this->categoryFilter);
    }

    protected function applyProgramaSearchToTableQuery(Builder $query): Builder
    {
        $term = trim($this->programaSearch);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function (Builder $inner) use ($like): void {
            $inner
                ->where('progname', 'like', $like)
                ->orWhere('company', 'like', $like)
                ->orWhere('program_id', 'like', $like);
        });
    }

    protected function getTableQuery(): Builder | Relation | null
    {
        $query = parent::getTableQuery();

        if (! $query instanceof Builder) {
            return $query;
        }

        return $this->applyProgramaSearchToTableQuery(
            $this->applyCategoryFilterToTableQuery($query),
        );
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
                View::make('filament.clientes.partials.programas-buscar'),
                View::make('filament.clientes.partials.programas-categorias-collapse')
                    ->visible(fn (): bool => in_array($this->activeTab, ['windows', 'mac'], true)),
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
                ->color('success')
                ->url(TiendaElegirOs::getUrl()),
        ];
    }
}
