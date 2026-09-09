<?php

namespace App\Http\Controllers\Api;

use App\Models\Juzgado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JuzgadoController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Juzgado::with(['jurisdiccion', 'materia'])
            ->withCount('procesos')
            ->orderBy('nombre');

        if ($request->boolean('solo_activos', true)) {
            $query->where('activo', true);
        }

        if ($request->filled('jurisdiccion_id')) {
            $query->where('jurisdiccion_id', $request->jurisdiccion_id);
        }

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->materia_id);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('edificio_direccion', 'like', "%{$term}%");
            });
        }

        if ($request->boolean('paginate', false)) {
            $perPage = (int) $request->input('per_page', 20);
            return $this->sendResponse($query->paginate($perPage));
        }

        return $this->sendResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $juzgado = Juzgado::with(['jurisdiccion', 'materia', 'jueces'])
            ->withCount('procesos')
            ->find($id);

        if (!$juzgado) {
            return $this->sendError('Juzgado no encontrado.', [], 404);
        }

        return $this->sendResponse($juzgado);
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

        $juzgado = Juzgado::create($validated);

        return $this->sendResponse($juzgado->load(['jurisdiccion', 'materia']), 'Juzgado registrado exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $juzgado = Juzgado::find($id);

        if (!$juzgado) {
            return $this->sendError('Juzgado no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'jurisdiccion_id'    => 'sometimes|required|exists:jurisdicciones,id',
            'materia_id'         => 'sometimes|required|exists:materias,id',
            'nombre'             => 'sometimes|required|string|max:255',
            'edificio_direccion' => 'nullable|string|max:255',
            'activo'             => 'boolean',
        ]);

        $juzgado->update($validated);

        return $this->sendResponse($juzgado->load(['jurisdiccion', 'materia']), 'Juzgado actualizado exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $juzgado = Juzgado::withCount('procesos')->find($id);

        if (!$juzgado) {
            return $this->sendError('Juzgado no encontrado.', [], 404);
        }

        if ($juzgado->procesos_count > 0) {
            return $this->sendError(
                "No se puede eliminar el juzgado '{$juzgado->nombre}' porque está vinculado a {$juzgado->procesos_count} proceso(s).",
                ['procesos_count' => $juzgado->procesos_count],
                422
            );
        }

        $juzgado->delete();

        return $this->sendResponse(null, 'Juzgado eliminado exitosamente.');
    }
}