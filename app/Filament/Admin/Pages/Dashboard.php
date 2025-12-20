<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\UserInfoWidget;
use App\Filament\Admin\Widgets\QuickAccessWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Escritorio';
    protected static ?string $navigationLabel = 'Escritorio';
    protected static ?string $navigationIcon = 'heroicon-o-home';

    /*protected function getHeaderWidgets(): array
    {
        return [
            UserInfoWidget::class,
            QuickAccessWidget::class,
        ];
    }*/

    protected function getWidgetColumns(): int | array
    {
        return 2; // ← Dos widgets por fila
    }
}
