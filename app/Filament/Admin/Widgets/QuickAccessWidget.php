<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class QuickAccessWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = [
        'default' => 1,
        'lg' => 2,
    ];

    protected string $view = 'filament.admin.widgets.quick-access';

    protected static ?int $sort = 2;
}
