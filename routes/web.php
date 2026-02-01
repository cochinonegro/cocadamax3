<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Programas;

Route::get('/', function () {
    return view('welcome');
});

// RUTA DE DIAGNÓSTICO
Route::get('/admin/download-local/{id}', function ($id) {
    try {
        // 1. Prueba básica de vida
        if (!class_exists('App\Models\Programas')) {
            throw new Exception("El modelo Programas no se encuentra.");
        }

        // 2. Buscar programa
        $programa = Programas::find($id);

        if (!$programa) {
            dd("ERROR: No se encontró el programa con ID: " . $id);
        }

        // 3. Imprimir datos en pantalla (Debug)
        // Si ves esto, la base de datos funciona
        if (!$programa->disk_name) {
            dd("ERROR: Este programa no tiene 'disk_name' en la base de datos.", $programa->toArray());
        }

        // 4. Construcción de ruta MANUAL (Sin depender de config/filesystems)
        // Esto confirma si el problema es el archivo config
        $rutaManual = "/Volumes/SIBI/" . $programa->file_path;

        // CORRECCIÓN PARA OTROS DISCOS
        if ($programa->disk_name == 'disco_hdd') $rutaManual = "/Volumes/HDD/" . $programa->file_path;
        if ($programa->disk_name == 'disco_laila') $rutaManual = "/Volumes/LAILA/" . $programa->file_path;

        // 5. Verificación física
        if (!file_exists($rutaManual)) {
            dd(
                "ERROR: El archivo no existe físicamente.",
                "Buscando en: " . $rutaManual,
                "¿Está el USB conectado y montado con ese nombre?"
            );
        }

        // 6. Intento de descarga directo
        return response()->download($rutaManual);

    } catch (\Throwable $e) {
        // AQUÍ ESTÁ LA MAGIA:
        // En lugar de dar Error 500, te mostrará el texto del error real.
        dd(
            "🛑 ERROR FATAL CAPTURADO:",
            $e->getMessage(),
            "Línea: " . $e->getLine(),
            "Archivo: " . $e->getFile()
        );
    }
})->name('download.local');
