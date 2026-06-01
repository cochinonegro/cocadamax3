<?php

namespace App\Providers;

use App\Http\Responses\Filament\AdminLoginResponse;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Carbon\Carbon;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoginResponseContract::class, AdminLoginResponse::class);
    }

    public function boot(): void
    {
        App::setLocale(config('app.locale', 'es'));
        Carbon::setLocale(config('app.locale', 'es'));

        Authenticate::redirectUsing(function (): string {
            if (request()->is('clientes', 'clientes/*')) {
                return route('welcome');
            }

            return route('login');
        });

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

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => auth()->check()
                ? view('filament.partials.user-name')->render()
                : '',
        );

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch): void {
            $panelSwitch
                ->panels(function (): ?array {
                    $user = auth()->user();

                    if (! $user?->canSwitchPanels()) {
                        return null;
                    }

                    return ['admin', 'clientes'];
                })
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
