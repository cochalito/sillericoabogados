<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actuacion extends Model
{
    use HasFactory;

    protected $table = 'actuaciones';

    protected $fillable = [
        'proceso_id',
        'user_id',
        'fecha_hora',
        'titulo_actuacion',
        'tipo_actuacion',
        'descripcion',
        'es_hito_relevante',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'es_hito_relevante' => 'boolean',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'actuacion_id');
    }
}