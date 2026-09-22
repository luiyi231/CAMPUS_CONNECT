<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EstadoSolicitud;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * HU1: Visualizar dashboard resumen de solicitudes y su distribución por estado.
     */
    public function index(): View
    {
        // Totales generales y por cada estado canónico (HU1)
        $totalSolicitudes = Solicitud::count();

        $estadosConteo = [
            EstadoSolicitud::RECIBIDA => Solicitud::whereHas('estado', fn ($q) => $q->where('nombre', EstadoSolicitud::RECIBIDA))->count(),
            EstadoSolicitud::PENDIENTE => Solicitud::whereHas('estado', fn ($q) => $q->where('nombre', EstadoSolicitud::PENDIENTE))->count(),
            EstadoSolicitud::EN_PROCESO => Solicitud::whereHas('estado', fn ($q) => $q->where('nombre', EstadoSolicitud::EN_PROCESO))->count(),
            EstadoSolicitud::COMPLETADA => Solicitud::whereHas('estado', fn ($q) => $q->where('nombre', EstadoSolicitud::COMPLETADA))->count(),
        ];

        // Solicitudes que requieren asignación urgente de responsable
        $sinResponsable = Solicitud::whereNull('id_responsable')->count();

        // Conteo por tipo de solicitud
        $tiposConteo = TipoSolicitud::withCount('solicitudes')->get();

        // Solicitudes recientes con relaciones para la tabla rápida
        $solicitudesRecientes = Solicitud::with(['estudiante', 'responsable', 'tipo', 'estado'])
            ->orderBy('id_solicitud', 'desc')
            ->take(8)
            ->get();

        // Lista de responsables disponibles para asignación rápida
        $responsables = User::whereIn('rol', ['RESPONSABLE', 'ADMINISTRADOR'])->get();
        $estados = EstadoSolicitud::all();

        return view('admin.dashboard', compact(
            'totalSolicitudes',
            'estadosConteo',
            'sinResponsable',
            'tiposConteo',
            'solicitudesRecientes',
            'responsables',
            'estados'
        ));
    }
}
