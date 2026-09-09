<?php

namespace App\Http\Controllers\Api;

use App\Models\Proceso;
use App\Models\Actuacion;
use App\Models\Documento;
use App\Models\EstadoProceso;
use App\Models\EtapaProcesal;
use App\Models\Jurisdiccion;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProcesoController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Proceso::with([
            'materia',
            'jurisdiccion',
            'demandante',
            'demandado',
            'cliente',
            'rolCliente',
            'articuloPrincipal',
            'articulos',
            'juez',
            'juzgado',
            'sala',
            'investigador.grado',
            'etapaProcesal',
            'estado',
            'abogado',
        ])
        ->withCount(['actuaciones', 'eventos', 'documentos'])
        ->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->materia_id);
        }

        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        if ($request->filled('etapa_procesal_id')) {
            $query->where('etapa_procesal_id', $request->etapa_procesal_id);
        }

        if ($request->filled('abogado_id')) {
            $query->where('abogado_id', $request->abogado_id);
        }

        if ($request->filled('juzgado_id')) {
            $query->where('juzgado_id', $request->juzgado_id);
        }

        if ($request->filled('juez_id')) {
            $query->where('juez_id', $request->juez_id);
        }

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->boolean('paginate', true)) {
            $perPage = (int) $request->input('per_page', 15);
            return $this->sendResponse($query->paginate($perPage));
        }

        return $this->sendResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $proceso = Proceso::with([
            'materia',
            'jurisdiccion',
            'demandante',
            'demandado',
            'cliente',
            'rolCliente',
            'articuloPrincipal',
            'articulos',
            'juez',
            'juzgado',
            'sala',
            'investigador.grado',
            'etapaProcesal',
            'estado',
            'abogado',
            'actuaciones.user',
            'actuaciones.documentos',
            'eventos',
            'documentos',
            'auditorias.user',
        ])->find($id);

        if (!$proceso) {
            return $this->sendError('Proceso no encontrado.', [], 404);
        }

        return $this->sendResponse($proceso);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo_interno'        => 'nullable|string|max:50|unique:procesos,codigo_interno',
            'cud'                   => 'nullable|string|max:100',
            'nurej'                 => 'nullable|string|max:100',
            'ianus'                 => 'nullable|string|max:100',
            'codigo_caso'           => 'nullable|string|max:100',
            'portal_fiscalia'       => 'boolean',
            'materia_id'            => 'required|exists:materias,id',
            'jurisdiccion_id'       => 'nullable|exists:jurisdicciones,id',
            'demandante_id'         => 'required|exists:sujetos_procesales,id',
            'demandado_id'          => 'required|exists:sujetos_procesales,id',
            'cliente_id'            => 'nullable|exists:sujetos_procesales,id',
            'rol_cliente_id'        => 'required|exists:roles_partes,id',
            'articulo_principal_id' => 'nullable|exists:articulos_ley,id',
            'juez_id'               => 'nullable|exists:jueces,id',
            'juzgado_id'            => 'nullable|exists:juzgados,id',
            'sala_id'               => 'nullable|exists:salas,id',
            'investigador_id'       => 'nullable|exists:investigadores,id',
            'etapa_procesal_id'     => 'nullable|exists:etapas_procesales,id',
            'estado_id'             => 'nullable|exists:estados_proceso,id',
            'situacion_actual'      => 'nullable|string',
            'abogado_id'            => 'nullable|exists:users,id',
            'fecha_inicio'          => 'nullable|date',
            'articulo_ids'          => 'nullable|array',
            'articulo_ids.*'        => 'exists:articulos_ley,id',
        ]);

        // Default cliente_id al demandante si no se especifica
        if (empty($validated['cliente_id'])) {
            $validated['cliente_id'] = $validated['demandante_id'];
        }

        // Default jurisdiccion_id a 1 (La Paz) si no se especifica
        if (empty($validated['jurisdiccion_id'])) {
            $firstJuris = Jurisdiccion::first();
            $validated['jurisdiccion_id'] = $firstJuris ? $firstJuris->id : 1;
        }

        // Default estado_id si no se especifica
        if (empty($validated['estado_id'])) {
            $firstEstado = EstadoProceso::where('activo', true)->orderBy('orden')->first();
            $validated['estado_id'] = $firstEstado ? $firstEstado->id : 1;
        }

        // Default etapa_procesal_id si no se especifica (primera etapa de la materia seleccionada)
        if (empty($validated['etapa_procesal_id'])) {
            $firstEtapa = EtapaProcesal::where('materia_id', $validated['materia_id'])
                ->where('activo', true)
                ->orderBy('orden')
                ->first();
            $validated['etapa_procesal_id'] = $firstEtapa ? $firstEtapa->id : (EtapaProcesal::first()->id ?? 1);
        }

        // Auto código interno
        if (empty($validated['codigo_interno'])) {
            $year = date('Y');
            $lastId = Proceso::withTrashed()->max('id') ?? 0;
            $validated['codigo_interno'] = sprintf('EXP-%s-%04d', $year, $lastId + 1);
        }

        DB::beginTransaction();
        try {
            $proceso = Proceso::create($validated);

            // Concurso de artículos
            if (!empty($validated['articulo_ids'])) {
                $attachData = [];
                $principalId = $validated['articulo_principal_id'] ?? null;
                foreach ($validated['articulo_ids'] as $artId) {
                    $attachData[$artId] = ['es_principal' => ($artId == $principalId)];
                }
                $proceso->articulos()->sync($attachData);
            } elseif (!empty($validated['articulo_principal_id'])) {
                $proceso->articulos()->sync([
                    $validated['articulo_principal_id'] => ['es_principal' => true]
                ]);
            }

            AuditService::log(
                $proceso->id,
                'CREACION_PROCESO',
                "Se creó el expediente {$proceso->codigo_interno}",
                ['data' => $validated]
            );

            DB::commit();

            return $this->sendResponse(
                $proceso->load(['materia', 'estado', 'cliente', 'abogado', 'articuloPrincipal', 'articulos']),
                'Proceso creado exitosamente.',
                201
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError('Error al crear el proceso: ' . $e->getMessage(), [], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $proceso = Proceso::find($id);

        if (!$proceso) {
            return $this->sendError('Proceso no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'codigo_interno'        => ['sometimes', 'required', 'string', 'max:50', Rule::unique('procesos')->ignore($proceso->id)],
            'cud'                   => 'nullable|string|max:100',
            'nurej'                 => 'nullable|string|max:100',
            'ianus'                 => 'nullable|string|max:100',
            'codigo_caso'           => 'nullable|string|max:100',
            'portal_fiscalia'       => 'boolean',
            'materia_id'            => 'nullable|exists:materias,id',
            'jurisdiccion_id'       => 'nullable|exists:jurisdicciones,id',
            'demandante_id'         => 'nullable|exists:sujetos_procesales,id',
            'demandado_id'          => 'nullable|exists:sujetos_procesales,id',
            'cliente_id'            => 'nullable|exists:sujetos_procesales,id',
            'rol_cliente_id'        => 'nullable|exists:roles_partes,id',
            'articulo_principal_id' => 'nullable|exists:articulos_ley,id',
            'juez_id'               => 'nullable|exists:jueces,id',
            'juzgado_id'            => 'nullable|exists:juzgados,id',
            'sala_id'               => 'nullable|exists:salas,id',
            'investigador_id'       => 'nullable|exists:investigadores,id',
            'etapa_procesal_id'     => 'nullable|exists:etapas_procesales,id',
            'estado_id'             => 'nullable|exists:estados_proceso,id',
            'situacion_actual'      => 'nullable|string',
            'abogado_id'            => 'nullable|exists:users,id',
            'fecha_inicio'          => 'nullable|date',
            'articulo_ids'          => 'nullable|array',
            'articulo_ids.*'        => 'exists:articulos_ley,id',
        ]);

        DB::beginTransaction();
        try {
            $proceso->update($validated);

            if ($request->has('articulo_ids')) {
                $attachData = [];
                $principalId = $request->input('articulo_principal_id', $proceso->articulo_principal_id);
                foreach ($request->articulo_ids as $artId) {
                    $attachData[$artId] = ['es_principal' => ($artId == $principalId)];
                }
                $proceso->articulos()->sync($attachData);
            }

            AuditService::log(
                $proceso->id,
                'ACTUALIZACION_PROCESO',
                "Se actualizaron los datos del expediente {$proceso->codigo_interno}",
                ['cambios' => $proceso->getChanges()]
            );

            DB::commit();

            return $this->sendResponse(
                $proceso->load(['materia', 'estado', 'cliente', 'abogado', 'articuloPrincipal', 'articulos']),
                'Proceso actualizado exitosamente.'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError('Error al actualizar el proceso: ' . $e->getMessage(), [], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $proceso = Proceso::find($id);

        if (!$proceso) {
            return $this->sendError('Proceso no encontrado.', [], 404);
        }

        AuditService::log(
            $proceso->id,
            'ELIMINACION_PROCESO',
            "Se envió a la papelera el expediente {$proceso->codigo_interno}"
        );

        $proceso->delete();

        return $this->sendResponse(null, 'Proceso archivado en papelera correctamente.');
    }

    public function syncArticulos(Request $request, int $id): JsonResponse
    {
        $proceso = Proceso::find($id);

        if (!$proceso) {
            return $this->sendError('Proceso no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'articulos'                => 'required|array',
            'articulos.*.articulo_id'  => 'required|exists:articulos_ley,id',
            'articulos.*.es_principal' => 'boolean',
        ]);

        $syncData = [];
        $principalId = null;

        foreach ($validated['articulos'] as $art) {
            $isPrincipal = $art['es_principal'] ?? false;
            $syncData[$art['articulo_id']] = ['es_principal' => $isPrincipal];
            if ($isPrincipal) {
                $principalId = $art['articulo_id'];
            }
        }

        $proceso->articulos()->sync($syncData);

        if ($principalId) {
            $proceso->update(['articulo_principal_id' => $principalId]);
        }

        AuditService::log(
            $proceso->id,
            'VINCULACION_ARTICULOS',
            'Se modificaron los artículos de ley vinculados al expediente'
        );

        return $this->sendResponse(
            $proceso->load(['articuloPrincipal', 'articulos']),
            'Artículos vinculados correctamente al expediente.'
        );
    }

    public function storeActuacion(Request $request, int $id): JsonResponse
    {
        $proceso = Proceso::find($id);

        if (!$proceso) {
            return $this->sendError('Proceso no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'titulo_actuacion' => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'fecha_hora'       => 'nullable|date',
            'etapa_procesal'   => 'nullable|string|max:100',
            'user_id'          => 'nullable|exists:users,id',
            'es_hito_clave'    => 'boolean',
        ]);

        $currentUser = AuditService::getCurrentUser();
        $validated['proceso_id'] = $proceso->id;
        $validated['user_id'] = $validated['user_id'] ?? ($currentUser ? $currentUser->id : 1);
        $validated['fecha_hora'] = $validated['fecha_hora'] ?? now();

        $actuacion = Actuacion::create($validated);

        AuditService::log(
            $proceso->id,
            'NUEVA_ACTUACION',
            "Nueva actuación: {$actuacion->titulo_actuacion}",
            ['actuacion_id' => $actuacion->id]
        );

        return $this->sendResponse($actuacion->load('user'), 'Actuación registrada correctamente.', 201);
    }
}