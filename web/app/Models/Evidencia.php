<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Evidencia extends Model
{
    use HasFactory;

    protected $table = 'evidencias';

    protected $primaryKey = 'id_evidencia';

    protected $fillable = [
        'id_solicitud',
        'archivo',
        'fecha_subida',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_subida' => 'datetime',
        ];
    }

    /**
     * Solicitud a la que pertenece esta evidencia.
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud', 'id_solicitud');
    }

    /**
     * URL accesible de la imagen / archivo.
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (filter_var($this->archivo, FILTER_VALIDATE_URL)) {
                    return $this->archivo;
                }

                return Storage::url($this->archivo);
            }
        );
    }
}
