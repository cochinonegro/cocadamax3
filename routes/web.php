<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Models\Programas;

Route::get('/', function () {
    return view('welcome');
});

// -----------------------------------------------------------------------------
// RUTA DE DESCARGA SEGURA (SOLO CON ENLACE FIRMADO)
// -----------------------------------------------------------------------------
// El middleware 'signed' es el portero de discoteca. 
// Si la firma no es válida, no deja pasar a nadie.
Route::get('/descarga-cliente/{id}', function (Request $request, $id) {
    
    // 1. Verificar si la firma es válida (Por seguridad extra)
    if (! $request->hasValidSignature()) {
        abort(403, '⛔ ESTE ENLACE NO ES VÁLIDO O HA CADUCADO.');
    }

    // 2. Buscar el programa en la base de datos
    $programa = Programas::findOrFail($id);

    // -----------------------------------------------------
    // CASO A: Es un enlace externo (pCloud, Drive, DigiStorage)
    // -----------------------------------------------------
    if ($programa->url) {
        // Redirigimos al cliente directamente a la nube.
        // Es lo mejor para no saturar tu servidor local.
        return redirect()->away($programa->url);
    }

    // -----------------------------------------------------
    // CASO B: Es un archivo local en tu USB (Mac)
    // -----------------------------------------------------
    if ($programa->disk_name && $programa->file_path) {
        
        // Ajustamos la ruta base según el disco que tengas conectado
        $rutaBase = "/Volumes/SIBI"; 
        if ($programa->disk_name == 'disco_hdd') $rutaBase = "/Volumes/HDD";
        // Añade aquí más discos si compras más en el futuro
        
        $rutaCompleta = $rutaBase . "/" . $programa->file_path;

        // Verificamos que el archivo exista antes de intentar enviarlo
        if (file_exists($rutaCompleta)) {
            // response()->download() envía el archivo al navegador del cliente
            return response()->download($rutaCompleta);
        } else {
            return "❌ Error: El archivo físico no está conectado en el servidor.";
        }
    }

    return "⚠️ Este programa no tiene archivo ni enlace configurado.";

})->name('descarga.segura')->middleware('signed');


// -----------------------------------------------------------------------------
// RUTA PARA QUE TÚ GENERES LOS ENLACES (SOLO PARA TI)
// -----------------------------------------------------------------------------
// Entra aquí para crear un enlace para un cliente.
// Ejemplo de uso: midominio.com/generar-link/308
Route::get('/generar-link/{id}', function ($id) {
    
    // 1. Buscamos el programa
    $prog = Programas::findOrFail($id);

    // 2. GENERAMOS LA URL FIRMADA TEMPORAL
    // 'descarga.segura' -> Es el nombre de la ruta de arriba.
    // now()->addDays(7) -> El enlace caducará en 7 días (puedes poner addHours(24)).
    $urlSegura = URL::temporarySignedRoute(
        'descarga.segura', 
        now()->addDays(7), 
        ['id' => $prog->id]
    );

    // 3. Mostramos el enlace en pantalla para que lo copies
    return "
        <div style='font-family:sans-serif; padding:40px; text-align:center;'>
            <h1>🔐 Enlace Generado para: {$prog->progname}</h1>
            <p>Copia este enlace y envíalo por correo al cliente. Caduca en 7 días.</p>
            
            <textarea style='width:100%; height:100px; font-size:16px; padding:10px;'>$urlSegura</textarea>
            
            <br><br>
            <a href='$urlSegura' style='background:blue; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>
                Probar Enlace Ahora
            </a>
        </div>
    ";
});