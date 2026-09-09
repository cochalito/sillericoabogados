<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proceso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'procesos';

    protected $fillable = [
        'codigo_interno',
        'cud',
        'nurej',
        'ianus',
        'codigo_caso',
        'portal_fiscalia',
        'jurisdiccion',
        'materia',
        'cliente_id',
        'rol_cliente',
        'demandante_denunciante',
        'demandado_denunciado',
        'juzgado_tribunal',
        'sala',
        'autoridad_juez_fiscal',
        'investigador_asignado',
        'delito_accion',
        'etapa_procesal',
        'estado',
        'estado_detalle',
        'abogado_id',
        'fecha_inicio',
    ];

    protected $casts = [
        'portal_fiscalia' => 'boolean',
        'fecha_inicio' => 'date',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function abogado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }

    public function actuaciones(): HasMany
    {
        return $this->hasMany(Actuacion::class, 'proceso_id')->orderBy('fecha_hora', 'desc');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'proceso_id')->orderBy('created_at', 'desc');
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(EventoCalendario::class, 'proceso_id')->orderBy('fecha_hora_inicio', 'asc');
    }

    public function auditorias(): HasMany
    {
        return $this->hasMany(AuditoriaProceso::class, 'proceso_id')->orderBy('created_at', 'desc');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('codigo_interno', 'like', "%{$term}%")
              ->orWhere('cud', 'like', "%{$term}%")
              ->orWhere('nurej', 'like', "%{$term}%")
              ->orWhere('codigo_caso', 'like', "%{$term}%")
              ->orWhere('demandante_denunciante', 'like', "%{$term}%")
              ->orWhere('demandado_denunciado', 'like', "%{$term}%")
              ->orWhere('delito_accion', 'like', "%{$term}%")
              ->orWhere('juzgado_tribunal', 'like', "%{$term}%");
        });
    }

    public function scopeMateria(Builder $query, ?string $materia): Builder
    {
        if (empty($materia) || $materia === 'Todas') {
            return $query;
        }

        return $query->where('materia', $materia);
    }

    public function scopeEstado(Builder $query, ?string $estado): Builder
    {
        if (empty($estado) || $estado === 'Todos') {
            return $query;
        }

        return $query->where('estado', $estado);
    }
}