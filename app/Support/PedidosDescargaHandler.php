<?php

namespace App\Support;

use App\Filament\Support\ProgramasTableColumns;
use App\Models\Programas;

class PedidosDescargaHandler
{
    public const LABEL = 'DESCARGA AQUÍ EL PROGRAMA';

    /**
     * Quita el programa de Pedidos (cliente y admin) y devuelve la URL de descarga.
     */
    public static function consume(Programas $programa): ?string
    {
        $url = ProgramasTableColumns::downloadUrl($programa->url);

        if (blank($url)) {
            return null;
        }

        PedidosVisibility::disableFor($programa);

        return $url;
    }
}
