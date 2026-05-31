<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgramaDownloadController;
use App\Models\Programas;

Route::get('/', function () {
    return view('welcome');
});

// =============================================================================
// RUTA DE DESCARGA SEGURA (MODO OCULTO / PROXY)
// =============================================================================
Route::get('/descarga-segura/{id}', function ($id) {
    
    // 1. Verificar seguridad (firma y caducidad)
    if (! request()->hasValidSignature()) {
        abort(403, '⛔ El enlace ha caducado o no es válido.');
    }

    // 2. Buscar el programa
    // Usamos 'Programas' tal cual lo tienes en tus modelos
    $programa = \App\Models\Programas::findOrFail($id); 

    if (empty($programa->url)) {
        abort(404, 'No hay enlace configurado para este programa.');
    }

    // 3. MODO STREAMING (Optimizado para DigiStorage)
    // Usamos headers para forzar la descarga y readfile para no gastar RAM.
    
    // Intentamos limpiar el nombre (quitar parámetros extra de la URL si los hubiera)
    $nombreLimpio = basename(parse_url($programa->url, PHP_URL_PATH));
    
    return response()->streamDownload(function () use ($programa) {
        // 'readfile' es mucho más rápido y ligero que 'file_get_contents'
        // Lee el archivo de DigiStorage y lo escupe directo al cliente.
        readfile($programa->url);
    }, $nombreLimpio);

})->name('cliente.descarga'); // Este nombre conecta con tu botón de Filament

Route::middleware(['auth'])->get('/clientes/descarga/{programas}', [ProgramaDownloadController::class, 'download'])
    ->name('invitado.descarga');

// Esta línea le dice a Laravel dónde está la puerta de entrada
Route::redirect('/login', '/admin/login')->name('login');

// Esta línea opcional hace que si entras a la raíz, te mande al panel
Route::redirect('/', '/admin');

