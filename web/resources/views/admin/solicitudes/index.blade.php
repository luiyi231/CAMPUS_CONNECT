@extends('layouts.admin')

@section('title', 'Gestión de Solicitudes')
@section('header_title', 'Bandeja de Solicitudes')
@section('header_subtitle', 'Control y seguimiento integral de reclamos estudiantiles')

@push('styles')
<style>
    .filter-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 120px;
        gap: 16px;
        align-items: flex-end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .form-control {
        height: 40px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0 12px;
        font-size: 13px;
        color: var(--text-dark);
        outline: none;
        transition: border-color 0.15s ease;
        background: #fff;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

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
        padding: 14px 18px;
        border-bottom: 1px solid var(--border-color);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .custom-table td {
        padding: 16px 18px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .custom-table tr:hover td {
        background-color: #f8fafc;
    }

    .pagination-wrapper {
        padding: 16px 20px;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endpush

@section('content')

    <!-- Barra de Filtros -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.solicitudes.index') }}" class="filter-grid">
            <!-- Buscar -->
            <div class="form-group" style="grid-column: span 1;">
                <label for="buscar">Buscar</label>
                <input type="text" name="buscar" id="buscar" class="form-control" placeholder="Descripción o estudiante..." value="{{ request('buscar') }}">
            </div>

            <!-- Estado -->
            <div class="form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" class="form-control">
                    <option value="">-- Todos los estados --</option>
                    @foreach($estados as $est)
                        <option value="{{ $est->id_estado }}" {{ request('estado') == $est->id_estado ? 'selected' : '' }}>
                            {{ $est->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tipo -->
            <div class="form-group">
                <label for="tipo">Tipo</label>
                <select name="tipo" id="tipo" class="form-control">
                    <option value="">-- Todos los tipos --</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t->id_tipo }}" {{ request('tipo') == $t->id_tipo ? 'selected' : '' }}>
                            {{ $t->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Responsable -->
            <div class="form-group">
                <label for="responsable">Responsable</label>
                <select name="responsable" id="responsable" class="form-control">
                    <option value="">-- Todos --</option>
                    <option value="sin_asignar" {{ request('responsable') === 'sin_asignar' ? 'selected' : '' }}>⚠️ Sin Asignar</option>
                    @foreach($responsables as $resp)
                        <option value="{{ $resp->id_usuario }}" {{ request('responsable') == $resp->id_usuario ? 'selected' : '' }}>
                            {{ $resp->nombre }} {{ $resp->apellido }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Botón Filtrar -->
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="height: 40px; width: 100%;">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla Principal de Solicitudes -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Listado de Solicitudes ({{ $solicitudes->total() }})</h3>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                    Seleccione una solicitud para asignar responsable (HU2) o cambiar estado (HU5)
                </p>
            </div>
            @if(request()->anyFilled(['estado', 'tipo', 'responsable', 'buscar']))
                <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-outline btn-sm">Limpiar Filtros</a>
            @endif
        </div>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Estudiante</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Estado (HU1)</th>
                        <th>Responsable (HU2)</th>
                        <th>Evidencias (HU6)</th>
                        <th>Fecha</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $sol)
                        <tr>
                            <td style="font-weight: 700; color: #1e293b;">#{{ $sol->id_solicitud }}</td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;">{{ $sol->estudiante->nombre_completo ?? 'Estudiante' }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $sol->estudiante->correo ?? '' }}</div>
                            </td>
                            <td>
                                <span class="badge-tipo">{{ $sol->tipo->nombre ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div style="max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #334155;" title="{{ $sol->descripcion }}">
                                    {{ $sol->descripcion }}
                                </div>
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
                                        <span style="width: 26px; height: 26px; background: #e0e7ff; color: #3730a3; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;">
                                            {{ substr($sol->responsable->nombre, 0, 1) }}
                                        </span>
                                        <span style="font-weight: 600; font-size: 12px; color: #1e293b;">
                                            {{ $sol->responsable->nombre }} {{ $sol->responsable->apellido }}
                                        </span>
                                    </div>
                                @else
                                    <span style="display: inline-block; color: #dc2626; background: #fef2f2; border: 1px dashed #f87171; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">
                                        ⚠️ Sin Responsable
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($sol->evidencias->count() > 0)
                                    <span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                                        📸 {{ $sol->evidencias->count() }}
                                    </span>
                                @else
                                    <span style="color: #94a3b8; font-size: 11px;">-</span>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: var(--text-muted);">
                                {{ $sol->fecha_creacion ? $sol->fecha_creacion->format('d/m/Y H:i') : '' }}
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.solicitudes.show', $sol->id_solicitud) }}" class="btn btn-primary btn-sm">
                                    Ver Detalle &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 48px; color: var(--text-muted);">
                                No se encontraron solicitudes con los criterios de búsqueda seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($solicitudes->hasPages())
            <div class="pagination-wrapper">
                {{ $solicitudes->links() }}
            </div>
        @endif
    </div>

@endsection
