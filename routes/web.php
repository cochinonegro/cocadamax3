<?php

use Illuminate\Support\Facades\Route;

Route::get('/admin/download-local/{id}', function ($id) {
    echo "<style>body{font-family:sans-serif; padding:20px; background:#f0f0f0;}</style>";
    echo "<div style='background:white; padding:20px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1);'>";

    // 1. ¿QUIÉN ESTÁ EJECUTANDO LA WEB?
    $usuario = exec('whoami');
    echo "<h2>👤 IDENTIDAD</h2>";
    echo "Soy el usuario: <strong style='font-size:1.2em; color:blue'>" . $usuario . "</strong><br>";

    if ($usuario == 'daemon' || $usuario == '_www') {
        echo "<p style='color:red'>❌ MAL: Sigo siendo el usuario fantasma de XAMPP. El reinicio no funcionó.</p>";
    } elseif ($usuario == 'rafaelperezoctavio') {
        echo "<p style='color:green'>✅ BIEN: Soy Rafa. Tengo permisos.</p>";
    }

    echo "<hr>";

    // 2. ¿QUÉ VEO EN EL USB?
    $rutaUSB = "/Volumes/SIBI";
    echo "<h2>📂 CONTENIDO DEL USB</h2>";

    if (!is_dir($rutaUSB)) {
        die("<h3 style='color:red'>⛔ ERROR: No veo el USB en /Volumes/SIBI</h3></div>");
    }

    $carpetas = scandir($rutaUSB);
    echo "<ul>";
    foreach ($carpetas as $item) {
        if ($item[0] == '.') continue; // Saltar ocultos

        // Truco: Ponemos corchetes para ver espacios invisibles
        echo "<li>Carpeta detectada: <strong>[" . $item . "]</strong></li>";

        // Si vemos la carpeta programas, entramos a mirar
        if (str_contains($item, "PROGRAMAS")) {
            echo "<ul><span style='color:purple'>↳ Mirando dentro de esta carpeta...</span>";
            $archivos = scandir($rutaUSB . "/" . $item);
            foreach ($archivos as $arch) {
                if ($arch[0] == '.') continue;
                echo "<li>📄 Archivo: <strong>[" . $arch . "]</strong></li>";
            }
            echo "</ul>";
        }
    }
    echo "</ul>";
    echo "</div>";
});
