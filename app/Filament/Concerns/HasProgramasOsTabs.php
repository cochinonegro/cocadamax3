<?php

namespace App\Filament\Concerns;

use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

trait HasProgramasOsTabs
{
    public function getTabs(): array
    {
        return $this->buildProgramasOsTabs(
            includeTodos: $this->shouldIncludeProgramasTodosTab(),
        );
    }

    protected function shouldIncludeProgramasTodosTab(): bool
    {
        return false;
    }

    /**
     * @return array<string, Tab>
     */
    protected function buildProgramasOsTabs(bool $includeTodos = false): array
    {
        $tabs = [];

        if ($includeTodos) {
            $tabs['todos'] = Tab::make('Todos')
                ->icon('heroicon-o-squares-2x2')
                ->extraAttributes([
                    'class' => 'programas-os-tab programas-os-tab--todos',
                ])
                ->badge(fn (): int => $this->countAllProgramasForTabs())
                ->badgeColor('gray');
        }

        return array_merge($tabs, [
            'windows' => $this->makeProgramasOsTab(
                label: 'Windows',
                icon: 'heroicon-o-computer-desktop',
                cssClass: 'programas-os-tab programas-os-tab--windows',
                osValues: ['windows', 'win-mac'],
                badgeColor: 'info',
            ),

            'mac' => $this->makeProgramasOsTab(
                label: 'Mac',
                icon: 'heroicon-o-device-tablet',
                cssClass: 'programas-os-tab programas-os-tab--mac',
                osValues: ['mac', 'win-mac'],
                badgeColor: 'danger',
            ),
        ]);
    }

    protected function countAllProgramasForTabs(): int
    {
        /** @var ListRecords $this */
        return static::getResource()::getEloquentQuery()->count();
    }

    /**
     * @param  list<string>  $osValues
     */
    protected function makeProgramasOsTab(
        string $label,
        string $icon,
        string $cssClass,
        array $osValues,
        string $badgeColor,
    ): Tab {
        return Tab::make($label)
            ->icon($icon)
            ->extraAttributes([
                'class' => $cssClass,
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('os_required', $osValues))
            ->badge(fn (): int => $this->countProgramasForOsTab($osValues))
            ->badgeColor($badgeColor);
    }

    /**
     * @param  list<string>  $osValues
     */
    protected function countProgramasForOsTab(array $osValues): int
    {
        /** @var ListRecords $this */
        return static::getResource()::getEloquentQuery()
            ->whereIn('os_required', $osValues)
            ->count();
    }
}
