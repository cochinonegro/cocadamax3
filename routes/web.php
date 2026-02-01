<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Programas;

Route::get('/', function () {
    return view('welcome');
});

// =============================================================================
// RUTA DE DESCARGA SEGURA PARA CLIENTES
// =============================================================================
// Esta ruta recibe el ID del programa y valida la firma automáticamente.
// Nombre de la ruta: 'cliente.descarga' (lo usaremos luego en Filament).

Route::get('/descarga-privada/{id}', function (Request $request, $id) {
    
    // 1. VALIDACIÓN DE SEGURIDAD (EL PORTERO)
    // Laravel verifica que el hash de la URL coincida y que no haya expirado.
    if (! $request->hasValidSignature()) {
        // Si la firma falla, abortamos con error 403 (Prohibido).
        abort(403, '⛔ ENLACE NO VÁLIDO O CADUCADO. Contacta con el administrador.');
    }

    // 2. BUSCAR EL PROGRAMA
    // Buscamos en la base de datos el programa con ese ID.
    $programa = Programas::findOrFail($id);

    // 3. REDIRECCIÓN A LA NUBE (DigiStorage / pCloud)
    // Verificamos si tienes guardado el enlace externo.
    if ($programa->url) {
        // 'redirect()->away()' saca al usuario de tu web y lo lleva a la nube
        // para que descargue el archivo directamente desde allí.
        return redirect()->away($programa->url);
    }

    // 4. SI NO HAY ENLACE
    return "❌ Error: Este programa no tiene un enlace de descarga (URL) configurado.";

})->name('cliente.descarga')->middleware('signed'); // 'signed' activa la protección de Laravel.