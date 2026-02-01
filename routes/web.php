<?php

use Illuminate\Support\Facades\Route;

Route::get('/admin/download-local/{id}', function ($id) {
    echo "<style>body{font-family:sans-serif; padding:20px; background:#f0f0f0;}</style>";
    echo "<div style='background:white; padding:20px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1);'>";

    // 1. ¿QUIÉN ESTÁ EJECUTANDO LA WEB?
    $usuario = exec('whoami');
    echo "<h2>👤 IDENTIDAD</h2>";
    echo "Soy el usuario: <strong style='font-size:1.2em; color:blue'>" . $usuario . "</strong><br>";

    // Nota: En Forge el usuario suele ser 'forge', no 'rafaelperezoctavio' ni '_www'.
    if ($usuario == 'daemon' || $usuario == '_www') {
        echo "<p style='color:red'>❌ MAL: Sigo siendo el usuario fantasma de XAMPP.</p>";
    } elseif ($usuario == 'rafaelperezoctavio' || $usuario == 'forge') {
        echo "<p style='color:green'>✅ BIEN: Usuario reconocido ($usuario).</p>";
    } else {
        echo "<p style='color:orange'>⚠️ AVISO: Soy el usuario '$usuario'.</p>";
    }

    echo "<hr>";

    // 2. ¿QUÉ VEO EN EL USB?
    // NOTA IMPORTANTE: En el servidor Forge (Linux), esta ruta "/Volumes/SIBI" NO EXISTIRÁ.
    // Tendrás que cambiarla por la ruta donde hayas montado el disco en Linux (ej: /mnt/sibi)
    $rutaUSB = "/Volumes/SIBI";
    echo "<h2>📂 CONTENIDO DEL USB</h2>";
    echo "<p>Buscando en: <code>$rutaUSB</code></p>";

    if (!is_dir($rutaUSB)) {
        // Usamos return en vez de die() para que cierre el div correctamente, aunque pare la ejecución lógica
        echo "<h3 style='color:red'>⛔ ERROR: No veo el USB en $rutaUSB</h3></div>";
        return;
    }

    $carpetas = scandir($rutaUSB);
    echo "<ul>";
    foreach ($carpetas as $item) {
        if ($item[0] == '.') continue;

        echo "<li>Carpeta detectada: <strong>[" . $item . "]</strong></li>";

        if (str_contains($item, "PROGRAMAS")) {
            echo "<ul><span style='color:purple'>↳ Mirando dentro de esta carpeta...</span>";
            // Aseguramos que existe la subcarpeta antes de escanear
            if(is_dir($rutaUSB . "/" . $item)){
                $archivos = scandir($rutaUSB . "/" . $item);
                foreach ($archivos as $arch) {
                    if ($arch[0] == '.') continue;
                    echo "<li>📄 Archivo: <strong>[" . $arch . "]</strong></li>";
                }
            }
            echo "</ul>";
        }
    }
    echo "</ul>";
    echo "</div>";

})->name('download.local'); // <--- AQUÍ ES DONDE DEBE IR EL CIERRE Y EL NOMBRE
