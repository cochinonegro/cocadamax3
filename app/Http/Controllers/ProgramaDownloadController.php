<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Filament\Support\ProgramasTableColumns;
use App\Models\Programas;
use App\Support\DescargaRegistrar;
use Illuminate\Http\RedirectResponse;

class ProgramaDownloadController extends Controller
{
    public function download(Programas $programas): RedirectResponse
    {
        $user = auth()->user();

        abort_unless(
            $user && $user->hasAnyRole([UserRole::Invitado->value, UserRole::Administrador->value]),
            403,
        );

        abort_unless($programas->isVisibleToClients(), 404, 'Este programa no está disponible para descarga.');

        $url = ProgramasTableColumns::downloadUrl($programas->url);

        abort_unless(filled($url), 404, 'No hay enlace configurado para este programa.');

        DescargaRegistrar::record($programas, $user);

        return redirect()->away($url);
    }
}
