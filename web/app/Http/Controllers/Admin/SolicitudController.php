<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EstadoSolicitud;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SolicitudController extends Controller
{
    /**
     * Listado general de solicitudes con filtros.
     */
    public function index(Request $request): View
    {
        $query = Solicitud::with(['estudiante', 'responsable', 'tipo', 'estado', 'evidencias']);

        // Filtro por Estado
        if ($request->filled('estado')) {
            $query->where('id_estado', $request->input('estado'));
        }

        // Filtro por Tipo
        if ($request->filled('tipo')) {
            $query->where('id_tipo', $request->input('tipo'));
        }

        // Filtro por Responsable
        if ($request->filled('responsable')) {
            if ($request->input('responsable') === 'sin_asignar') {
                $query->whereNull('id_responsable');
            } else {
                $query->where('id_responsable', $request->input('responsable'));
            }
        }

        // Búsqueda por texto en descripción o nombre de estudiante
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('descripcion', 'like', "%{$buscar}%")
                    ->orWhereHas('estudiante', function ($uq) use ($buscar) {
                        $uq->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('apellido', 'like', "%{$buscar}%")
                            ->orWhere('correo', 'like', "%{$buscar}%");
                    });
            });
        }

        $solicitudes = $query->orderBy('id_solicitud', 'desc')->paginate(12)->withQueryString();

        $estados = EstadoSolicitud::all();
        $tipos = TipoSolicitud::all();
        $responsables = User::whereIn('rol', ['RESPONSABLE', 'ADMINISTRADOR'])->get();

        return view('admin.solicitudes.index', compact('solicitudes', 'estados', 'tipos', 'responsables'));
    }

    /**
     * Detalle de la solicitud (evidencias HU6, historial de estados HU5, asignación HU2).
     */
    public function show(int $id): View
    {
        $solicitud = Solicitud::with([
            'estudiante',
            'responsable',
            'tipo',
            'estado',
            'evidencias',
            'historial.usuario',
            'historial.estado',
        ])->findOrFail($id);

        $estados = EstadoSolicitud::all();
        $responsables = User::whereIn('rol', ['RESPONSABLE', 'ADMINISTRADOR'])->get();

        return view('admin.solicitudes.show', compact('solicitud', 'estados', 'responsables'));
    }

    /**
     * HU2: Asignar un responsable a una solicitud.
     */
    public function asignarResponsable(Request $request, int $id): RedirectResponse
    {
        $solicitud = Solicitud::findOrFail($id);

        $validated = $request->validate([
            'id_responsable' => 'required|exists:usuarios,id_usuario',
        ], [
            'id_responsable.required' => 'Debe seleccionar un responsable válido.',
            'id_responsable.exists' => 'El usuario seleccionado no existe.',
        ]);

        $responsable = User::findOrFail($validated['id_responsable']);

        $solicitud->update([
            'id_responsable' => $responsable->id_usuario,
            'fecha_actualizacion' => now(),
        ]);

        // Si la solicitud estaba en RECIBIDA, sugerir o pasar a PENDIENTE de resolución
        $estadoRecibida = EstadoSolicitud::where('nombre', EstadoSolicitud::RECIBIDA)->first();
        $estadoPendiente = EstadoSolicitud::where('nombre', EstadoSolicitud::PENDIENTE)->first();

        if ($estadoRecibida && $estadoPendiente && $solicitud->id_estado === $estadoRecibida->id_estado && $request->boolean('avanzar_a_pendiente', true)) {
            $adminUser = Auth::user() ?? User::where('rol', 'ADMINISTRADOR')->first();
            $solicitud->cambiarEstado($estadoPendiente->id_estado, $adminUser ? $adminUser->id_usuario : $responsable->id_usuario);
        }

        return redirect()->back()->with('success', "Se asignó correctamente a {$responsable->nombre} {$responsable->apellido} como responsable.");
    }

    /**
     * HU5: Actualizar el estado de una solicitud (RECIBIDA, PENDIENTE, EN-PROCESO, COMPLETADA).
     */
    public function actualizarEstado(Request $request, int $id): RedirectResponse
    {
        $solicitud = Solicitud::findOrFail($id);

        $validated = $request->validate([
            'id_estado' => 'required|exists:estados_solicitud,id_estado',
        ], [
            'id_estado.required' => 'Debe seleccionar un estado.',
            'id_estado.exists' => 'El estado seleccionado no es válido.',
        ]);

        $nuevoEstado = EstadoSolicitud::findOrFail($validated['id_estado']);

        // Obtener usuario administrador que realiza la acción
        $usuarioEjecutor = Auth::user() ?? User::where('rol', 'ADMINISTRADOR')->first() ?? $solicitud->responsable ?? $solicitud->estudiante;

        $solicitud->cambiarEstado($nuevoEstado->id_estado, $usuarioEjecutor->id_usuario);

        return redirect()->back()->with('success', "El estado de la solicitud #{$solicitud->id_solicitud} se actualizó a '{$nuevoEstado->nombre}'.");
    }
}
