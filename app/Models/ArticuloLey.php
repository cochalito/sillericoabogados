<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticuloLey extends Model
{
    use HasFactory;

    protected $table = 'articulos_ley';

    protected $fillable = [
        'codigo_normativo',
        'numero_articulo',
        'epigrafe_delito',
        'materia_id',
        'pena_minima_anos',
        'pena_maxima_anos',
        'texto_tipificacion',
        'activo',
    ];

    protected $casts = [
        'pena_minima_anos' => 'float',
        'pena_maxima_anos' => 'float',
        'activo' => 'boolean',
    ];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function procesosPrincipales(): HasMany
    {
        return $this->hasMany(Proceso::class, 'articulo_principal_id');
    }

    public function procesos(): BelongsToMany
    {
        return $this->belongsToMany(Proceso::class, 'proceso_articulos', 'articulo_id', 'proceso_id')
            ->withPivot('es_principal')
            ->withTimestamps();
    }
}
