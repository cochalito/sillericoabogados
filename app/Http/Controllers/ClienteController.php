<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = Cliente::withCount('procesos')->orderBy('nombre_razon_social');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('nombre_razon_social', 'like', "%{$term}%")
                  ->orWhere('documento_identidad', 'like', "%{$term}%")
                  ->orWhere('persona_contacto', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        }

        $clientes = $query->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $clientes,
            ]);
        }

        return view('admin.clientes.index', compact('clientes'));
    }

    public function show($id): JsonResponse
    {
        $cliente = Cliente::with('procesos.abogado')->findOrFail($id);

        return response()->json([
            'success' => true,
            'cliente' => $cliente,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipo_cliente' => 'required|in:NATURAL,JURIDICO',
            'nombre_razon_social' => 'required|string|max:255',
            'documento_identidad' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:50',
            'celular_whatsapp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'persona_contacto' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
        ]);

        $cliente = Cliente::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cliente registrado correctamente.',
            'cliente' => $cliente,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'tipo_cliente' => 'sometimes|in:NATURAL,JURIDICO',
            'nombre_razon_social' => 'sometimes|string|max:255',
            'documento_identidad' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:50',
            'celular_whatsapp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'persona_contacto' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
        ]);

        $cliente->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado correctamente.',
            'cliente' => $cliente,
        ]);
    }
}