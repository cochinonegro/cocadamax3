<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\QuickAccessWidget;
use App\Filament\Admin\Widgets\UserInfoWidget;
use App\Filament\Admin\Widgets\VentasUltimos10DiasChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Escritorio';

    protected static ?string $title = 'Escritorio';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            VentasUltimos10DiasChart::class,
            UserInfoWidget::class,
            QuickAccessWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
