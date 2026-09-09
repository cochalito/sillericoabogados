<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EtapaProcesal extends Model
{
    use HasFactory;

    protected $table = 'etapas_procesales';

    protected $fillable = [
        'materia_id',
        'orden',
        'nombre',
        'dias_termino_sugerido',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class, 'etapa_procesal_id');
    }
}
