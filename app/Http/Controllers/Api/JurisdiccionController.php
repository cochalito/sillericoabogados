<?php

namespace App\Http\Controllers\Api;

use App\Models\Jurisdiccion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JurisdiccionController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Jurisdiccion::withCount(['procesos', 'juzgados'])->orderBy('nombre');

        if ($request->boolean('solo_activas', true)) {
            $query->where('activo', true);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('departamento', 'like', "%{$term}%")
                  ->orWhere('codigo', 'like', "%{$term}%");
            });
        }

        return $this->sendResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $jurisdiccion = Jurisdiccion::withCount(['procesos', 'juzgados', 'salas'])->find($id);

        if (!$jurisdiccion) {
            return $this->sendError('Jurisdicción no encontrada.', [], 404);
        }

        return $this->sendResponse($jurisdiccion);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo'       => 'nullable|string|max:50|unique:jurisdicciones,codigo',
            'nombre'       => 'required|string|max:100|unique:jurisdicciones,nombre',
            'departamento' => 'nullable|string|max:100',
            'activo'       => 'boolean',
        ]);

        if (empty($validated['codigo'])) {
            $validated['codigo'] = Str::upper(Str::slug($validated['nombre'], '_'));
        }

        $jurisdiccion = Jurisdiccion::create($validated);

        return $this->sendResponse($jurisdiccion, 'Jurisdicción creada exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $jurisdiccion = Jurisdiccion::find($id);

        if (!$jurisdiccion) {
            return $this->sendError('Jurisdicción no encontrada.', [], 404);
        }

        $validated = $request->validate([
            'codigo'       => ['sometimes', 'required', 'string', 'max:50', Rule::unique('jurisdicciones')->ignore($jurisdiccion->id)],
            'nombre'       => ['sometimes', 'required', 'string', 'max:100', Rule::unique('jurisdicciones')->ignore($jurisdiccion->id)],
            'departamento' => 'nullable|string|max:100',
            'activo'       => 'boolean',
        ]);

        $jurisdiccion->update($validated);

        return $this->sendResponse($jurisdiccion, 'Jurisdicción actualizada exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $jurisdiccion = Jurisdiccion::withCount('procesos')->find($id);

        if (!$jurisdiccion) {
            return $this->sendError('Jurisdicción no encontrada.', [], 404);
        }

        if ($jurisdiccion->procesos_count > 0) {
            return $this->sendError(
                "No se puede eliminar la jurisdicción '{$jurisdiccion->nombre}' porque está vinculada a {$jurisdiccion->procesos_count} proceso(s).",
                ['procesos_count' => $jurisdiccion->procesos_count],
                422
            );
        }

        $jurisdiccion->delete();

        return $this->sendResponse(null, 'Jurisdicción eliminada exitosamente.');
    }
}