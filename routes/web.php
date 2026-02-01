<?php

use Illuminate\Support\Facades\Route;
use App\Models\Programas;

Route::get('/admin/download-local/{id}', function ($id) {
    // 1. Definir la ruta del USB
    $rutaUSB = "/Volumes/SIBI";
    
    echo "<h1>🕵️‍♂️ REPORTE DE DETECTIVE</h1>";

    // PRUEBA A: ¿Existe el USB?
    if (!is_dir($rutaUSB)) {
        die("<h2 style='color:red'>❌ FALLO: PHP no encuentra la carpeta /Volumes/SIBI. <br>¿Está el USB conectado? ¿Se llama SIBI (todo mayúsculas)?</h2>");
    }
    echo "<h3 style='color:green'>✅ El USB existe y PHP puede tocarlo.</h3>";

    // PRUEBA B: ¿Qué carpetas hay dentro?
    $carpetas = scandir($rutaUSB);
    
    echo "<h3>📂 Contenido del USB SIBI:</h3>";
    echo "<ul>";
    foreach ($carpetas as $item) {
        if ($item == '.' || $item == '..') continue;
        echo "<li>Checking: <strong>[" . $item . "]</strong></li>";
        
        // Si encontramos la carpeta "AA PROGRAMAS", miramos dentro
        if (str_contains($item, "PROGRAMAS")) {
            echo "<ul><span style='color:blue'>found! Mirando dentro de [$item]...</span>";
            $subarchivos = scandir($rutaUSB . "/" . $item);
            foreach ($subarchivos as $sub) {
                 if ($sub == '.' || $sub == '..') continue;
                 echo "<li>📄 Archivo: $sub</li>";
            }
            echo "</ul>";
        }
    }
    echo "</ul>";

    // PRUEBA C: El archivo que buscamos
    $programa = Programas::find($id);
    echo "<hr><h3>🎯 Buscando archivo específico:</h3>";
    echo "Base de datos dice: <strong>" . $programa->file_path . "</strong><br>";
    
    $rutaCompleta = $rutaUSB . "/" . $programa->file_path;
    
    if (file_exists($rutaCompleta)) {
        echo "<h1 style='color:green'>🎉 ¡BINGO! El archivo EXISTE FÍSICAMENTE.</h1>";
        echo "Si ves esto, el problema de permisos ESTÁ RESUELTO.";
    } else {
        echo "<h1 style='color:red'>❌ EL ARCHIVO NO ESTÁ.</h1>";
        echo "Compara letra por letra el nombre del archivo de arriba con el de la lista.";
    }
});