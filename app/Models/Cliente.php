<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'tipo_cliente',
        'nombre_razon_social',
        'documento_identidad',
        'telefono',
        'celular_whatsapp',
        'email',
        'direccion',
        'persona_contacto',
        'notas',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class, 'cliente_id');
    }
}