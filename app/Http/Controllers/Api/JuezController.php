<?php

namespace App\Http\Controllers\Api;

use App\Models\Juez;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JuezController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Juez::with('juzgado')
            ->withCount('procesos')
            ->orderBy('nombre_completo');

        if ($request->boolean('solo_activos', true)) {
            $query->where('activo', true);
        }

        if ($request->filled('tipo_autoridad')) {
            $query->where('tipo_autoridad', $request->tipo_autoridad);
        }

        if ($request->filled('juzgado_id')) {
            $query->where('juzgado_id', $request->juzgado_id);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('nombre_completo', 'like', "%{$term}%")
                  ->orWhere('telefono', 'like', "%{$term}%");
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
        $juez = Juez::with(['juzgado', 'procesos'])->withCount('procesos')->find($id);

        if (!$juez) {
            return $this->sendError('Juez no encontrado.', [], 404);
        }

        return $this->sendResponse($juez);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipo_autoridad'  => 'required|in:JUEZ_INSTRUCCION,JUEZ_SENTENCIA,VOCAL_SALA,FISCAL_MATERIA',
            'nombre_completo' => 'required|string|max:255',
            'juzgado_id'      => 'nullable|exists:juzgados,id',
            'telefono'        => 'nullable|string|max:50',
            'activo'          => 'boolean',
        ]);

        $juez = Juez::create($validated);

        return $this->sendResponse($juez->load('juzgado'), 'Juez registrado exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $juez = Juez::find($id);

        if (!$juez) {
            return $this->sendError('Juez no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'tipo_autoridad'  => 'sometimes|required|in:JUEZ_INSTRUCCION,JUEZ_SENTENCIA,VOCAL_SALA,FISCAL_MATERIA',
            'nombre_completo' => 'sometimes|required|string|max:255',
            'juzgado_id'      => 'nullable|exists:juzgados,id',
            'telefono'        => 'nullable|string|max:50',
            'activo'          => 'boolean',
        ]);

        $juez->update($validated);

        return $this->sendResponse($juez->load('juzgado'), 'Juez actualizado exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $juez = Juez::withCount('procesos')->find($id);

        if (!$juez) {
            return $this->sendError('Juez no encontrado.', [], 404);
        }

        if ($juez->procesos_count > 0) {
            return $this->sendError(
                "No se puede eliminar al juez '{$juez->nombre_completo}' porque está vinculado a {$juez->procesos_count} proceso(s).",
                ['procesos_count' => $juez->procesos_count],
                422
            );
        }

        $juez->delete();

        return $this->sendResponse(null, 'Juez eliminado exitosamente.');
    }
}