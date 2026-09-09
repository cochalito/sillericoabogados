<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    use HasFactory;

    protected $table = 'materias';

    protected $fillable = [
        'codigo',
        'nombre',
        'color_hex',
        'color_badge',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class, 'materia_id');
    }

    public function etapas(): HasMany
    {
        return $this->hasMany(EtapaProcesal::class, 'materia_id')->orderBy('orden');
    }

    public function juzgados(): HasMany
    {
        return $this->hasMany(Juzgado::class, 'materia_id');
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(ArticuloLey::class, 'materia_id');
    }
}
