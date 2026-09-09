<?php

namespace App\Http\Controllers\Api;

use App\Models\Materia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MateriaController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Materia::withCount('procesos')->orderBy('nombre');

        if ($request->boolean('solo_activas', true)) {
            $query->where('activo', true);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('codigo', 'like', "%{$term}%");
            });
        }

        return $this->sendResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $materia = Materia::withCount(['procesos', 'etapas', 'juzgados', 'articulos'])->find($id);

        if (!$materia) {
            return $this->sendError('Materia no encontrada.', [], 404);
        }

        return $this->sendResponse($materia);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo'      => 'nullable|string|max:50|unique:materias,codigo',
            'nombre'      => 'required|string|max:100|unique:materias,nombre',
            'color_hex'   => 'nullable|string|max:20',
            'color_badge' => 'nullable|string|max:50',
            'activo'      => 'boolean',
        ]);

        if (empty($validated['codigo'])) {
            $validated['codigo'] = Str::upper(Str::slug($validated['nombre'], '_'));
        }

        $materia = Materia::create($validated);

        return $this->sendResponse($materia, 'Materia creada exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $materia = Materia::find($id);

        if (!$materia) {
            return $this->sendError('Materia no encontrada.', [], 404);
        }

        $validated = $request->validate([
            'codigo'      => ['sometimes', 'required', 'string', 'max:50', Rule::unique('materias')->ignore($materia->id)],
            'nombre'      => ['sometimes', 'required', 'string', 'max:100', Rule::unique('materias')->ignore($materia->id)],
            'color_hex'   => 'nullable|string|max:20',
            'color_badge' => 'nullable|string|max:50',
            'activo'      => 'boolean',
        ]);

        $materia->update($validated);

        return $this->sendResponse($materia, 'Materia actualizada exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $materia = Materia::withCount('procesos')->find($id);

        if (!$materia) {
            return $this->sendError('Materia no encontrada.', [], 404);
        }

        if ($materia->procesos_count > 0) {
            return $this->sendError(
                "No se puede eliminar la materia '{$materia->nombre}' porque está vinculada a {$materia->procesos_count} proceso(s).",
                ['procesos_count' => $materia->procesos_count],
                422
            );
        }

        $materia->delete();

        return $this->sendResponse(null, 'Materia eliminada exitosamente.');
    }
}