<?php

namespace App\Http\Controllers;

use App\Models\Actuacion;
use App\Models\Documento;
use App\Models\Proceso;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActuacionController extends Controller
{
    public function store(Request $request, $procesoId): JsonResponse
    {
        $proceso = Proceso::findOrFail($procesoId);
        $currentUser = AuditService::getCurrentUser();

        $request->validate([
            'titulo_actuacion' => 'required|string|max:255',
            'tipo_actuacion' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string',
            'es_hito_relevante' => 'nullable|boolean',
            'fecha_hora' => 'nullable|date',
            'abogado_id' => 'nullable|exists:users,id',
            'archivo' => 'nullable|file|max:20480',
        ]);

        $userId = $request->abogado_id ?: ($currentUser ? $currentUser->id : $proceso->abogado_id);

        $actuacion = Actuacion::create([
            'proceso_id' => $proceso->id,
            'user_id' => $userId,
            'fecha_hora' => $request->filled('fecha_hora') ? $request->fecha_hora : now(),
            'titulo_actuacion' => $request->titulo_actuacion,
            'tipo_actuacion' => $request->tipo_actuacion ?? 'Diligencia',
            'descripcion' => $request->descripcion,
            'es_hito_relevante' => $request->boolean('es_hito_relevante', true),
        ]);

        $doc = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $originalName = $file->getClientOriginalName();
            $path = $file->store("expedientes/{$proceso->id}", 'public');

            $doc = Documento::create([
                'proceso_id' => $proceso->id,
                'actuacion_id' => $actuacion->id,
                'nombre_original' => $originalName,
                'ruta_archivo' => $path,
                'mime_type' => $file->getClientMimeType(),
                'peso_bytes' => $file->getSize(),
            ]);
        }

        if (!empty($request->descripcion)) {
            $proceso->update([
                'estado_detalle' => $request->descripcion
            ]);
        }

        // Audit Trail Log
        $descLog = "Registró actuación: {$actuacion->titulo_actuacion}";
        if ($doc) {
            $descLog .= " (Documento adjunto: {$doc->nombre_original})";
        }
        $auditoria = AuditService::log($proceso->id, 'NUEVA_ACTUACION', $descLog, [
            'actuacion_id' => $actuacion->id,
            'tipo' => $actuacion->tipo_actuacion,
            'documento' => $doc ? $doc->nombre_original : null,
        ], $userId);

        $actuacion->load(['user', 'documentos']);
        $auditoria->load('user');

        $auditoriaFormatted = [
            'id' => $auditoria->id,
            'accion' => $auditoria->accion,
            'descripcion' => $auditoria->descripcion,
            'usuario' => $auditoria->user ? $auditoria->user->name : 'Sistema',
            'cargo' => $auditoria->user ? $auditoria->user->cargo : 'Personal',
            'iniciales' => $auditoria->user ? $auditoria->user->iniciales : 'SB',
            'color' => $auditoria->user ? $auditoria->user->color : 'bg-slate-700 text-white',
            'fecha' => $auditoria->created_at->format('d/m/Y H:i'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Actuación registrada exitosamente.',
            'actuacion' => $actuacion,
            'documento' => $doc,
            'auditoria' => $auditoriaFormatted,
        ], 201);
    }
}