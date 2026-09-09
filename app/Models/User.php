<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol_id',
        'cargo',
        'iniciales',
        'color',
        'telefono',
        'es_abogado',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'es_abogado' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function hasPermission(string $codigo): bool
    {
        if (!$this->rol) {
            return false;
        }

        // Director general has full access
        if ($this->rol->codigo === 'DIRECTOR') {
            return true;
        }

        return $this->rol->permisos->contains('codigo', $codigo);
    }

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class, 'abogado_id');
    }

    public function actuaciones(): HasMany
    {
        return $this->hasMany(Actuacion::class, 'user_id');
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(EventoCalendario::class, 'user_id');
    }

    public function auditorias(): HasMany
    {
        return $this->hasMany(AuditoriaProceso::class, 'user_id');
    }
}