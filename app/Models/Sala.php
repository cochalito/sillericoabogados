<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sala extends Model
{
    use HasFactory;

    protected $table = 'salas';

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

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class, 'sala_id');
    }
}
