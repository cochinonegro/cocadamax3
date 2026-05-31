<?php

namespace App\Providers;

use App\Enums\UserRole;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        FilamentColor::register([
            'blue' => Color::Blue,
            'sky' => Color::Sky,
            'cyan' => Color::Cyan,
            'teal' => Color::Teal,
            'green' => Color::Green,
            'amber' => Color::Amber,
            'yellow' => Color::Yellow,
            'orange' => Color::Orange,
            'rose' => Color::Rose,
            'pink' => Color::Pink,
            'fuchsia' => Color::Fuchsia,
            'violet' => Color::Violet,
            'indigo' => Color::Indigo,
            'purple' => Color::Purple,
        ]);

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch): void {
            $panelSwitch
                ->panels(['admin', 'clientes'])
                ->modalHeading('Cambiar panel')
                ->labels([
                    'admin' => 'Administración',
                    'clientes' => 'Vista Cliente',
                ])
                ->icons([
                    'admin' => 'heroicon-o-cog-6-tooth',
                    'clientes' => 'heroicon-o-users',
                ])
                ->simple();
        });
    }
}
