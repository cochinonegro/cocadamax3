<?php

namespace App\Filament\Admin\Resources\Descargas\Pages;

use App\Filament\Admin\Resources\Descargas\DescargasResource;
use App\Filament\Admin\Resources\Descargas\Widgets\DescargasVentasMesWidget;
use App\Filament\Admin\Resources\Descargas\Widgets\DescargasVentasSemanaWidget;
use Filament\Resources\Pages\ListRecords;

class ListDescargas extends ListRecords
{
    protected static string $resource = DescargasResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            DescargasVentasSemanaWidget::class,
            DescargasVentasMesWidget::class,
        ];
    }
}
