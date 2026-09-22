<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialEstado extends Model
{
    use HasFactory;

    protected $table = 'historial_estados';

    protected $primaryKey = 'id_historial';

    protected $fillable = [
        'id_solicitud',
        'id_estado',
        'id_usuario',
        'fecha_cambio',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_cambio' => 'datetime',
        ];
    }

    /**
     * Solicitud relacionada al cambio de estado.
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud', 'id_solicitud');
    }

    /**
     * Estado que se asignó en esta transición.
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoSolicitud::class, 'id_estado', 'id_estado');
    }

    /**
     * Usuario (generalmente Administrador) que ejecutó el cambio.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
