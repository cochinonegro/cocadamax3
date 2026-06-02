<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class UserInfoWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = [
        'default' => 1,
        'lg' => 2,
    ];

    protected string $view = 'filament.admin.widgets.user-info';

    protected static ?int $sort = 1;
}
