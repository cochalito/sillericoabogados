<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditoriaProceso extends Model
{
    use HasFactory;

    protected $table = 'auditorias_procesos';

    public $timestamps = false;

    protected $fillable = [
        'proceso_id',
        'user_id',
        'accion',
        'descripcion',
        'detalles',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'detalles' => 'array',
        'created_at' => 'datetime',
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