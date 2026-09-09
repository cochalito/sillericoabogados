<?php

namespace App\Http\Controllers\Api;

use App\Models\Investigador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestigadorController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Investigador::with('grado')
            ->withCount('procesos')
            ->orderBy('apellidos')
            ->orderBy('nombres');

        if ($request->boolean('solo_activos', true)) {
            $query->where('activo', true);
        }

        if ($request->filled('grado_id')) {
            $query->where('grado_id', $request->grado_id);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('nombres', 'like', "%{$term}%")
                  ->orWhere('apellidos', 'like', "%{$term}%")
                  ->orWhere('division', 'like', "%{$term}%")
                  ->orWhere('celular_contacto', 'like', "%{$term}%");
            });
        }

        return $this->sendResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $investigador = Investigador::with(['grado', 'procesos'])->withCount('procesos')->find($id);

        if (!$investigador) {
            return $this->sendError('Investigador policial no encontrado.', [], 404);
        }

        return $this->sendResponse($investigador);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'grado_id'         => 'required|exists:grados_policiales,id',
            'nombres'          => 'required|string|max:100',
            'apellidos'        => 'required|string|max:100',
            'division'         => 'nullable|string|max:150',
            'celular_contacto' => 'nullable|string|max:50',
            'activo'           => 'boolean',
        ]);

        $investigador = Investigador::create($validated);

        return $this->sendResponse($investigador->load('grado'), 'Investigador policial registrado exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $investigador = Investigador::find($id);

        if (!$investigador) {
            return $this->sendError('Investigador policial no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'grado_id'         => 'sometimes|required|exists:grados_policiales,id',
            'nombres'          => 'sometimes|required|string|max:100',
            'apellidos'        => 'sometimes|required|string|max:100',
            'division'         => 'nullable|string|max:150',
            'celular_contacto' => 'nullable|string|max:50',
            'activo'           => 'boolean',
        ]);

        $investigador->update($validated);

        return $this->sendResponse($investigador->load('grado'), 'Investigador policial actualizado exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $investigador = Investigador::withCount('procesos')->find($id);

        if (!$investigador) {
            return $this->sendError('Investigador policial no encontrado.', [], 404);
        }

        if ($investigador->procesos_count > 0) {
            return $this->sendError(
                "No se puede eliminar al investigador '{$investigador->nombre_completo}' porque está vinculado a {$investigador->procesos_count} proceso(s).",
                ['procesos_count' => $investigador->procesos_count],
                422
            );
        }

        $investigador->delete();

        return $this->sendResponse(null, 'Investigador policial eliminado exitosamente.');
    }
}