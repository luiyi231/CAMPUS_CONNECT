<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SolicitudController;
use Illuminate\Support\Facades\Route;

// Redireccionar raíz al panel de administración
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Panel Administrativo de Solicitudes (Blade)
Route::prefix('admin')->name('admin.')->group(function () {
    // HU1: Dashboard de solicitudes y estados
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Gestión de Solicitudes
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/{id}', [SolicitudController::class, 'show'])->name('solicitudes.show');

    // HU2: Asignar un responsable a una solicitud
    Route::post('/solicitudes/{id}/asignar-responsable', [SolicitudController::class, 'asignarResponsable'])->name('solicitudes.asignarResponsable');

    // HU5: Actualizar el estado de una solicitud
    Route::post('/solicitudes/{id}/actualizar-estado', [SolicitudController::class, 'actualizarEstado'])->name('solicitudes.actualizarEstado');
});
