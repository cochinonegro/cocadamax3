<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Programas;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProgramaDownloadController extends Controller
{
    public function download(Programas $programas): StreamedResponse
    {
        $user = auth()->user();

        abort_unless(
            $user && $user->hasAnyRole([UserRole::Invitado->value, UserRole::Administrador->value]),
            403,
        );

        abort_unless($programas->isVisibleToClients(), 404, 'Este programa no está disponible para descarga.');

        if ($programas->disk_name && $programas->file_path) {
            abort_unless(
                Storage::disk($programas->disk_name)->exists($programas->file_path),
                404,
                'Archivo no encontrado en el disco.',
            );

            return Storage::disk($programas->disk_name)->download(
                $programas->file_path,
                basename($programas->file_path),
            );
        }

        abort_unless(filled($programas->url), 404, 'No hay enlace configurado para este programa.');

        $nombreLimpio = basename(parse_url($programas->url, PHP_URL_PATH)) ?: $programas->progname;

        return response()->streamDownload(function () use ($programas) {
            readfile($programas->url);
        }, $nombreLimpio);
    }
}
