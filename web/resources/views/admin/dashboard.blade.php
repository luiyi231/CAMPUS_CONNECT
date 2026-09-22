@extends('layouts.admin')

@section('title', 'Dashboard de Solicitudes')
@section('header_title', 'Dashboard de Solicitudes')
@section('header_subtitle', 'Resumen ejecutivo y control del estado de incidencias universitarias (HU1)')

@push('styles')
<style>
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .metric-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 22px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        position: relative;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .card-total::before { background: #3b82f6; }
    .card-recibida::before { background: var(--state-recibida); }
    .card-pendiente::before { background: var(--state-pendiente); }
    .card-enproceso::before { background: var(--state-enproceso); }
    .card-completada::before { background: var(--state-completada); }

    .metric-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .metric-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .metric-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .metric-count {
        font-size: 32px;
        font-weight: 800;
        color: var(--text-dark);
        line-height: 1;
        margin-bottom: 6px;
    }

    .metric-sub {
        font-size: 12px;
        color: var(--text-muted);
    }

    .dashboard-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    @media (max-width: 1024px) {
        .dashboard-layout {
            grid-template-columns: 1fr;
        }
    }

    /* Table styling */
    .table-container {
        overflow-x: auto;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }

    .custom-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .custom-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
        color: #334155;
    }

    .custom-table tr:hover td {
        background-color: #f8fafc;
    }

    .type-progress-item {
        margin-bottom: 16px;
    }

    .type-progress-header {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .type-progress-bar-bg {
        height: 8px;
        background-color: #f1f5f9;
        border-radius: 9999px;
        overflow: hidden;
    }

    .type-progress-bar-fill {
        height: 100%;
        border-radius: 9999px;
        background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
    }

    .empty-state {
        text-align: center;
        padding: 32px;
        color: var(--text-muted);
    }
</style>
@endpush

@section('content')

    <!-- HU1: Resumen de Solicitudes y Conteo por Estado -->
    <div class="metrics-grid">
        <!-- Total -->
        <a href="{{ route('admin.solicitudes.index') }}" style="text-decoration: none;">
            <div class="metric-card card-total">
                <div class="metric-header">
                    <span class="metric-title">Total Solicitudes</span>
                    <div class="metric-icon-box" style="background: #eff6ff; color: #2563eb;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-count">{{ $totalSolicitudes }}</div>
                <div class="metric-sub">Registradas en el campus</div>
            </div>
        </a>

        <!-- RECIBIDAS -->
        <a href="{{ route('admin.solicitudes.index', ['estado' => 1]) }}" style="text-decoration: none;">
            <div class="metric-card card-recibida">
                <div class="metric-header">
                    <span class="metric-title">Recibidas</span>
                    <div class="metric-icon-box" style="background: var(--state-recibida-bg); color: var(--state-recibida);">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-count" style="color: var(--state-recibida);">
                    {{ $estadosConteo['RECIBIDA'] ?? 0 }}
                </div>
                <div class="metric-sub">Nuevos reclamos de estudiantes</div>
            </div>
        </a>

        <!-- PENDIENTES -->
        <a href="{{ route('admin.solicitudes.index', ['estado' => 2]) }}" style="text-decoration: none;">
            <div class="metric-card card-pendiente">
                <div class="metric-header">
                    <span class="metric-title">Pendientes</span>
                    <div class="metric-icon-box" style="background: var(--state-pendiente-bg); color: var(--state-pendiente);">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-count" style="color: var(--state-pendiente);">
                    {{ $estadosConteo['PENDIENTE'] ?? 0 }}
                </div>
                <div class="metric-sub">En espera de asignación/revisión</div>
            </div>
        </a>

        <!-- EN-PROCESO -->
        <a href="{{ route('admin.solicitudes.index', ['estado' => 3]) }}" style="text-decoration: none;">
            <div class="metric-card card-enproceso">
                <div class="metric-header">
                    <span class="metric-title">En Proceso</span>
                    <div class="metric-icon-box" style="background: var(--state-enproceso-bg); color: var(--state-enproceso);">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-count" style="color: var(--state-enproceso);">
                    {{ $estadosConteo['EN-PROCESO'] ?? 0 }}
                </div>
                <div class="metric-sub">Técnicos resolviendo activamente</div>
            </div>
        </a>

        <!-- COMPLETADAS -->
        <a href="{{ route('admin.solicitudes.index', ['estado' => 4]) }}" style="text-decoration: none;">
            <div class="metric-card card-completada">
                <div class="metric-header">
                    <span class="metric-title">Completadas</span>
                    <div class="metric-icon-box" style="background: var(--state-completada-bg); color: var(--state-completada);">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-count" style="color: var(--state-completada);">
                    {{ $estadosConteo['COMPLETADA'] ?? 0 }}
                </div>
                <div class="metric-sub">Resueltas exitosamente</div>
            </div>
        </a>
    </div>

    <!-- Layout Dividido: Tabla Reciente y Distribución por Tipo -->
    <div class="dashboard-layout">
        <!-- Panel Izquierdo: Solicitudes Recientes -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Solicitudes Recientes</h3>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                        Últimos reclamos recibidos y su responsable asignado (HU2, HU5)
                    </p>
                </div>
                <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-outline btn-sm">Ver Todas</a>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Estudiante</th>
                            <th>Tipo</th>
                            <th>Estado (HU1)</th>
                            <th>Responsable (HU2)</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudesRecientes as $sol)
                            <tr>
                                <td style="font-weight: 700; color: #1e293b;">#{{ $sol->id_solicitud }}</td>
                                <td>
                                    <div style="font-weight: 600;">{{ $sol->estudiante->nombre_completo ?? 'Estudiante' }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">{{ $sol->estudiante->correo ?? '' }}</div>
                                </td>
                                <td>
                                    <span class="badge-tipo">{{ $sol->tipo->nombre ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @php
                                        $estadoNombre = $sol->estado->nombre ?? 'RECIBIDA';
                                        $badgeClass = match($estadoNombre) {
                                            'RECIBIDA' => 'badge-recibida',
                                            'PENDIENTE' => 'badge-pendiente',
                                            'EN-PROCESO' => 'badge-en-proceso',
                                            'COMPLETADA' => 'badge-completada',
                                            default => 'badge-recibida',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $estadoNombre }}
                                    </span>
                                </td>
                                <td>
                                    @if($sol->responsable)
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span style="width: 24px; height: 24px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700;">
                                                {{ substr($sol->responsable->nombre, 0, 1) }}
                                            </span>
                                            <span style="font-weight: 500; font-size: 12px;">{{ $sol->responsable->nombre }} {{ $sol->responsable->apellido }}</span>
                                        </div>
                                    @else
                                        <span style="color: #ef4444; font-size: 11px; font-weight: 700; background: #fef2f2; padding: 2px 6px; border-radius: 4px; border: 1px dashed #fca5a5;">
                                            Sin Asignar
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.solicitudes.show', $sol->id_solicitud) }}" class="btn btn-outline btn-sm" style="padding: 4px 8px;">
                                        Gestionar &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">No hay solicitudes registradas actualmente.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Panel Derecho: Tipos y Alertas de Gestión -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Alerta Responsables Pendientes -->
            @if($sinResponsable > 0)
                <div class="card" style="border-left: 4px solid #ef4444; background: #fffaf0;">
                    <div class="card-body" style="padding: 18px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <div style="background: #fee2e2; color: #dc2626; border-radius: 8px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 style="font-size: 14px; font-weight: 700; color: #991b1b; margin-bottom: 4px;">
                                    {{ $sinResponsable }} Solicitud(es) sin asignar (HU2)
                                </h4>
                                <p style="font-size: 12px; color: #7f1d1d; margin-bottom: 12px;">
                                    Asigne un responsable para evitar incertidumbres y acelerar la resolución.
                                </p>
                                <a href="{{ route('admin.solicitudes.index', ['responsable' => 'sin_asignar']) }}" class="btn btn-primary btn-sm" style="background: #dc2626;">
                                    Asignar Ahora
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Distribución por Tipo de Solicitud (HU3) -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Por Tipo de Solicitud</h3>
                </div>
                <div class="card-body">
                    @foreach($tiposConteo as $tipo)
                        @php
                            $percentage = $totalSolicitudes > 0 ? round(($tipo->solicitudes_count / $totalSolicitudes) * 100) : 0;
                        @endphp
                        <div class="type-progress-item">
                            <div class="type-progress-header">
                                <span>{{ $tipo->nombre }}</span>
                                <span style="color: var(--text-muted);">{{ $tipo->solicitudes_count }} ({{ $percentage }}%)</span>
                            </div>
                            <div class="type-progress-bar-bg">
                                <div class="type-progress-bar-fill" style="width: {{ $percentage }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection
