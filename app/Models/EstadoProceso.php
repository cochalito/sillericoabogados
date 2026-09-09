<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoProceso extends Model
{
    use HasFactory;

    protected $table = 'estados_proceso';

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo_agrupador',
        'descripcion_estado',
        'color_badge',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class, 'estado_id');
    }
}
