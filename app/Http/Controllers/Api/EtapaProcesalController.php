<?php

namespace App\Http\Controllers\Api;

use App\Models\EtapaProcesal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EtapaProcesalController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = EtapaProcesal::with('materia')
            ->withCount('procesos')
            ->orderBy('orden')
            ->orderBy('nombre');

        if ($request->boolean('solo_activas', true)) {
            $query->where('activo', true);
        }

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->materia_id);
        }

        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        return $this->sendResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $etapa = EtapaProcesal::with('materia')->withCount('procesos')->find($id);

        if (!$etapa) {
            return $this->sendError('Etapa procesal no encontrada.', [], 404);
        }

        return $this->sendResponse($etapa);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'materia_id'             => 'required|exists:materias,id',
            'nombre'                 => 'required|string|max:150',
            'orden'                  => 'nullable|integer',
            'dias_termino_sugerido'  => 'nullable|integer',
            'activo'                 => 'boolean',
        ]);

        $etapa = EtapaProcesal::create($validated);

        return $this->sendResponse($etapa->load('materia'), 'Etapa procesal creada exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $etapa = EtapaProcesal::find($id);

        if (!$etapa) {
            return $this->sendError('Etapa procesal no encontrada.', [], 404);
        }

        $validated = $request->validate([
            'materia_id'             => 'sometimes|required|exists:materias,id',
            'nombre'                 => 'sometimes|required|string|max:150',
            'orden'                  => 'nullable|integer',
            'dias_termino_sugerido'  => 'nullable|integer',
            'activo'                 => 'boolean',
        ]);

        $etapa->update($validated);

        return $this->sendResponse($etapa->load('materia'), 'Etapa procesal actualizada exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $etapa = EtapaProcesal::withCount('procesos')->find($id);

        if (!$etapa) {
            return $this->sendError('Etapa procesal no encontrada.', [], 404);
        }

        if ($etapa->procesos_count > 0) {
            return $this->sendError(
                "No se puede eliminar la etapa '{$etapa->nombre}' porque está vinculada a {$etapa->procesos_count} proceso(s).",
                ['procesos_count' => $etapa->procesos_count],
                422
            );
        }

        $etapa->delete();

        return $this->sendResponse(null, 'Etapa procesal eliminada exitosamente.');
    }
}