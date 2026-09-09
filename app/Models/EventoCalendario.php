<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoCalendario extends Model
{
    use HasFactory;

    protected $table = 'eventos_calendario';

    protected $fillable = [
        'proceso_id',
        'user_id',
        'titulo',
        'tipo_evento',
        'fecha_hora_inicio',
        'fecha_hora_fin',
        'lugar_enlace',
        'es_plazo_fatal',
        'prioridad',
        'estado',
        'observaciones',
        'notificado_telegram',
    ];

    protected $casts = [
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_fin' => 'datetime',
        'es_plazo_fatal' => 'boolean',
        'notificado_telegram' => 'boolean',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}