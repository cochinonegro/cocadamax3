<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Programas;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/download-local/{id}', function ($id) {
    // 1. Buscamos el programa (si falla, dará 404, no 500)
    $programa = Programas::findOrFail($id);

    // 2. Si es archivo local
    if ($programa->disk_name && $programa->file_path) {
        set_time_limit(0);

        // Obtenemos la ruta FÍSICA REAL en tu Mac (Ej: /Volumes/SIBI/...)
        // Esto evita errores de drivers de Laravel
        $rutaReal = Storage::disk($programa->disk_name)->path($programa->file_path);

        // Verificamos si el archivo existe antes de intentar nada
        if (!file_exists($rutaReal)) {
            return "ERROR: El archivo no está conectado. Verifica que el USB '" . $programa->disk_name . "' esté enchufado.";
        }

        // Descarga directa (Fuerza bruta, funciona siempre)
        return response()->download($rutaReal);
    }

    // 3. Si no es local, URL externa
    if ($programa->url) {
        return redirect()->away($programa->url);
    }

    return "No hay archivo configurado.";
})->name('download.local');
