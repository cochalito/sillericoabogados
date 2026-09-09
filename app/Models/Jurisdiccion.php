<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurisdiccion extends Model
{
    use HasFactory;

    protected $table = 'jurisdicciones';

    protected $fillable = [
        'codigo',
        'nombre',
        'departamento',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class, 'jurisdiccion_id');
    }

    public function juzgados(): HasMany
    {
        return $this->hasMany(Juzgado::class, 'jurisdiccion_id');
    }

    public function salas(): HasMany
    {
        return $this->hasMany(Sala::class, 'jurisdiccion_id');
    }
}
