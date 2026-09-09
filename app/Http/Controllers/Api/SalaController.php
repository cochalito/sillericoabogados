<?php

namespace App\Http\Controllers\Api;

use App\Models\Sala;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalaController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Sala::with(['jurisdiccion', 'materia'])
            ->withCount('procesos')
            ->orderBy('nombre');

        if ($request->boolean('solo_activas', true)) {
            $query->where('activo', true);
        }

        if ($request->filled('jurisdiccion_id')) {
            $query->where('jurisdiccion_id', $request->jurisdiccion_id);
        }

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->materia_id);
        }

        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('edificio_direccion', 'like', '%' . $request->search . '%');
        }

        return $this->sendResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $sala = Sala::with(['jurisdiccion', 'materia'])->withCount('procesos')->find($id);

        if (!$sala) {
            return $this->sendError('Sala no encontrada.', [], 404);
        }

        return $this->sendResponse($sala);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'jurisdiccion_id'    => 'required|exists:jurisdicciones,id',
            'materia_id'         => 'required|exists:materias,id',
            'nombre'             => 'required|string|max:255',
            'edificio_direccion' => 'nullable|string|max:255',
            'activo'             => 'boolean',
        ]);

        $sala = Sala::create($validated);

        return $this->sendResponse($sala->load(['jurisdiccion', 'materia']), 'Sala creada exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $sala = Sala::find($id);

        if (!$sala) {
            return $this->sendError('Sala no encontrada.', [], 404);
        }

        $validated = $request->validate([
            'jurisdiccion_id'    => 'sometimes|required|exists:jurisdicciones,id',
            'materia_id'         => 'sometimes|required|exists:materias,id',
            'nombre'             => 'sometimes|required|string|max:255',
            'edificio_direccion' => 'nullable|string|max:255',
            'activo'             => 'boolean',
        ]);

        $sala->update($validated);

        return $this->sendResponse($sala->load(['jurisdiccion', 'materia']), 'Sala actualizada exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $sala = Sala::withCount('procesos')->find($id);

        if (!$sala) {
            return $this->sendError('Sala no encontrada.', [], 404);
        }

        if ($sala->procesos_count > 0) {
            return $this->sendError(
                "No se puede eliminar la sala '{$sala->nombre}' porque está vinculada a {$sala->procesos_count} proceso(s).",
                ['procesos_count' => $sala->procesos_count],
                422
            );
        }

        $sala->delete();

        return $this->sendResponse(null, 'Sala eliminada exitosamente.');
    }
}