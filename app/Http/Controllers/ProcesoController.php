<?php

namespace App\Http\Controllers;

use App\Models\Actuacion;
use App\Models\Cliente;
use App\Models\EventoCalendario;
use App\Models\Proceso;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcesoController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = Proceso::with([
            'cliente',
            'abogado',
            'actuaciones.user',
            'actuaciones.documentos',
            'documentos',
            'eventos',
            'auditorias.user'
        ])->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('materia') && $request->materia !== 'Todas') {
            $query->whereHas('materia', fn($q) => $q->where('nombre', $request->materia));
        }

        if ($request->filled('estado') && $request->estado !== 'Todos') {
            $query->whereHas('estado', fn($q) => $q->where('nombre', $request->estado));
        }

        if ($request->filled('abogado') && $request->abogado !== 'Todos') {
            $query->whereHas('abogado', function ($q) use ($request) {
                $q->where('name', $request->abogado);
            });
        }

        $procesos = $query->get();

        $procesosFormatted = $procesos->map(function ($p) {
            return [
                'id' => $p->id,
                'codigo' => $p->codigo_interno,
                'tipo' => $p->portal_fiscalia ? 'Portal Fis' : ($p->cud ? 'CUD' : 'CASO'),
                'nurej' => $p->nurej ?: ($p->cud ?: 'N/A'),
                'ubicacion' => $p->jurisdiccion ?: 'CENTRO',
                'denunciante' => $p->demandante_denunciante,
                'denunciado' => $p->demandado_denunciado,
                'juzgado' => $p->juzgado_tribunal ?: 'Juzgado Departamental',
                'sala' => $p->sala ?: 'SALA PENAL',
                'fiscal' => $p->autoridad_juez_fiscal ?: 'Fiscal Asignado',
                'delito' => $p->delito_accion,
                'materia' => $p->materia,
                'estado_badge' => $p->estado,
                'estado' => $p->estado_detalle ?: $p->estado,
                'fecha' => $p->fecha_inicio ? $p->fecha_inicio->format('d/m/Y') : date('d/m/Y'),
                'abogado' => $p->abogado ? $p->abogado->name : 'Alan Sillerico Segurondo',
                'abogado_id' => $p->abogado_id,
                'telefono' => $p->cliente ? $p->cliente->celular_whatsapp : '59177234317',
                'correo' => $p->cliente ? $p->cliente->email : 'legal@bufete-sillerico.com',
                'hitos' => $p->actuaciones->map(function ($act) {
                    return [
                        'id' => $act->id,
                        'fecha' => $act->fecha_hora->format('d/m/Y H:i'),
                        'accion' => $act->titulo_actuacion,
                        'abogado' => $act->user ? $act->user->name : 'Alan Sillerico Segurondo',
                        'comentarios' => $act->descripcion,
                        'documentos' => $act->documentos->pluck('nombre_original')->toArray(),
                    ];
                })->values()->toArray(),
                'documentos' => $p->documentos->pluck('nombre_original')->toArray(),
                'auditorias' => $p->auditorias->map(function ($aud) {
                    return [
                        'id' => $aud->id,
                        'accion' => $aud->accion,
                        'descripcion' => $aud->descripcion,
                        'usuario' => $aud->user ? $aud->user->name : 'Sistema',
                        'cargo' => $aud->user ? $aud->user->cargo : 'Personal',
                        'iniciales' => $aud->user ? $aud->user->iniciales : 'SB',
                        'color' => $aud->user ? $aud->user->color : 'bg-slate-700 text-white',
                        'fecha' => $aud->created_at->format('d/m/Y H:i'),
                    ];
                })->values()->toArray(),
            ];
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $procesosFormatted,
                'total' => $procesosFormatted->count(),
            ]);
        }

        // Stats
        $stats = [
            'total' => Proceso::count(),
            'penal' => Proceso::whereHas('materia', fn($q) => $q->where('nombre', 'like', '%Penal%'))->count(),
            'civil' => Proceso::whereHas('materia', fn($q) => $q->where('nombre', 'like', '%Civil%'))->count(),
            'familiar' => Proceso::whereHas('materia', fn($q) => $q->where('nombre', 'like', '%Familiar%'))->count(),
            'laboral' => Proceso::whereHas('materia', fn($q) => $q->where('nombre', 'like', '%Laboral%'))->count(),
            'corporativo' => Proceso::whereHas('materia', fn($q) => $q->where('nombre', 'like', '%Comercial%')->orWhere('nombre', 'like', '%Corporativo%'))->count(),
            'casacion' => Proceso::whereHas('estado', fn($q) => $q->where('nombre', 'like', '%Casación%'))->count(),
            'apelacion' => Proceso::whereHas('estado', fn($q) => $q->where('nombre', 'like', '%Apelación%'))->count(),
            'sentencia' => Proceso::whereHas('estado', fn($q) => $q->where('nombre', 'like', '%Sentencia%'))->count(),
            'rebeldia' => Proceso::whereHas('estado', fn($q) => $q->where('nombre', 'like', '%Rebeldía%'))->count(),
        ];

        $equipo = User::where('es_abogado', true)->get()->map(function ($u) {
            return [
                'id' => $u->id,
                'nombre' => $u->name,
                'cargo' => $u->cargo,
                'iniciales' => $u->iniciales,
                'color' => $u->color,
                'activos' => $u->procesos()->count(),
            ];
        });

        $clientes = Cliente::orderBy('nombre_razon_social')->get();
        $currentUser = AuditService::getCurrentUser();

        return view('admin.procesos.index', compact('procesosFormatted', 'stats', 'equipo', 'clientes', 'currentUser'));
    }

    public function show($id): JsonResponse
    {
        $proceso = Proceso::with([
            'cliente',
            'abogado',
            'actuaciones.user',
            'actuaciones.documentos',
            'documentos',
            'eventos',
            'auditorias.user'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'proceso' => $proceso,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'materia' => 'required|string',
            'delito_accion' => 'required|string|max:255',
            'demandante_denunciante' => 'required|string|max:255',
            'demandado_denunciado' => 'required|string|max:255',
            'cud' => 'nullable|string|max:100',
            'nurej' => 'nullable|string|max:100',
            'codigo_caso' => 'nullable|string|max:100',
            'portal_fiscalia' => 'nullable|boolean',
            'jurisdiccion' => 'nullable|string|max:100',
            'juzgado_tribunal' => 'nullable|string|max:255',
            'sala' => 'nullable|string|max:255',
            'autoridad_juez_fiscal' => 'nullable|string|max:255',
            'investigador_asignado' => 'nullable|string|max:255',
            'etapa_procesal' => 'nullable|string|max:100',
            'estado' => 'required|string|max:50',
            'estado_detalle' => 'nullable|string',
            'abogado_id' => 'nullable|exists:users,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'nuevo_cliente_nombre' => 'nullable|string|max:255',
            'rol_cliente' => 'nullable|string',
        ]);

        $currentUser = AuditService::getCurrentUser();

        if (empty($validated['cliente_id']) && !empty($request->nuevo_cliente_nombre)) {
            $cliente = Cliente::firstOrCreate(
                ['nombre_razon_social' => trim($request->nuevo_cliente_nombre)],
                [
                    'tipo_cliente' => 'NATURAL',
                    'persona_contacto' => trim($request->nuevo_cliente_nombre),
                    'activo' => true,
                ]
            );
            $validated['cliente_id'] = $cliente->id;
        }

        $lastId = Proceso::withTrashed()->max('id') ?? 0;
        $validated['codigo_interno'] = sprintf('EXP-%s-%04d', date('Y'), $lastId + 1);
        $validated['portal_fiscalia'] = $request->boolean('portal_fiscalia');

        $proceso = Proceso::create($validated);

        // Audit log
        AuditService::log(
            $proceso->id,
            'CREACION_PROCESO',
            "Registró el expediente {$proceso->codigo_interno} ({$proceso->delito_accion}) para el cliente {$proceso->demandante_denunciante}",
            [
                'codigo' => $proceso->codigo_interno,
                'delito' => $proceso->delito_accion,
                'materia' => $proceso->materia,
                'juzgado' => $proceso->juzgado_tribunal,
            ],
            $currentUser ? $currentUser->id : null
        );

        if (!empty($validated['estado_detalle'])) {
            Actuacion::create([
                'proceso_id' => $proceso->id,
                'user_id' => $currentUser ? $currentUser->id : $validated['abogado_id'],
                'fecha_hora' => now(),
                'titulo_actuacion' => 'Registro Inicial de Causa',
                'tipo_actuacion' => 'Diligencia',
                'descripcion' => $validated['estado_detalle'],
                'es_hito_relevante' => true,
            ]);
        }

        $proceso->load(['cliente', 'abogado', 'actuaciones.user', 'documentos', 'eventos', 'auditorias.user']);

        $formatted = [
            'id' => $proceso->id,
            'codigo' => $proceso->codigo_interno,
            'tipo' => $proceso->portal_fiscalia ? 'Portal Fis' : ($proceso->cud ? 'CUD' : 'CASO'),
            'nurej' => $proceso->nurej ?: ($proceso->cud ?: 'N/A'),
            'ubicacion' => $proceso->jurisdiccion ?: 'CENTRO',
            'denunciante' => $proceso->demandante_denunciante,
            'denunciado' => $proceso->demandado_denunciado,
            'juzgado' => $proceso->juzgado_tribunal ?: 'Juzgado Departamental',
            'sala' => $proceso->sala ?: 'SALA PENAL',
            'fiscal' => $proceso->autoridad_juez_fiscal ?: 'Fiscal Asignado',
            'delito' => $proceso->delito_accion,
            'materia' => $proceso->materia,
            'estado_badge' => $proceso->estado,
            'estado' => $proceso->estado_detalle ?: $proceso->estado,
            'fecha' => date('d/m/Y'),
            'abogado' => $proceso->abogado ? $proceso->abogado->name : 'Alan Sillerico Segurondo',
            'abogado_id' => $proceso->abogado_id,
            'telefono' => $proceso->cliente ? $proceso->cliente->celular_whatsapp : '59177234317',
            'correo' => $proceso->cliente ? $proceso->cliente->email : 'legal@bufete-sillerico.com',
            'hitos' => [],
            'documentos' => [],
            'auditorias' => [
                [
                    'id' => 1,
                    'accion' => 'CREACION_PROCESO',
                    'descripcion' => "Registró el expediente {$proceso->codigo_interno}",
                    'usuario' => $currentUser ? $currentUser->name : 'Alan Sillerico Segurondo',
                    'cargo' => $currentUser ? $currentUser->cargo : 'Director General',
                    'iniciales' => $currentUser ? $currentUser->iniciales : 'AS',
                    'color' => $currentUser ? $currentUser->color : 'bg-brand-green text-brand-gold',
                    'fecha' => date('d/m/Y H:i'),
                ]
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Proceso registrado exitosamente.',
            'proceso' => $formatted,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $proceso = Proceso::findOrFail($id);
        $currentUser = AuditService::getCurrentUser();

        $validated = $request->validate([
            'materia' => 'sometimes|string',
            'delito_accion' => 'sometimes|string|max:255',
            'demandante_denunciante' => 'sometimes|string|max:255',
            'demandado_denunciado' => 'sometimes|string|max:255',
            'cud' => 'nullable|string|max:100',
            'nurej' => 'nullable|string|max:100',
            'codigo_caso' => 'nullable|string|max:100',
            'portal_fiscalia' => 'nullable|boolean',
            'jurisdiccion' => 'nullable|string|max:100',
            'juzgado_tribunal' => 'nullable|string|max:255',
            'sala' => 'nullable|string|max:255',
            'autoridad_juez_fiscal' => 'nullable|string|max:255',
            'investigador_asignado' => 'nullable|string|max:255',
            'etapa_procesal' => 'nullable|string|max:100',
            'estado' => 'sometimes|string|max:50',
            'estado_detalle' => 'nullable|string',
            'abogado_id' => 'nullable|exists:users,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'rol_cliente' => 'nullable|string',
        ]);

        $cambios = [];
        if (isset($validated['estado']) && $validated['estado'] !== $proceso->estado) {
            $cambios[] = "Estado de '{$proceso->estado}' a '{$validated['estado']}'";
        }
        if (isset($validated['juzgado_tribunal']) && $validated['juzgado_tribunal'] !== $proceso->juzgado_tribunal) {
            $cambios[] = "Juzgado a '{$validated['juzgado_tribunal']}'";
        }

        $proceso->update($validated);

        $descCambios = count($cambios) > 0 ? implode(', ', $cambios) : "Actualizó datos del expediente";
        AuditService::log(
            $proceso->id,
            'MODIFICACION_DATOS',
            $descCambios,
            $validated,
            $currentUser ? $currentUser->id : null
        );

        return response()->json([
            'success' => true,
            'message' => 'Proceso actualizado exitosamente.',
            'proceso' => $proceso,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $proceso = Proceso::findOrFail($id);
        $currentUser = AuditService::getCurrentUser();

        AuditService::log(
            $proceso->id,
            'ELIMINACION',
            "Archivó / eliminó la causa {$proceso->codigo_interno}",
            null,
            $currentUser ? $currentUser->id : null
        );

        $proceso->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proceso archivado correctamente.',
        ]);
    }
}