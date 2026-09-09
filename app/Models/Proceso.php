<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'materia_id',
        'jurisdiccion_id',
        'demandante_id',
        'demandado_id',
        'cliente_id',
        'rol_cliente_id',
        'articulo_principal_id',
        'juez_id',
        'juzgado_id',
        'sala_id',
        'investigador_id',
        'etapa_procesal_id',
        'estado_id',
        'situacion_actual',
        'abogado_id',
        'fecha_inicio',
    ];

    protected $casts = [
        'portal_fiscalia' => 'boolean',
        'fecha_inicio' => 'date',
    ];

    // ==========================================
    // RELACIONES PARAMÉTRICAS
    // ==========================================

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function jurisdiccion(): BelongsTo
    {
        return $this->belongsTo(Jurisdiccion::class, 'jurisdiccion_id');
    }

    public function demandante(): BelongsTo
    {
        return $this->belongsTo(SujetoProcesal::class, 'demandante_id');
    }

    public function demandado(): BelongsTo
    {
        return $this->belongsTo(SujetoProcesal::class, 'demandado_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(SujetoProcesal::class, 'cliente_id');
    }

    public function rolCliente(): BelongsTo
    {
        return $this->belongsTo(RolParte::class, 'rol_cliente_id');
    }

    public function articuloPrincipal(): BelongsTo
    {
        return $this->belongsTo(ArticuloLey::class, 'articulo_principal_id');
    }

    public function articulos(): BelongsToMany
    {
        return $this->belongsToMany(ArticuloLey::class, 'proceso_articulos', 'proceso_id', 'articulo_id')
            ->withPivot('es_principal')
            ->withTimestamps();
    }

    public function juez(): BelongsTo
    {
        return $this->belongsTo(Juez::class, 'juez_id');
    }

    public function juzgado(): BelongsTo
    {
        return $this->belongsTo(Juzgado::class, 'juzgado_id');
    }

    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class, 'sala_id');
    }

    public function investigador(): BelongsTo
    {
        return $this->belongsTo(Investigador::class, 'investigador_id');
    }

    public function etapaProcesal(): BelongsTo
    {
        return $this->belongsTo(EtapaProcesal::class, 'etapa_procesal_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoProceso::class, 'estado_id');
    }

    public function abogado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }

    // ==========================================
    // BITÁCORA Y DOCUMENTOS
    // ==========================================

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

    // ==========================================
    // ACCESSORS PARA COMPATIBILIDAD
    // ==========================================

    public function getDemandanteDenuncianteAttribute(): string
    {
        return $this->demandante ? $this->demandante->nombre_razon_social : 'Sin demandante';
    }

    public function getDemandadoDenunciadoAttribute(): string
    {
        return $this->demandado ? $this->demandado->nombre_razon_social : 'Sin demandado';
    }

    public function getDelitoAccionAttribute(): string
    {
        return $this->articuloPrincipal ? $this->articuloPrincipal->epigrafe_delito : 'Acción Procesal';
    }

    public function getEstadoBadgeAttribute(): string
    {
        return $this->estado ? $this->estado->nombre : 'En Trámite';
    }

    // ==========================================
    // SCOPES DE BÚSQUEDA
    // ==========================================

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
              ->orWhereHas('demandante', function ($sq) use ($term) {
                  $sq->where('nombre_razon_social', 'like', "%{$term}%");
              })
              ->orWhereHas('demandado', function ($sq) use ($term) {
                  $sq->where('nombre_razon_social', 'like', "%{$term}%");
              })
              ->orWhereHas('articuloPrincipal', function ($sq) use ($term) {
                  $sq->where('epigrafe_delito', 'like', "%{$term}%")
                     ->orWhere('numero_articulo', 'like', "%{$term}%");
              });
        });
    }
}