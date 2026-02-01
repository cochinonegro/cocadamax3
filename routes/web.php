<?php

use Illuminate\Support\Facades\Route;
use App\Models\Programas;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/download-local/{id}', function ($id) {
    // 1. Configuración
    $rutaUSB = "/Volumes/SIBI"; // <--- Asegúrate que este es el nombre correcto
    
    echo "<h1>🕵️‍♂️ REPORTE DE PERMISOS</h1>";

    // PRUEBA A: ¿Existe la carpeta Volumes?
    if (!is_dir('/Volumes')) {
        die("<h2 style='color:red'>❌ ERROR CRÍTICO: PHP no puede ver ni siquiera /Volumes.</h2>");
    }

    // PRUEBA B: Intentar leer el USB de forma segura
    // El @ evita que PHP explote si no tiene permiso
    $carpetas = @scandir($rutaUSB);
    
    if ($carpetas === false) {
        // AQUÍ ES DONDE DABA EL ERROR 500 ANTES
        die("
            <div style='background-color:#ffebee; padding:20px; border: 2px solid red;'>
                <h2 style='color:red; margin-top:0;'>⛔ ACCESO DENEGADO (PERMISOS)</h2>
                <p>El servidor PHP intenta leer <strong>$rutaUSB</strong>, pero MacOS lo bloquea.</p>
                <hr>
                <h3>SOLUCIÓN OBLIGATORIA:</h3>
                <ol>
                    <li>Ve a <strong>Ajustes del Sistema</strong> > <strong>Privacidad y Seguridad</strong>.</li>
                    <li>Entra en <strong>Acceso total al disco</strong>.</li>
                    <li>Busca en la lista <strong>Visual Studio Code</strong> (o la app que uses).</li>
                    <li><strong>¡ACTÍVALO!</strong> (Interruptor azul).</li>
                    <li><strong>IMPORTANTE:</strong> Reinicia Visual Studio Code totalmente (CMD + Q).</li>
                </ol>
            </div>
        ");
    }

    echo "<h3 style='color:green'>✅ ¡Acceso conseguido! PHP puede leer el USB.</h3>";
    echo "<h3>📂 Contenido encontrado en SIBI:</h3><ul>";
    
    foreach ($carpetas as $item) {
        if ($item == '.' || $item == '..') continue;
        echo "<li>" . $item . "</li>";
    }
    echo "</ul>";

    // PRUEBA C: Buscar el archivo del programa
    $programa = Programas::find($id);
    if ($programa) {
        echo "<hr><h3>Buscando archivo de base de datos:</h3>";
        echo "Ruta: " . $programa->file_path . "<br>";
        
        if (file_exists($rutaUSB . "/" . $programa->file_path)) {
            echo "<h2 style='color:green'>🎉 EL ARCHIVO EXISTE Y ES ACCESIBLE.</h2>";
            echo "<p>Ya puedes volver a poner el código de descarga original.</p>";
        } else {
            echo "<h2 style='color:orange'>⚠️ Permisos OK, pero archivo no encontrado.</h2>";
            echo "<p>Revisa si el nombre de la carpeta 'AA PROGRAMAS' está bien escrito.</p>";
        }
    }

});