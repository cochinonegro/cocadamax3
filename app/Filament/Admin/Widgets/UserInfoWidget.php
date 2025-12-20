<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class UserInfoWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.user-info';

    protected static ?int $sort = 2;
}
