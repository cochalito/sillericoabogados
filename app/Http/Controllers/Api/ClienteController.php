<?php

namespace App\Http\Controllers\Api;

use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Cliente::withCount('procesos')->orderBy('nombre_razon_social');

        if ($request->filled('tipo_persona')) {
            $query->where('tipo_persona', $request->tipo_persona);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('nombre_razon_social', 'like', "%{$term}%")
                  ->orWhere('documento_identidad', 'like', "%{$term}%")
                  ->orWhere('persona_contacto', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('celular_whatsapp', 'like', "%{$term}%");
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
        $cliente = Cliente::with([
            'procesos.abogado',
            'procesos.materia',
            'procesos.estado',
            'procesos.juzgado',
            'procesos.juez'
        ])
        ->withCount('procesos')
        ->find($id);

        if (!$cliente) {
            return $this->sendError('Cliente no encontrado.', [], 404);
        }

        return $this->sendResponse($cliente);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipo_persona'        => 'required|in:NATURAL,JURIDICA',
            'nombre_razon_social' => 'required|string|max:255',
            'documento_identidad' => 'nullable|string|max:50',
            'expedido_en'         => 'nullable|string|max:10',
            'telefono'            => 'nullable|string|max:50',
            'celular_whatsapp'    => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:100',
            'direccion'           => 'nullable|string|max:255',
            'persona_contacto'    => 'nullable|string|max:255',
            'cargo_contacto'      => 'nullable|string|max:100',
            'notas'               => 'nullable|string',
        ]);

        $validated['es_cliente'] = true;

        $cliente = Cliente::create($validated);

        return $this->sendResponse($cliente, 'Cliente registrado exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return $this->sendError('Cliente no encontrado.', [], 404);
        }

        $validated = $request->validate([
            'tipo_persona'        => 'sometimes|required|in:NATURAL,JURIDICA',
            'nombre_razon_social' => 'sometimes|required|string|max:255',
            'documento_identidad' => 'nullable|string|max:50',
            'expedido_en'         => 'nullable|string|max:10',
            'telefono'            => 'nullable|string|max:50',
            'celular_whatsapp'    => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:100',
            'direccion'           => 'nullable|string|max:255',
            'persona_contacto'    => 'nullable|string|max:255',
            'cargo_contacto'      => 'nullable|string|max:100',
            'notas'               => 'nullable|string',
        ]);

        $cliente->update($validated);

        return $this->sendResponse($cliente, 'Cliente actualizado exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $cliente = Cliente::withCount('procesos')->find($id);

        if (!$cliente) {
            return $this->sendError('Cliente no encontrado.', [], 404);
        }

        if ($cliente->procesos_count > 0) {
            return $this->sendError(
                "No se puede eliminar el cliente '{$cliente->nombre_razon_social}' porque tiene {$cliente->procesos_count} proceso(s) asignado(s).",
                ['procesos_count' => $cliente->procesos_count],
                422
            );
        }

        $cliente->delete();

        return $this->sendResponse(null, 'Cliente eliminado exitosamente.');
    }
}