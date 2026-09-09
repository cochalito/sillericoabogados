<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradoPolicial extends Model
{
    use HasFactory;

    protected $table = 'grados_policiales';

    protected $fillable = [
        'codigo',
        'nombre',
        'abreviatura',
        'jerarquia_orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function investigadores(): HasMany
    {
        return $this->hasMany(Investigador::class, 'grado_id');
    }
}
