<?php

use App\Models\EstadoSolicitud;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use App\Models\User;
use Database\Seeders\CampusConnectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CampusConnectSeeder::class);
    Storage::fake('public');
});

test('HU3: un estudiante puede registrar una nueva solicitud con su respectivo tipo', function () {
    $estudiante = User::where('rol', 'ESTUDIANTE')->first();
    $tipo = TipoSolicitud::where('nombre', 'Mantenimiento')->first();

    $response = $this->postJson('/api/solicitudes', [
        'id_estudiante' => $estudiante->id_usuario,
        'id_tipo' => $tipo->id_tipo,
        'descripcion' => 'Se cayó la toma de corriente del bloque C aula 101 y saltan chispas.',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.id_tipo', $tipo->id_tipo);

    // Verificar que el estado inicial por defecto sea RECIBIDA
    $estadoRecibida = EstadoSolicitud::where('nombre', EstadoSolicitud::RECIBIDA)->first();
    $response->assertJsonPath('data.id_estado', $estadoRecibida->id_estado);

    // Verificar en base de datos
    $this->assertDatabaseHas('solicitudes', [
        'id_estudiante' => $estudiante->id_usuario,
        'id_tipo' => $tipo->id_tipo,
        'id_estado' => $estadoRecibida->id_estado,
    ]);

    // Verificar que se haya registrado en el historial inicial
    $this->assertDatabaseHas('historial_estados', [
        'id_estado' => $estadoRecibida->id_estado,
        'id_usuario' => $estudiante->id_usuario,
    ]);
});

test('HU4: un estudiante puede ver sus solicitudes y el estado en el que se encuentran', function () {
    $estudiante = User::where('rol', 'ESTUDIANTE')->first();

    $response = $this->getJson("/api/estudiantes/{$estudiante->id_usuario}/solicitudes");

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonStructure([
        'success',
        'estudiante' => ['id_usuario', 'nombre', 'correo'],
        'total',
        'data' => [
            '*' => [
                'id_solicitud',
                'id_tipo',
                'id_estado',
                'descripcion',
                'tipo' => ['id_tipo', 'nombre'],
                'estado' => ['id_estado', 'nombre'],
            ],
        ],
    ]);
});

test('HU6: un estudiante puede adjuntar evidencia fotográfica a su solicitud', function () {
    $solicitud = Solicitud::first();
    $fakeImage = UploadedFile::fake()->create('evidencia_danio.jpg', 500, 'image/jpeg');

    $response = $this->postJson("/api/solicitudes/{$solicitud->id_solicitud}/evidencias", [
        'archivo' => $fakeImage,
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.id_solicitud', $solicitud->id_solicitud);

    $archivoRuta = $response->json('data.archivo');
    Storage::disk('public')->assertExists($archivoRuta);

    $this->assertDatabaseHas('evidencias', [
        'id_solicitud' => $solicitud->id_solicitud,
        'archivo' => $archivoRuta,
    ]);
});
