<?php


use App\Models\Programas;
use Illuminate\Support\Facades\Storage;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/admin/download-local/{id}', function ($id) {
    // 1. Buscamos el programa
    $programa = Programas::findOrFail($id);

    // 2. Verificamos si es un archivo local o una URL externa
    if ($programa->disk_name && $programa->file_path) {

        // Configuración para archivos gigantes (evita cortes)
        set_time_limit(0);

        // Verifica si el archivo existe realmente en el disco SIBI (o el que sea)
        if (! Storage::disk($programa->disk_name)->exists($programa->file_path)) {
            abort(404, 'EL ARCHIVO NO EXISTE EN EL DISCO: ' . $programa->disk_name);
        }

        // 3. ¡Descarga directa desde el disco USB!
        return Storage::disk($programa->disk_name)->download($programa->file_path);
    }

    // Si no tiene disco local, intentamos redirigir a la URL externa
    if ($programa->url) {
        return redirect()->away($programa->url);
    }

    return "No hay archivo configurado para este programa.";
})->name('download.local');
