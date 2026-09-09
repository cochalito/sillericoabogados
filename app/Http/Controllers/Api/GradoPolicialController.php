<?php

namespace App\Http\Controllers\Api;

use App\Models\GradoPolicial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GradoPolicialController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = GradoPolicial::withCount('investigadores')
            ->orderBy('jerarquia_orden');

        if ($request->boolean('solo_activos', true)) {
            $query->where('activo', true);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('abreviatura', 'like', "%{$term}%")
                  ->orWhere('codigo', 'like', "%{$term}%");
            });
        }

        return $this->sendResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $grado = GradoPolicial::withCount('investigadores')->find($id);

        if (!$grado) {
            return $this->sendError('Grado policial no encontrado.', [], 404);
        }

        return $this->sendResponse($grado);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo'          => 'nullable|string|max:50|unique:grados_policiales,codigo',
            'nombre'          => 'required|string|max:100|unique:grados_policiales,nombre',
            'abreviatura'     => 'required|string|max:50',
            'jerarquia_orden' => 'nullable|integer',
            'activo'          => 'boolean',
        ]);

        if (empty($validated['codigo'])) {
            $validated['codigo'] = Str::upper(Str::slug($validated['nombre'], '_'));
        }

        $grado = GradoPolicial::create($validated);

        return $this->sendResponse($grado, 'Grado policial registrado exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $grado = GradoPolicial::find($id);

        if (!$grado) {
            return $this->sendError('Grado policial no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'codigo'          => ['sometimes', 'required', 'string', 'max:50', Rule::unique('grados_policiales')->ignore($grado->id)],
            'nombre'          => ['sometimes', 'required', 'string', 'max:100', Rule::unique('grados_policiales')->ignore($grado->id)],
            'abreviatura'     => 'sometimes|required|string|max:50',
            'jerarquia_orden' => 'nullable|integer',
            'activo'          => 'boolean',
        ]);

        $grado->update($validated);

        return $this->sendResponse($grado, 'Grado policial actualizado exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $grado = GradoPolicial::withCount('investigadores')->find($id);

        if (!$grado) {
            return $this->sendError('Grado policial no encontrado.', [], 404);
        }

        if ($grado->investigadores_count > 0) {
            return $this->sendError(
                "No se puede eliminar el grado '{$grado->nombre}' porque está asignado a {$grado->investigadores_count} investigador(es).",
                ['investigadores_count' => $grado->investigadores_count],
                422
            );
        }

        $grado->delete();

        return $this->sendResponse(null, 'Grado policial eliminado exitosamente.');
    }
}