<?php

namespace App\Http\Controllers\Api;

use App\Models\EstadoProceso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EstadoProcesoController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = EstadoProceso::withCount('procesos')->orderBy('orden')->orderBy('nombre');

        if ($request->boolean('solo_activos', true)) {
            $query->where('activo', true);
        }

        if ($request->filled('tipo_agrupador')) {
            $query->where('tipo_agrupador', $request->tipo_agrupador);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('codigo', 'like', "%{$term}%")
                  ->orWhere('descripcion_estado', 'like', "%{$term}%");
            });
        }

        return $this->sendResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $estado = EstadoProceso::withCount('procesos')->find($id);

        if (!$estado) {
            return $this->sendError('Estado de proceso no encontrado.', [], 404);
        }

        return $this->sendResponse($estado);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo'             => 'nullable|string|max:50|unique:estados_proceso,codigo',
            'nombre'             => 'required|string|max:100|unique:estados_proceso,nombre',
            'tipo_agrupador'     => 'nullable|string|max:50',
            'descripcion_estado' => 'nullable|string',
            'color_badge'        => 'nullable|string|max:80',
            'orden'              => 'nullable|integer',
            'activo'             => 'boolean',
        ]);

        if (empty($validated['codigo'])) {
            $validated['codigo'] = Str::upper(Str::slug($validated['nombre'], '_'));
        }

        $estado = EstadoProceso::create($validated);

        return $this->sendResponse($estado, 'Estado de proceso creado exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $estado = EstadoProceso::find($id);

        if (!$estado) {
            return $this->sendError('Estado de proceso no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'codigo'             => ['sometimes', 'required', 'string', 'max:50', Rule::unique('estados_proceso')->ignore($estado->id)],
            'nombre'             => ['sometimes', 'required', 'string', 'max:100', Rule::unique('estados_proceso')->ignore($estado->id)],
            'tipo_agrupador'     => 'nullable|string|max:50',
            'descripcion_estado' => 'nullable|string',
            'color_badge'        => 'nullable|string|max:80',
            'orden'              => 'nullable|integer',
            'activo'             => 'boolean',
        ]);

        $estado->update($validated);

        return $this->sendResponse($estado, 'Estado de proceso actualizado exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $estado = EstadoProceso::withCount('procesos')->find($id);

        if (!$estado) {
            return $this->sendError('Estado de proceso no encontrado.', [], 404);
        }

        if ($estado->procesos_count > 0) {
            return $this->sendError(
                "No se puede eliminar el estado '{$estado->nombre}' porque está asignado a {$estado->procesos_count} proceso(s).",
                ['procesos_count' => $estado->procesos_count],
                422
            );
        }

        $estado->delete();

        return $this->sendResponse(null, 'Estado de proceso eliminado exitosamente.');
    }
}