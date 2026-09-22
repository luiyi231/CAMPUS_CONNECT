<?php

use App\Models\EstadoSolicitud;
use App\Models\Solicitud;
use App\Models\User;
use Database\Seeders\CampusConnectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CampusConnectSeeder::class);
});

test('HU1: el administrador puede ver el dashboard con el resumen y conteo de estados', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.dashboard');
    $response->assertViewHasAll([
        'totalSolicitudes',
        'estadosConteo',
        'sinResponsable',
        'tiposConteo',
        'solicitudesRecientes',
    ]);

    // Verificar que los 4 estados canónicos están presentes en los conteos
    $conteo = $response->viewData('estadosConteo');
    expect($conteo)->toHaveKeys(['RECIBIDA', 'PENDIENTE', 'EN-PROCESO', 'COMPLETADA']);
});

test('HU2: el administrador puede asignar un responsable a una solicitud', function () {
    $solicitud = Solicitud::whereNull('id_responsable')->first();
    $responsable = User::where('rol', 'RESPONSABLE')->first();

    expect($solicitud)->not->toBeNull();
    expect($responsable)->not->toBeNull();

    $response = $this->post(route('admin.solicitudes.asignarResponsable', $solicitud->id_solicitud), [
        'id_responsable' => $responsable->id_usuario,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $solicitud->refresh();
    expect($solicitud->id_responsable)->toBe($responsable->id_usuario);
});

test('HU5: el administrador puede actualizar el estado de una solicitud y se registra en historial', function () {
    $solicitud = Solicitud::first();
    $estadoCompletada = EstadoSolicitud::where('nombre', EstadoSolicitud::COMPLETADA)->first();

    $response = $this->post(route('admin.solicitudes.actualizarEstado', $solicitud->id_solicitud), [
        'id_estado' => $estadoCompletada->id_estado,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $solicitud->refresh();
    expect($solicitud->id_estado)->toBe($estadoCompletada->id_estado);

    // Verificar que se haya registrado en el historial de estados
    $this->assertDatabaseHas('historial_estados', [
        'id_solicitud' => $solicitud->id_solicitud,
        'id_estado' => $estadoCompletada->id_estado,
    ]);
});

test('el administrador puede ver el listado y detalle de una solicitud', function () {
    $solicitud = Solicitud::first();

    $indexResponse = $this->get(route('admin.solicitudes.index'));
    $indexResponse->assertStatus(200);

    $showResponse = $this->get(route('admin.solicitudes.show', $solicitud->id_solicitud));
    $showResponse->assertStatus(200);
    $showResponse->assertSee($solicitud->descripcion);
});
