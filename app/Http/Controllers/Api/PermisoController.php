<?php

namespace App\Http\Controllers\Api;

use App\Models\Permiso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermisoController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Permiso::orderBy('modulo')->orderBy('nombre');

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->boolean('agrupado', false)) {
            $grouped = Permiso::all()->groupBy('modulo');
            return $this->sendResponse($grouped);
        }

        return $this->sendResponse($query->get());
    }

    public function show(int $id): JsonResponse
    {
        $permiso = Permiso::with('roles')->find($id);

        if (!$permiso) {
            return $this->sendError('Permiso no encontrado.', [], 404);
        }

        return $this->sendResponse($permiso);
    }
}