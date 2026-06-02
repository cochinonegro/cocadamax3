<?php

namespace App\Support;

use App\Enums\ProgramaSolicitudStatus;
use App\Models\Programas;
use App\Models\ProgramaSolicitud;
use App\Models\User;

final class PrecioAcordadoPedido
{
    public static function resolveForDescarga(Programas $programa, ?User $user): ?float
    {
        if ($user === null) {
            return null;
        }

        $precio = ProgramaSolicitud::query()
            ->where('user_id', $user->id)
            ->where('programas_id', $programa->id)
            ->where('status', ProgramaSolicitudStatus::Accepted)
            ->whereNotNull('precio_acordado')
            ->latest('id')
            ->value('precio_acordado');

        return $precio !== null ? (float) $precio : null;
    }

    public static function format(?float $precio): ?string
    {
        if ($precio === null) {
            return null;
        }

        return DescargaVentas::formatEuro($precio);
    }
}
