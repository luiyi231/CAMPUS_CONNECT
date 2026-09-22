<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'tipos_solicitud';

    protected $primaryKey = 'id_tipo';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Solicitudes que pertenecen a este tipo.
     */
    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'id_tipo', 'id_tipo');
    }
}
