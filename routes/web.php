<?php

use Illuminate\Support\Facades\Route;
use App\Models\Programas;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/download-local/{id}', function ($id) {
    // 1. Buscamos el programa
    $programa = Programas::findOrFail($id);

    // Ruta base esperada
    $rutaBase = "/Volumes/SIBI";

    // Ajuste según el disco seleccionado en BD
    if ($programa->disk_name == 'disco_hdd') $rutaBase = "/Volumes/HDD";
    if ($programa->disk_name == 'disco_laila') $rutaBase = "/Volumes/LAILA";

    $debug = [];
    $debug[] = "--- INICIO DEL DIAGNÓSTICO ---";
    $debug[] = "Buscando archivo: " . $programa->file_path;
    $debug[] = "En el disco montado en: " . $rutaBase;

    // PASO A: ¿PHP puede ver la carpeta /Volumes?
    if (!is_dir('/Volumes')) {
        dd("❌ ERROR CRÍTICO: PHP no tiene acceso a /Volumes. Es un problema de PERMISOS DE MAC.");
    }

    // PASO B: ¿Qué discos ve PHP realmente?
    $discos = scandir('/Volumes');
    $debug[] = "👀 Discos que ve PHP en /Volumes: " . implode(' | ', $discos);

    // PASO C: ¿Ve tu disco específico?
    if (!in_array(basename($rutaBase), $discos)) {
        dd($debug, "❌ ERROR: PHP ve otros discos, pero NO ve '" . basename($rutaBase) . "'. ¿Se llama diferente? ¿SIBI 1?");
    }

    // PASO D: Explorar dentro del disco
    // Intentamos ver qué carpetas hay dentro de /Volumes/SIBI
    $contenidoDisco = @scandir($rutaBase);

    if (!$contenidoDisco) {
        dd($debug, "❌ ERROR DE PERMISOS: PHP ve el disco, pero MacOS le prohíbe leer dentro. Necesitas dar 'Full Disk Access' a la terminal/PHP.");
    }

    $debug[] = "📂 Carpetas dentro de tu disco: " . implode(' | ', $contenidoDisco);

    // PASO E: Validar la carpeta del archivo
    // Extraemos la primera parte de tu ruta (Ej: "AA PROGRAMAS")
    $partes = explode('/', $programa->file_path);
    $carpeta = $partes[0]; // "AA PROGRAMAS"

    if (!in_array($carpeta, $contenidoDisco)) {
        dd($debug, "❌ ERROR DE NOMBRE: PHP ve el disco, pero NO encuentra la carpeta exact: '" . $carpeta . "'",
           "Revisa espacios dobles o mayúsculas. Copia y pega uno de los nombres de arriba 👆");
    }

    // PASO F: Si llegamos aquí, listamos el contenido de esa carpeta
    $archivos = scandir($rutaBase . "/" . $carpeta);
    $nombreArchivo = basename($programa->file_path);

    if (!in_array($nombreArchivo, $archivos)) {
         dd($debug,
            "📂 Archivos encontrados en " . $carpeta . ": " . implode(' | ', $archivos),
            "❌ ERROR FINAL: La carpeta existe, pero el archivo '" . $nombreArchivo . "' no está dentro."
         );
    }

    return "✅ ¡TODO PARECE CORRECTO! El archivo debería descargarse. Si ves esto, restaura el código de descarga anterior.";

})->name('download.local');
