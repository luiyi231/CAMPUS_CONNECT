<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'contrasena',
        'rol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    /**
     * Get the password name for authentication.
     */
    public function getAuthPasswordName(): string
    {
        return 'contrasena';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'contrasena' => 'hashed',
        ];
    }

    /**
     * Nombre completo del usuario.
     */
    protected function nombreCompleto(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->nombre} {$this->apellido}")
        );
    }

    /**
     * Compatibilidad con email.
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->correo,
            set: fn ($value) => ['correo' => $value]
        );
    }

    /**
     * Compatibilidad con name.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->nombre} {$this->apellido}")
        );
    }

    /**
     * Solicitudes creadas como estudiante.
     */
    public function solicitudesCreadas(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'id_estudiante', 'id_usuario');
    }

    /**
     * Solicitudes asignadas como responsable.
     */
    public function solicitudesAsignadas(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'id_responsable', 'id_usuario');
    }

    /**
     * Historial de estados registrados por este usuario.
     */
    public function historialesRealizados(): HasMany
    {
        return $this->hasMany(HistorialEstado::class, 'id_usuario', 'id_usuario');
    }
}
