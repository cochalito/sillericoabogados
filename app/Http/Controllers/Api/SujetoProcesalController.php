<?php

namespace App\Http\Controllers\Api;

use App\Models\SujetoProcesal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SujetoProcesalController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = SujetoProcesal::withCount(['procesosCliente', 'procesosDemandante', 'procesosDemandado'])
            ->orderBy('nombre_razon_social');

        if ($request->has('es_cliente')) {
            $query->where('es_cliente', $request->boolean('es_cliente'));
        }

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
        $sujeto = SujetoProcesal::with([
            'procesosCliente.abogado',
            'procesosCliente.materia',
            'procesosCliente.estado',
            'procesosDemandante',
            'procesosDemandado'
        ])
        ->withCount(['procesosCliente', 'procesosDemandante', 'procesosDemandado'])
        ->find($id);

        if (!$sujeto) {
            return $this->sendError('Sujeto procesal no encontrado.', [], 404);
        }

        return $this->sendResponse($sujeto);
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
            'es_cliente'          => 'boolean',
        ]);

        $sujeto = SujetoProcesal::create($validated);

        return $this->sendResponse($sujeto, 'Sujeto procesal registrado exitosamente.', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $sujeto = SujetoProcesal::find($id);

        if (!$sujeto) {
            return $this->sendError('Sujeto procesal no encontrado.', [], 404);
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
            'es_cliente'          => 'boolean',
        ]);

        $sujeto->update($validated);

        return $this->sendResponse($sujeto, 'Sujeto procesal actualizado exitosamente.');
    }

    public function destroy(int $id): JsonResponse
    {
        $sujeto = SujetoProcesal::withCount(['procesosCliente', 'procesosDemandante', 'procesosDemandado'])->find($id);

        if (!$sujeto) {
            return $this->sendError('Sujeto procesal no encontrado.', [], 404);
        }

        $totalProcesos = $sujeto->procesos_cliente_count + $sujeto->procesos_demandante_count + $sujeto->procesos_demandado_count;
        if ($totalProcesos > 0) {
            return $this->sendError(
                "No se puede eliminar el sujeto '{$sujeto->nombre_razon_social}' porque figura en {$totalProcesos} proceso(s).",
                ['total_procesos' => $totalProcesos],
                422
            );
        }

        $sujeto->delete();

        return $this->sendResponse(null, 'Sujeto procesal eliminado exitosamente.');
    }
}