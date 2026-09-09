<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SujetoProcesal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sujetos_procesales';

    protected $fillable = [
        'tipo_persona',
        'nombre_razon_social',
        'documento_identidad',
        'persona_contacto',
        'celular_whatsapp',
        'email',
        'direccion',
        'es_cliente',
        'activo',
    ];

    protected $casts = [
        'es_cliente' => 'boolean',
        'activo' => 'boolean',
    ];

    public function procesosComoDemandante(): HasMany
    {
        return $this->hasMany(Proceso::class, 'demandante_id');
    }

    public function procesosComoDemandado(): HasMany
    {
        return $this->hasMany(Proceso::class, 'demandado_id');
    }

    public function procesosComoCliente(): HasMany
    {
        return $this->hasMany(Proceso::class, 'cliente_id');
    }

    // Aliases
    public function procesosCliente(): HasMany
    {
        return $this->procesosComoCliente();
    }

    public function procesosDemandante(): HasMany
    {
        return $this->procesosComoDemandante();
    }

    public function procesosDemandado(): HasMany
    {
        return $this->procesosComoDemandado();
    }

    public function todosLosProcesos()
    {
        return Proceso::where('demandante_id', $this->id)
            ->orWhere('demandado_id', $this->id)
            ->orWhere('cliente_id', $this->id);
    }
}