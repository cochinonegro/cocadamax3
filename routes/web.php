<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Programas;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/download-local/{id}', function ($id) {
    // 1. Buscamos el programa
    $programa = Programas::findOrFail($id);

    // 2. Si es archivo local (USB)
    if ($programa->disk_name && $programa->file_path) {
        set_time_limit(0);

        // Construimos la ruta manualmente para evitar bloqueos de configuración
        // Ajusta aquí si tus discos tienen nombres base distintos
        $rutaBase = "/Volumes/SIBI";
        if ($programa->disk_name == 'disco_hdd') $rutaBase = "/Volumes/HDD";
        if ($programa->disk_name == 'disco_laila') $rutaBase = "/Volumes/LAILA";

        $rutaCompleta = $rutaBase . "/" . $programa->file_path;

        // Verificamos existencia física
        if (!file_exists($rutaCompleta)) {
            // Si falla, mostramos mensaje técnico
            return "ERROR: MacOS no deja leer el archivo o no existe.\n" .
                   "Ruta buscada: " . $rutaCompleta . "\n" .
                   "Solución: Ve a Ajustes > Privacidad > Acceso Total al Disco y activa la Terminal.";
        }

        // 3. ¡Descarga!
        return response()->download($rutaCompleta);
    }

    // 4. Si es URL externa
    if ($programa->url) {
        return redirect()->away($programa->url);
    }

    return "No configurado.";
})->name('download.local');
