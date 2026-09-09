<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';

    protected $fillable = [
        'proceso_id',
        'actuacion_id',
        'nombre_original',
        'ruta_archivo',
        'mime_type',
        'peso_bytes',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    public function actuacion(): BelongsTo
    {
        return $this->belongsTo(Actuacion::class, 'actuacion_id');
    }

    public function getTamanoFormateadoAttribute(): string
    {
        $bytes = $this->peso_bytes;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}