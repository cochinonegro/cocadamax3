<?php

namespace App\Filament\Concerns;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

trait HasProgramasOsTabs
{
    public function getTabs(): array
    {
        return [
            'windows' => Tab::make('Windows')
                ->icon('heroicon-o-computer-desktop')
                ->extraAttributes([
                    'class' => 'programas-os-tab programas-os-tab--windows',
                ])
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('os_required', ['windows', 'win-mac'])),

            'mac' => Tab::make('Mac')
                ->icon('heroicon-o-device-tablet')
                ->extraAttributes([
                    'class' => 'programas-os-tab programas-os-tab--mac',
                ])
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('os_required', ['mac', 'win-mac'])),
        ];
    }
}
