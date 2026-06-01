<?php

use App\Http\Controllers\ProgramaDownloadController;
use App\Http\Controllers\WelcomeController;
use App\Support\DescargaRegistrar;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/deploy-check', fn () => response()->json([
    'app' => 'cocadamax3',
    'version' => 'guest-welcome-v5',
    'time' => now()->toIso8601String(),
]))->name('deploy.check');
Route::post('/registro', [WelcomeController::class, 'register'])->name('welcome.register');
Route::post('/iniciar-sesion', [WelcomeController::class, 'login'])->name('welcome.login');

Route::post('/telegram/webhook', \App\Http\Controllers\TelegramWebhookController::class)
    ->name('telegram.webhook');

Route::redirect('/login', '/admin/login')->name('login');

// =============================================================================
// RUTA DE DESCARGA SEGURA (MODO OCULTO / PROXY)
// =============================================================================
Route::get('/descarga-segura/{id}', function ($id) {

    // 1. Verificar seguridad (firma y caducidad)
    if (! request()->hasValidSignature()) {
        abort(403, '⛔ El enlace ha caducado o no es válido.');
    }

    // 2. Buscar el programa
    $programa = \App\Models\Programas::findOrFail($id);

    if (empty($programa->url)) {
        abort(404, 'No hay enlace configurado para este programa.');
    }

    if (auth()->check()) {
        DescargaRegistrar::record($programa, auth()->user());
    }

    // 3. MODO STREAMING (Optimizado para DigiStorage)
    $nombreLimpio = basename(parse_url($programa->url, PHP_URL_PATH));

    return response()->streamDownload(function () use ($programa) {
        readfile($programa->url);
    }, $nombreLimpio);

})->name('cliente.descarga');

Route::middleware(['auth'])->get('/clientes/descarga/{programas}', [ProgramaDownloadController::class, 'download'])
    ->name('invitado.descarga');
