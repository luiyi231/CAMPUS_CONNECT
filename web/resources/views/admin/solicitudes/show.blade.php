@extends('layouts.admin')

@section('title', "Solicitud #{$solicitud->id_solicitud}")
@section('header_title', "Gestión de Solicitud #{$solicitud->id_solicitud}")
@section('header_subtitle', "Detalle, asignación de responsable (HU2), cambio de estado (HU5) y evidencias (HU6)")

@push('styles')
<style>
    .details-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    @media (max-width: 1024px) {
        .details-layout {
            grid-template-columns: 1fr;
        }
    }

    .info-group {
        margin-bottom: 20px;
    }

    .info-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 14px;
        color: var(--text-dark);
        line-height: 1.5;
    }

    .info-card-highlight {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 16px;
        margin-bottom: 20px;
    }

    /* Galería de evidencias (HU6) */
    .evidence-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 14px;
        margin-top: 12px;
    }

    .evidence-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        aspect-ratio: 1;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        background: #0f172a;
    }

    .evidence-item:hover {
        transform: scale(1.02);
        box-shadow: var(--shadow-md);
    }

    .evidence-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .evidence-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 6px 8px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        font-size: 10px;
        font-weight: 500;
    }

    /* Timeline Historial Estados */
    .timeline {
        position: relative;
        padding-left: 28px;
        margin-top: 16px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 6px;
        bottom: 6px;
        width: 2px;
        background: var(--border-color);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-dot {
        position: absolute;
        left: -28px;
        top: 3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: white;
        border: 3px solid var(--primary);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
    }

    .timeline-content {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 10px 14px;
    }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    .timeline-date {
        font-size: 11px;
        color: var(--text-muted);
    }

    .timeline-user {
        font-size: 12px;
        color: var(--text-dark);
        font-weight: 600;
    }

    /* Form Styles */
    .action-panel {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        padding: 22px;
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
    }

    .action-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-select {
        width: 100%;
        height: 42px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0 12px;
        font-size: 14px;
        color: var(--text-dark);
        background: white;
        margin-bottom: 12px;
        outline: none;
    }

    .form-select:focus {
        border-color: var(--primary);
    }

    /* Modal Lightbox */
    .lightbox-modal {
        display: none;
        position: fixed;
        z-index: 100;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .lightbox-content {
        max-width: 90%;
        max-height: 85vh;
        border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
    }

    .lightbox-close {
        position: absolute;
        top: 24px;
        right: 28px;
        color: white;
        font-size: 32px;
        font-weight: bold;
        cursor: pointer;
    }
</style>
@endpush

@section('content')

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-outline btn-sm">
            &larr; Volver al listado
        </a>
    </div>

    <div class="details-layout">
        <!-- Columna Izquierda: Detalle de Solicitud, Evidencias e Historial -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Ficha Principal de la Solicitud -->
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 20px; font-weight: 800; color: #1e293b;">
                            Solicitud #{{ $solicitud->id_solicitud }}
                        </span>
                        <span class="badge-tipo" style="font-size: 13px; padding: 4px 10px;">
                            {{ $solicitud->tipo->nombre ?? 'N/A' }}
                        </span>
                    </div>

                    @php
                        $estadoNombre = $solicitud->estado->nombre ?? 'RECIBIDA';
                        $badgeClass = match($estadoNombre) {
                            'RECIBIDA' => 'badge-recibida',
                            'PENDIENTE' => 'badge-pendiente',
                            'EN-PROCESO' => 'badge-en-proceso',
                            'COMPLETADA' => 'badge-completada',
                            default => 'badge-recibida',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}" style="font-size: 13px; padding: 6px 14px;">
                        {{ $estadoNombre }}
                    </span>
                </div>

                <div class="card-body">
                    <!-- Datos del Estudiante -->
                    <div class="info-card-highlight">
                        <div class="info-label">Estudiante Solicitante (HU3)</div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 44px; height: 44px; background: #e0f2fe; color: #0369a1; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px;">
                                {{ substr($solicitud->estudiante->nombre ?? 'E', 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 15px; color: #0f172a;">
                                    {{ $solicitud->estudiante->nombre_completo ?? 'Estudiante Anónimo' }}
                                </div>
                                <div style="font-size: 13px; color: var(--text-muted);">
                                    📧 {{ $solicitud->estudiante->correo ?? 'Sin correo' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción de la Solicitud -->
                    <div class="info-group">
                        <div class="info-label">Descripción del Inconveniente</div>
                        <div style="background: white; border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; font-size: 14px; line-height: 1.6; color: #334155; white-space: pre-line;">
                            {{ $solicitud->descripcion }}
                        </div>
                    </div>

                    <!-- Metadatos de Fecha -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding-top: 12px; border-top: 1px solid var(--border-color);">
                        <div>
                            <span class="info-label">Fecha de Registro</span>
                            <div style="font-size: 13px; font-weight: 600; color: #475569;">
                                {{ $solicitud->fecha_creacion ? $solicitud->fecha_creacion->format('d/m/Y H:i:s') : 'N/D' }}
                            </div>
                        </div>
                        <div>
                            <span class="info-label">Última Actualización</span>
                            <div style="font-size: 13px; font-weight: 600; color: #475569;">
                                {{ $solicitud->fecha_actualizacion ? $solicitud->fecha_actualizacion->format('d/m/Y H:i:s') : 'N/D' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HU6: Evidencias Fotográficas -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Evidencias Fotográficas Adjuntas (HU6)</h3>
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                            Archivos adjuntados por el estudiante para complementar la información
                        </p>
                    </div>
                    <span style="font-size: 12px; font-weight: 700; background: #eff6ff; color: #2563eb; padding: 4px 10px; border-radius: 9999px;">
                        {{ $solicitud->evidencias->count() }} Fotos
                    </span>
                </div>

                <div class="card-body">
                    @if($solicitud->evidencias->count() > 0)
                        <div class="evidence-grid">
                            @foreach($solicitud->evidencias as $evidencia)
                                <div class="evidence-item" onclick="openLightbox('{{ $evidencia->url }}')">
                                    <img src="{{ $evidencia->url }}" alt="Evidencia fotográfica #{{ $evidencia->id_evidencia }}" onerror="this.src='https://placehold.co/400x400/e2e8f0/475569?text=Imagen+No+Disponible'">
                                    <div class="evidence-overlay">
                                        {{ $evidencia->fecha_subida ? $evidencia->fecha_subida->format('d/m/Y') : 'Foto' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 28px; color: var(--text-muted); font-size: 13px;">
                            <svg style="width: 38px; height: 38px; margin: 0 auto 8px; opacity: 0.5;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <p>El estudiante no adjuntó evidencias fotográficas para esta solicitud.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Historial de Estados (Auditoría / Trazabilidad) -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Historial de Transiciones de Estado</h3>
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                            Trazabilidad completa de cambios generados por administradores o el sistema
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="timeline">
                        @forelse($solicitud->historial as $item)
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        @php
                                            $stName = $item->estado->nombre ?? 'N/A';
                                            $bCls = match($stName) {
                                                'RECIBIDA' => 'badge-recibida',
                                                'PENDIENTE' => 'badge-pendiente',
                                                'EN-PROCESO' => 'badge-en-proceso',
                                                'COMPLETADA' => 'badge-completada',
                                                default => 'badge-recibida',
                                            };
                                        @endphp
                                        <span class="badge {{ $bCls }}" style="font-size: 11px;">
                                            {{ $stName }}
                                        </span>
                                        <span class="timeline-date">
                                            {{ $item->fecha_cambio ? $item->fecha_cambio->format('d/m/Y H:i:s') : 'N/D' }}
                                        </span>
                                    </div>
                                    <div class="timeline-user">
                                        Modificado por: <strong>{{ $item->usuario->nombre_completo ?? 'Usuario del Sistema' }}</strong>
                                        <span style="font-size: 11px; color: var(--text-muted);">({{ $item->usuario->rol ?? 'SISTEMA' }})</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p style="color: var(--text-muted); font-size: 13px;">No hay historial registrado.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Acciones Administrativas (HU2 y HU5) -->
        <div style="display: flex; flex-direction: column; gap: 24px;">

            <!-- HU2: Asignar Responsable -->
            <div class="action-panel">
                <h4 class="action-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--primary);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Asignar Responsable (HU2)
                </h4>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 14px;">
                    Asigne a un técnico o administrador encargado para dar certidumbre y resolver el problema.
                </p>

                @if($solicitud->responsable)
                    <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 12px; margin-bottom: 14px;">
                        <span style="font-size: 11px; font-weight: 700; color: #4338ca; text-transform: uppercase;">Responsable Actual:</span>
                        <div style="font-weight: 700; font-size: 14px; color: #1e1b4b; margin-top: 2px;">
                            {{ $solicitud->responsable->nombre }} {{ $solicitud->responsable->apellido }}
                        </div>
                        <div style="font-size: 12px; color: #6366f1;">
                            {{ $solicitud->responsable->correo }} ({{ $solicitud->responsable->rol }})
                        </div>
                    </div>
                @else
                    <div style="background: #fef2f2; border: 1px dashed #f87171; border-radius: 8px; padding: 12px; margin-bottom: 14px; text-align: center;">
                        <span style="font-size: 12px; font-weight: 700; color: #dc2626;">
                            ⚠️ Actualmente sin responsable asignado
                        </span>
                    </div>
                @endif

                <form action="{{ route('admin.solicitudes.asignarResponsable', $solicitud->id_solicitud) }}" method="POST">
                    @csrf
                    <label for="id_responsable" class="info-label">Seleccionar Responsable</label>
                    <select name="id_responsable" id="id_responsable" class="form-select" required>
                        <option value="">-- Seleccione un usuario --</option>
                        @foreach($responsables as $resp)
                            <option value="{{ $resp->id_usuario }}" {{ $solicitud->id_responsable == $resp->id_usuario ? 'selected' : '' }}>
                                {{ $resp->nombre }} {{ $resp->apellido }} ({{ $resp->rol }})
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Guardar Asignación (HU2)
                    </button>
                </form>
            </div>

            <!-- HU5: Actualizar Estado de la Solicitud -->
            <div class="action-panel">
                <h4 class="action-title">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--state-enproceso);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualizar Estado (HU5)
                </h4>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 14px;">
                    Cambie el flujo de la solicitud entre <strong>RECIBIDA</strong>, <strong>PENDIENTE</strong>, <strong>EN-PROCESO</strong> y <strong>COMPLETADA</strong>.
                </p>

                <form action="{{ route('admin.solicitudes.actualizarEstado', $solicitud->id_solicitud) }}" method="POST">
                    @csrf
                    <label for="id_estado" class="info-label">Nuevo Estado</label>
                    <select name="id_estado" id="id_estado" class="form-select" required>
                        @foreach($estados as $est)
                            <option value="{{ $est->id_estado }}" {{ $solicitud->id_estado == $est->id_estado ? 'selected' : '' }}>
                                {{ $est->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary" style="width: 100%; background: var(--state-enproceso);">
                        Actualizar Estado (HU5)
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Modal Lightbox para Ampliar Evidencias -->
    <div id="lightboxModal" class="lightbox-modal" onclick="closeLightbox()">
        <span class="lightbox-close">&times;</span>
        <img id="lightboxImg" class="lightbox-content" src="" alt="Evidencia Ampliada">
    </div>

@endsection

@push('scripts')
<script>
    function openLightbox(url) {
        document.getElementById('lightboxImg').src = url;
        document.getElementById('lightboxModal').style.display = 'flex';
    }

    function closeLightbox() {
        document.getElementById('lightboxModal').style.display = 'none';
    }

    // Cerrar con Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeLightbox();
        }
    });
</script>
@endpush
