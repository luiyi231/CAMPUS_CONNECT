<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EstadoSolicitud;
use App\Models\Evidencia;
use App\Models\HistorialEstado;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SolicitudApiController extends Controller
{
    /**
     * HU3: Registrar una nueva solicitud con su respectivo tipo y opcional evidencia fotográfica (HU6).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_estudiante' => 'required|exists:usuarios,id_usuario',
            'id_tipo' => 'required|exists:tipos_solicitud,id_tipo',
            'descripcion' => 'required|string|min:10',
            'evidencias.*' => 'nullable|image|max:10240', // hasta 10MB por foto
        ]);

        // Estado inicial obligatorio: RECIBIDA
        $estadoInicial = EstadoSolicitud::firstOrCreate(
            ['nombre' => EstadoSolicitud::RECIBIDA]
        );

        $solicitud = Solicitud::create([
            'id_estudiante' => $validated['id_estudiante'],
            'id_responsable' => null,
            'id_tipo' => $validated['id_tipo'],
            'id_estado' => $estadoInicial->id_estado,
            'descripcion' => $validated['descripcion'],
            'fecha_creacion' => now(),
            'fecha_actualizacion' => now(),
        ]);

        // Registrar entrada inicial en HISTORIAL_ESTADO
        HistorialEstado::create([
            'id_solicitud' => $solicitud->id_solicitud,
            'id_estado' => $estadoInicial->id_estado,
            'id_usuario' => $validated['id_estudiante'],
            'fecha_cambio' => now(),
        ]);

        // HU6: Si se adjuntaron fotos al momento del registro
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                $path = $file->store('evidencias', 'public');
                Evidencia::create([
                    'id_solicitud' => $solicitud->id_solicitud,
                    'archivo' => $path,
                    'fecha_subida' => now(),
                ]);
            }
        }

        $solicitud->load(['estudiante', 'tipo', 'estado', 'evidencias', 'historial']);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud registrada exitosamente en estado RECIBIDA.',
            'data' => $solicitud,
        ], 201);
    }

    /**
     * HU4: Ver las solicitudes de un estudiante y su estado actual.
     */
    public function misSolicitudes(int $id_estudiante): JsonResponse
    {
        $estudiante = User::findOrFail($id_estudiante);

        $solicitudes = Solicitud::with(['tipo', 'estado', 'responsable', 'evidencias', 'historial.estado'])
            ->where('id_estudiante', $estudiante->id_usuario)
            ->orderBy('id_solicitud', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'estudiante' => [
                'id_usuario' => $estudiante->id_usuario,
                'nombre' => $estudiante->nombre_completo,
                'correo' => $estudiante->correo,
            ],
            'total' => $solicitudes->count(),
            'data' => $solicitudes,
        ]);
    }

    /**
     * Ver detalle de una solicitud específica.
     */
    public function show(int $id): JsonResponse
    {
        $solicitud = Solicitud::with([
            'estudiante',
            'responsable',
            'tipo',
            'estado',
            'evidencias',
            'historial.estado',
            'historial.usuario',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $solicitud,
        ]);
    }

    /**
     * HU6: Adjuntar evidencia fotográfica a una solicitud existente.
     */
    public function adjuntarEvidencia(Request $request, int $id): JsonResponse
    {
        $solicitud = Solicitud::findOrFail($id);

        $request->validate([
            'archivo' => 'required|image|max:10240',
        ], [
            'archivo.required' => 'Debe enviar un archivo de imagen.',
            'archivo.image' => 'El archivo debe ser una imagen válida (jpeg, png, bmp, gif, svg o webp).',
        ]);

        $path = $request->file('archivo')->store('evidencias', 'public');

        $evidencia = Evidencia::create([
            'id_solicitud' => $solicitud->id_solicitud,
            'archivo' => $path,
            'fecha_subida' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Evidencia fotográfica adjuntada correctamente.',
            'data' => [
                'id_evidencia' => $evidencia->id_evidencia,
                'id_solicitud' => $evidencia->id_solicitud,
                'archivo' => $evidencia->archivo,
                'url' => $evidencia->url,
                'fecha_subida' => $evidencia->fecha_subida,
            ],
        ], 201);
    }

    /**
     * Catálogos para la aplicación móvil (tipos y estados).
     */
    public function catalogos(): JsonResponse
    {
        return response()->json([
            'tipos' => TipoSolicitud::all(),
            'estados' => EstadoSolicitud::all(),
        ]);
    }
}
