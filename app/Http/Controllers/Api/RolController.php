<?php

namespace App\Http\Controllers\Api;

use App\Models\Rol;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RolController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Rol::with('permisos')
            ->withCount('users')
            ->orderBy('id');

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
        $rol = Rol::with(['permisos', 'users'])->withCount('users')->find($id);

        if (!$rol) {
            return $this->sendError('Rol no encontrado.', [], 404);
        }

        return $this->sendResponse($rol);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo'        => 'nullable|string|max:50|unique:roles,codigo',
            'nombre'        => 'required|string|max:100|unique:roles,nombre',
            'descripcion'   => 'nullable|string',
            'color_badge'   => 'nullable|string|max:50',
            'activo'        => 'boolean',
            'permiso_ids'   => 'nullable|array',
            'permiso_ids.*' => 'exists:permisos,id',
        ]);

        if (empty($validated['codigo'])) {
            $validated['codigo'] = Str::upper(Str::slug($validated['nombre'], '_'));
        }

        $rol = Rol::create($validated);

        if ($request->has('permiso_ids')) {
            $rol->permisos()->sync($request->permiso_ids);
        }

        return $this->sendResponse($rol->load('permisos'), 'Rol creado exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $rol = Rol::find($id);

        if (!$rol) {
            return $this->sendError('Rol no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'codigo'        => ['sometimes', 'required', 'string', 'max:50', Rule::unique('roles')->ignore($rol->id)],
            'nombre'        => ['sometimes', 'required', 'string', 'max:100', Rule::unique('roles')->ignore($rol->id)],
            'descripcion'   => 'nullable|string',
            'color_badge'   => 'nullable|string|max:50',
            'activo'        => 'boolean',
            'permiso_ids'   => 'nullable|array',
            'permiso_ids.*' => 'exists:permisos,id',
        ]);

        $rol->update($validated);

        if ($request->has('permiso_ids')) {
            $rol->permisos()->sync($request->permiso_ids);
        }

        return $this->sendResponse($rol->load('permisos'), 'Rol actualizado exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $rol = Rol::withCount('users')->find($id);

        if (!$rol) {
            return $this->sendError('Rol no encontrado.', [], 404);
        }

        if ($rol->users_count > 0) {
            return $this->sendError(
                "No se puede eliminar el rol '{$rol->nombre}' porque está asignado a {$rol->users_count} usuario(s).",
                ['users_count' => $rol->users_count],
                422
            );
        }

        $rol->permisos()->detach();
        $rol->delete();

        return $this->sendResponse(null, 'Rol eliminado exitosamente.');
    }

    public function syncPermisos(Request $request, int $id): JsonResponse
    {
        $rol = Rol::find($id);

        if (!$rol) {
            return $this->sendError('Rol no encontrado.', [], 404);
        }

        $request->validate([
            'permiso_ids'   => 'required|array',
            'permiso_ids.*' => 'exists:permisos,id',
        ]);

        $rol->permisos()->sync($request->permiso_ids);

        return $this->sendResponse($rol->load('permisos'), 'Permisos del rol sincronizados correctamente.');
    }
}