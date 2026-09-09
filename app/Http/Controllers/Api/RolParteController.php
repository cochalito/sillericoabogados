<?php

namespace App\Http\Controllers\Api;

use App\Models\RolParte;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RolParteController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = RolParte::orderBy('nombre');

        if ($request->boolean('solo_activos', true)) {
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
        $rolParte = RolParte::find($id);

        if (!$rolParte) {
            return $this->sendError('Rol de parte procesal no encontrado.', [], 404);
        }

        return $this->sendResponse($rolParte);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:50|unique:roles_partes,codigo',
            'nombre' => 'required|string|max:100|unique:roles_partes,nombre',
            'activo' => 'boolean',
        ]);

        if (empty($validated['codigo'])) {
            $validated['codigo'] = Str::upper(Str::slug($validated['nombre'], '_'));
        }

        $rolParte = RolParte::create($validated);

        return $this->sendResponse($rolParte, 'Rol de parte creado exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $rolParte = RolParte::find($id);

        if (!$rolParte) {
            return $this->sendError('Rol de parte procesal no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'codigo' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('roles_partes')->ignore($rolParte->id)],
            'nombre' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('roles_partes')->ignore($rolParte->id)],
            'activo' => 'boolean',
        ]);

        $rolParte->update($validated);

        return $this->sendResponse($rolParte, 'Rol de parte actualizado exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $rolParte = RolParte::find($id);

        if (!$rolParte) {
            return $this->sendError('Rol de parte procesal no encontrado.', [], 404);
        }

        $count = \App\Models\Proceso::where('rol_cliente_id', $id)->count();
        if ($count > 0) {
            return $this->sendError(
                "No se puede eliminar el rol '{$rolParte->nombre}' porque está asignado a {$count} proceso(s).",
                ['procesos_count' => $count],
                422
            );
        }

        $rolParte->delete();

        return $this->sendResponse(null, 'Rol de parte eliminado exitosamente.');
    }
}