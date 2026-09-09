<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Juzgado extends Model
{
    use HasFactory;

    protected $table = 'juzgados';

    protected $fillable = [
        'jurisdiccion_id',
        'materia_id',
        'nombre',
        'edificio_direccion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function jurisdiccion(): BelongsTo
    {
        return $this->belongsTo(Jurisdiccion::class, 'jurisdiccion_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function jueces(): HasMany
    {
        return $this->hasMany(Juez::class, 'juzgado_id');
    }

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class, 'juzgado_id');
    }
}
