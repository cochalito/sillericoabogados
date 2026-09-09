<?php

namespace App\Http\Controllers;

use App\Models\EventoCalendario;
use App\Models\Proceso;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarioController extends Controller
{
    public function index(): View
    {
        $eventos = EventoCalendario::with(['proceso', 'user'])
            ->orderBy('fecha_hora_inicio', 'asc')
            ->get();

        $procesos = Proceso::select('id', 'codigo_interno', 'demandante_denunciante', 'delito_accion')->get();
        $abogados = User::where('es_abogado', true)->get();

        return view('admin.calendario.index', compact('eventos', 'procesos', 'abogados'));
    }

    public function apiEvents(Request $request): JsonResponse
    {
        $query = EventoCalendario::with(['proceso.cliente', 'user']);

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('fecha_hora_inicio', [$request->start, $request->end]);
        }

        if ($request->filled('tipo') && $request->tipo !== 'Todos') {
            $query->where('tipo_evento', $request->tipo);
        }

        $eventos = $query->orderBy('fecha_hora_inicio', 'asc')->get();

        return response()->json([
            'success' => true,
            'eventos' => $eventos,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'proceso_id' => 'nullable|exists:procesos,id',
            'user_id' => 'nullable|exists:users,id',
            'tipo_evento' => 'required|string|max:50',
            'fecha_hora_inicio' => 'required|date',
            'fecha_hora_fin' => 'nullable|date|after_or_equal:fecha_hora_inicio',
            'lugar_enlace' => 'nullable|string|max:255',
            'es_plazo_fatal' => 'nullable|boolean',
            'prioridad' => 'nullable|string|in:Alta,Media,Baja',
            'observaciones' => 'nullable|string',
        ]);

        $validated['es_plazo_fatal'] = $request->boolean('es_plazo_fatal');
        $validated['estado'] = 'Pendiente';

        $evento = EventoCalendario::create($validated);
        $evento->load(['proceso', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Evento o audiencia agendada correctamente.',
            'evento' => $evento,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $evento = EventoCalendario::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'estado' => 'sometimes|in:Pendiente,Realizado,Suspendido,Cancelado',
            'fecha_hora_inicio' => 'sometimes|date',
            'fecha_hora_fin' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        $evento->update($validated);
        $evento->load(['proceso', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Evento actualizado correctamente.',
            'evento' => $evento,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $evento = EventoCalendario::findOrFail($id);
        $evento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evento eliminado correctamente.',
        ]);
    }
}