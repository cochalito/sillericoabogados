<?php

namespace App\Http\Controllers\Api;

use App\Models\ArticuloLey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticuloLeyController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = ArticuloLey::with('materia')
            ->withCount('procesos')
            ->orderBy('codigo_normativo')
            ->orderBy('numero_articulo');

        if ($request->boolean('solo_activos', true)) {
            $query->where('activo', true);
        }

        if ($request->filled('codigo_normativo')) {
            $query->where('codigo_normativo', $request->codigo_normativo);
        }

        if ($request->filled('materia_id')) {
            $query->where('materia_id', $request->materia_id);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('numero_articulo', 'like', "%{$term}%")
                  ->orWhere('epigrafe_delito', 'like', "%{$term}%")
                  ->orWhere('codigo_normativo', 'like', "%{$term}%")
                  ->orWhere('texto_tipificacion', 'like', "%{$term}%");
            });
        }

        if ($request->boolean('paginate', false)) {
            $perPage = (int) $request->input('per_page', 25);
            return $this->sendResponse($query->paginate($perPage));
        }

        return $this->sendResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $articulo = ArticuloLey::with('materia')->withCount('procesos')->find($id);

        if (!$articulo) {
            return $this->sendError('Artículo de ley no encontrado.', [], 404);
        }

        return $this->sendResponse($articulo);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo_normativo'   => 'required|string|max:50',
            'numero_articulo'    => 'required|string|max:50',
            'epigrafe_delito'    => 'required|string|max:255',
            'materia_id'         => 'required|exists:materias,id',
            'pena_minima_anos'   => 'nullable|numeric|min:0',
            'pena_maxima_anos'   => 'nullable|numeric|min:0',
            'texto_tipificacion' => 'nullable|string',
            'activo'             => 'boolean',
        ]);

        $articulo = ArticuloLey::create($validated);

        return $this->sendResponse($articulo->load('materia'), 'Artículo de ley registrado exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $articulo = ArticuloLey::find($id);

        if (!$articulo) {
            return $this->sendError('Artículo de ley no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'codigo_normativo'   => 'sometimes|required|string|max:50',
            'numero_articulo'    => 'sometimes|required|string|max:50',
            'epigrafe_delito'    => 'sometimes|required|string|max:255',
            'materia_id'         => 'sometimes|required|exists:materias,id',
            'pena_minima_anos'   => 'nullable|numeric|min:0',
            'pena_maxima_anos'   => 'nullable|numeric|min:0',
            'texto_tipificacion' => 'nullable|string',
            'activo'             => 'boolean',
        ]);

        $articulo->update($validated);

        return $this->sendResponse($articulo->load('materia'), 'Artículo de ley actualizado exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $articulo = ArticuloLey::withCount('procesos')->find($id);

        if (!$articulo) {
            return $this->sendError('Artículo de ley no encontrado.', [], 404);
        }

        if ($articulo->procesos_count > 0) {
            return $this->sendError(
                "No se puede eliminar el artículo '{$articulo->numero_articulo} {$articulo->epigrafe_delito}' porque está asociado a {$articulo->procesos_count} proceso(s).",
                ['procesos_count' => $articulo->procesos_count],
                422
            );
        }

        $articulo->delete();

        return $this->sendResponse(null, 'Artículo de ley eliminado exitosamente.');
    }
}