<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'estados_solicitud';

    protected $primaryKey = 'id_estado';

    public const RECIBIDA = 'RECIBIDA';

    public const PENDIENTE = 'PENDIENTE';

    public const EN_PROCESO = 'EN-PROCESO';

    public const COMPLETADA = 'COMPLETADA';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Solicitudes en este estado actual.
     */
    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'id_estado', 'id_estado');
    }

    /**
     * Entradas en historial con este estado.
     */
    public function historiales(): HasMany
    {
        return $this->hasMany(HistorialEstado::class, 'id_estado', 'id_estado');
    }
}
