<?php

use App\Http\Controllers\Api\SolicitudApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Campus Connect (Orientado a la App Móvil / Estudiantes)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // HU3: Registrar nueva solicitud con tipo y opcional evidencia
    Route::post('/solicitudes', [SolicitudApiController::class, 'store'])->name('api.solicitudes.store');

    // HU4: Ver mis solicitudes y en qué estado se encuentran
    Route::get('/estudiantes/{id_estudiante}/solicitudes', [SolicitudApiController::class, 'misSolicitudes'])->name('api.estudiantes.solicitudes');
    Route::get('/solicitudes/{id}', [SolicitudApiController::class, 'show'])->name('api.solicitudes.show');

    // HU6: Adjuntar evidencia fotográfica a una solicitud
    Route::post('/solicitudes/{id}/evidencias', [SolicitudApiController::class, 'adjuntarEvidencia'])->name('api.solicitudes.adjuntarEvidencia');

    // Catálogos auxiliares (Tipos y Estados)
    Route::get('/catalogos', [SolicitudApiController::class, 'catalogos'])->name('api.catalogos');
});

// Alias directos sin prefijo de versión para compatibilidad
Route::post('/solicitudes', [SolicitudApiController::class, 'store']);
Route::get('/estudiantes/{id_estudiante}/solicitudes', [SolicitudApiController::class, 'misSolicitudes']);
Route::get('/solicitudes/{id}', [SolicitudApiController::class, 'show']);
Route::post('/solicitudes/{id}/evidencias', [SolicitudApiController::class, 'adjuntarEvidencia']);
Route::get('/catalogos', [SolicitudApiController::class, 'catalogos']);
