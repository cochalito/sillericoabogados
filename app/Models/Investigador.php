<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Investigador extends Model
{
    use HasFactory;

    protected $table = 'investigadores';

    protected $fillable = [
        'grado_id',
        'nombres',
        'apellidos',
        'division',
        'celular_contacto',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function grado(): BelongsTo
    {
        return $this->belongsTo(GradoPolicial::class, 'grado_id');
    }

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class, 'investigador_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        $grado = $this->grado ? $this->grado->abreviatura : '';
        return trim("{$grado} {$this->nombres} {$this->apellidos}");
    }
}
