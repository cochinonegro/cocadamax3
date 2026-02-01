<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Programas;

Route::get('/', function () {
    return view('welcome');
});

// Ruta simple para recuperar la web
Route::get('/admin/download-local/{id}', function ($id) {
    
    // 1. Buscamos el programa
    $programa = Programas::findOrFail($id);

    // 2. Definimos la ruta del archivo
    $rutaCompleta = "/Volumes/SIBI/" . $programa->file_path;
    
    // Ajuste para otros discos
    if ($programa->disk_name == 'disco_hdd') $rutaCompleta = "/Volumes/HDD/" . $programa->file_path;
    if ($programa->disk_name == 'disco_laila') $rutaCompleta = "/Volumes/LAILA/" . $programa->file_path;

    // 3. Comprobamos si el Mac nos deja leerlo
    if (!file_exists($rutaCompleta)) {
        return "ERROR DE PERMISOS O RUTA: MacOS no ve el archivo en: " . $rutaCompleta . 
               " (Asegúrate de que la Terminal/VS Code tiene Acceso Total al Disco)";
    }

    // 4. Descargamos
    return response()->download($rutaCompleta);

})->name('download.local');