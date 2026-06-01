<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Programas;
use App\Support\PedidosDescargaHandler;
use Illuminate\Http\RedirectResponse;

class PedidosDownloadController extends Controller
{
    public function download(Programas $programas): RedirectResponse
    {
        $user = auth()->user();

        abort_unless(
            $user && $user->hasAnyRole([UserRole::Invitado->value, UserRole::Administrador->value]),
            403,
        );

        abort_unless($programas->isVisibleInPedidos(), 404, 'Este programa no está disponible en Pedidos.');

        $url = PedidosDescargaHandler::consume($programas);

        abort_unless(filled($url), 404, 'No hay enlace configurado para este programa.');

        return redirect()->away($url);
    }
}
