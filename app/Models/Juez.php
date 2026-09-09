<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Juez extends Model
{
    use HasFactory;

    protected $table = 'jueces';

    protected $fillable = [
        'tipo_autoridad',
        'nombre_completo',
        'juzgado_id',
        'telefono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function juzgado(): BelongsTo
    {
        return $this->belongsTo(Juzgado::class, 'juzgado_id');
    }

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class, 'juez_id');
    }
}
