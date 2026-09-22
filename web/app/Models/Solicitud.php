<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $primaryKey = 'id_solicitud';

    protected $fillable = [
        'id_estudiante',
        'id_responsable',
        'id_tipo',
        'id_estado',
        'descripcion',
        'fecha_creacion',
        'fecha_actualizacion',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_creacion' => 'datetime',
            'fecha_actualizacion' => 'datetime',
        ];
    }

    /**
     * Estudiante que registró la solicitud.
     */
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_estudiante', 'id_usuario');
    }

    /**
     * Usuario responsable asignado a resolver la solicitud.
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_responsable', 'id_usuario');
    }

    /**
     * Tipo o categoría de la solicitud (Mantenimiento, soporte, etc.).
     */
    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoSolicitud::class, 'id_tipo', 'id_tipo');
    }

    /**
     * Estado actual de la solicitud (RECIBIDA, PENDIENTE, EN-PROCESO, COMPLETADA).
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoSolicitud::class, 'id_estado', 'id_estado');
    }

    /**
     * Evidencias fotográficas asociadas.
     */
    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class, 'id_solicitud', 'id_solicitud');
    }

    /**
     * Historial de transiciones de estado de la solicitud.
     */
    public function historial(): HasMany
    {
        return $this->hasMany(HistorialEstado::class, 'id_solicitud', 'id_solicitud')
            ->orderBy('fecha_cambio', 'desc');
    }

    /**
     * Registrar cambio de estado y registrar en historial automáticamente.
     */
    public function cambiarEstado(int $nuevoEstadoId, int $usuarioId): HistorialEstado
    {
        $this->update([
            'id_estado' => $nuevoEstadoId,
            'fecha_actualizacion' => now(),
        ]);

        return $this->historial()->create([
            'id_estado' => $nuevoEstadoId,
            'id_usuario' => $usuarioId,
            'fecha_cambio' => now(),
        ]);
    }
}
