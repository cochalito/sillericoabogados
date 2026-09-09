<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with('rol')->withCount('procesos')->orderBy('name');

        if ($request->boolean('solo_abogados', false)) {
            $query->where('es_abogado', true);
        }

        if ($request->boolean('solo_activos', true)) {
            $query->where('activo', true);
        }

        if ($request->filled('rol_id')) {
            $query->where('rol_id', $request->rol_id);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('cargo', 'like', "%{$term}%");
            });
        }

        return $this->sendResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $user = User::with(['rol.permisos', 'procesos'])->withCount('procesos')->find($id);

        if (!$user) {
            return $this->sendError('Usuario no encontrado.', [], 404);
        }

        return $this->sendResponse($user);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email',
            'password'    => 'required|string|min:6',
            'rol_id'      => 'nullable|exists:roles,id',
            'cargo'       => 'nullable|string|max:100',
            'iniciales'   => 'nullable|string|max:10',
            'color'       => 'nullable|string|max:50',
            'telefono'    => 'nullable|string|max:50',
            'es_abogado'  => 'boolean',
            'activo'      => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return $this->sendResponse($user->load('rol'), 'Usuario creado exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError('Usuario no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'email'       => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'    => 'nullable|string|min:6',
            'rol_id'      => 'nullable|exists:roles,id',
            'cargo'       => 'nullable|string|max:100',
            'iniciales'   => 'nullable|string|max:10',
            'color'       => 'nullable|string|max:50',
            'telefono'    => 'nullable|string|max:50',
            'es_abogado'  => 'boolean',
            'activo'      => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return $this->sendResponse($user->load('rol'), 'Usuario actualizado exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::withCount('procesos')->find($id);

        if (!$user) {
            return $this->sendError('Usuario no encontrado.', [], 404);
        }

        if ($user->procesos_count > 0) {
            return $this->sendError(
                "No se puede eliminar al usuario '{$user->name}' porque tiene {$user->procesos_count} proceso(s) asignado(s).",
                ['procesos_count' => $user->procesos_count],
                422
            );
        }

        $user->delete();

        return $this->sendResponse(null, 'Usuario eliminado exitosamente.');
    }
}