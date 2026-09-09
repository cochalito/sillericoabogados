<?php

namespace App\Http\Controllers\Api;

use App\Models\ArticuloLey;
use App\Models\EstadoProceso;
use App\Models\EtapaProcesal;
use App\Models\GradoPolicial;
use App\Models\Investigador;
use App\Models\Juez;
use App\Models\Jurisdiccion;
use App\Models\Juzgado;
use App\Models\Materia;
use App\Models\Rol;
use App\Models\RolParte;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class MetaController extends ApiController
{
    public function bootstrap(): JsonResponse
    {
        return $this->sendResponse([
            'materias' => Materia::where('activo', true)->orderBy('nombre')->get(['id', 'codigo', 'nombre', 'color_hex', 'color_badge']),
            'jurisdicciones' => Jurisdiccion::where('activo', true)->orderBy('nombre')->get(['id', 'codigo', 'nombre', 'departamento']),
            'estados' => EstadoProceso::where('activo', true)->orderBy('orden')->get(['id', 'codigo', 'nombre', 'tipo_agrupador', 'color_badge', 'descripcion_estado']),
            'etapas' => EtapaProcesal::where('activo', true)->orderBy('orden')->get(['id', 'materia_id', 'nombre', 'orden', 'dias_termino_sugerido']),
            'roles_partes' => RolParte::where('activo', true)->orderBy('nombre')->get(['id', 'codigo', 'nombre']),
            'grados_policiales' => GradoPolicial::where('activo', true)->orderBy('jerarquia_orden')->get(['id', 'codigo', 'nombre', 'abreviatura']),
            'abogados' => User::where('es_abogado', true)->where('activo', true)->orderBy('name')->get(['id', 'name', 'cargo', 'iniciales', 'color']),
            'roles' => Rol::where('activo', true)->orderBy('id')->get(['id', 'codigo', 'nombre', 'color_badge']),
        ]);
    }
}