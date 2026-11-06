<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticuloLey extends Model
{
    use HasFactory;

    protected $table = 'articulos_ley';

    protected $fillable = [
        'codigo',
        'tipo',
        'autor',
        'titulo',
        'descripcion',
        'archivo_pdf',
        'fecha_publicacion',
        'estado',
    ];

    protected $casts = [
        'fecha_publicacion' => 'datetime',
    ];

    /**
     * Retorna la ruta física absoluta del archivo en el sistema compartido
     */
    public function getFilePath(): ?string
    {
        if (empty($this->archivo_pdf)) {
            return null;
        }

        // Ruta en sillerico-sistema (almacenamiento primario)
        $pathSistema = '/app/sillerico/sillerico-sistema/storage/app/public/'.$this->archivo_pdf;
        if (file_exists($pathSistema)) {
            return $pathSistema;
        }

        // Ruta local por fallback si en el futuro se replica
        $pathLocal = storage_path('app/public/'.$this->archivo_pdf);
        if (file_exists($pathLocal)) {
            return $pathLocal;
        }

        return null;
    }

    /**
     * Determina la extensión o tipo de archivo (PDF, DOCX, etc.)
     */
    public function getFileTypeAttribute(): ?string
    {
        if (empty($this->archivo_pdf)) {
            return null;
        }

        $ext = strtolower(pathinfo($this->archivo_pdf, PATHINFO_EXTENSION));

        return strtoupper($ext);
    }

    /**
     * Búsqueda por texto en código, autor, título o descripción
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('codigo', 'like', "%{$term}%")
                ->orWhere('autor', 'like', "%{$term}%")
                ->orWhere('titulo', 'like', "%{$term}%")
                ->orWhere('descripcion', 'like', "%{$term}%");
        });
    }

    /**
     * Scope para filtrar por estado activo
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', 'Activo');
    }

    /**
     * Scope para Leyes
     */
    public function scopeLeyes(Builder $query): Builder
    {
        return $query->where('tipo', 'LEY');
    }

    /**
     * Scope para Artículos Doctrinales
     */
    public function scopeArticulos(Builder $query): Builder
    {
        return $query->where('tipo', 'ARTICULO');
    }
}
