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

    // 2. Verificamos si es un archivo local
    if ($programa->disk_name && $programa->file_path) {
        set_time_limit(0);

        // Verificamos si existe usando el disco
        if (! Storage::disk($programa->disk_name)->exists($programa->file_path)) {
            abort(404, 'EL ARCHIVO NO EXISTE EN EL DISCO: ' . $programa->disk_name);
        }

        // --- CAMBIO CLAVE AQUÍ ---
        // En lugar de usar ->download() del Storage, pedimos la RUTA COMPLETA
        // y forzamos la descarga directa con PHP. Esto quita el error del editor.
        $rutaCompleta = Storage::disk($programa->disk_name)->path($programa->file_path);

        return response()->download($rutaCompleta);
    }

    // 3. Si no es local, URL externa
    if ($programa->url) {
        return redirect()->away($programa->url);
    }

    return "No hay archivo configurado para este programa.";
})->name('download.local');
