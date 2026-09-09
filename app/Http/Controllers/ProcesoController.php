<?php

namespace App\Http\Controllers;

use App\Models\Actuacion;
use App\Models\ArticuloLey;
use App\Models\Cliente;
use App\Models\EstadoProceso;
use App\Models\EtapaProcesal;
use App\Models\EventoCalendario;
use App\Models\Investigador;
use App\Models\Juez;
use App\Models\Juzgado;
use App\Models\Jurisdiccion;
use App\Models\Materia;
use App\Models\Proceso;
use App\Models\RolParte;
use App\Models\Sala;
use App\Models\SujetoProcesal;
use App\Models\User;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcesoController extends Controller
{
    protected array $relations = [
        'materia',
        'jurisdiccion',
        'demandante',
        'demandado',
        'cliente',
        'rolCliente',
        'articuloPrincipal',
        'juez',
        'juzgado',
        'sala',
        'investigador.grado',
        'etapaProcesal',
        'estado',
        'abogado',
        'actuaciones.user',
        'actuaciones.documentos',
        'documentos',
        'eventos',
        'auditorias.user'
    ];

    public function index(Request $request): View|JsonResponse
    {
        $query = Proceso::with($this->relations)->orderBy('id', 'desc');

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
        $procesosFormatted = $procesos->map(fn($p) => $this->formatProceso($p));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $procesosFormatted,
                'total' => $procesosFormatted->count(),
            ]);
        }

        // Estadísticas agregadas
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

        // Tablas Paramétricas
        $materias = Materia::where('activo', true)->orderBy('nombre')->get();
        $estados = EstadoProceso::where('activo', true)->orderBy('orden')->get();
        $etapas = EtapaProcesal::where('activo', true)->orderBy('orden')->get();
        $jurisdicciones = Jurisdiccion::orderBy('nombre')->get();
        $juzgados = Juzgado::where('activo', true)->orderBy('nombre')->get();
        $salas = Sala::where('activo', true)->orderBy('nombre')->get();
        $jueces = Juez::where('activo', true)->orderBy('nombre_completo')->get();
        $investigadores = Investigador::with('grado')->where('activo', true)->orderBy('nombres')->get();
        $articulos = ArticuloLey::where('activo', true)->orderBy('epigrafe_delito')->get();
        $rolesPartes = RolParte::orderBy('nombre')->get();
        $sujetos = SujetoProcesal::where('activo', true)->orderBy('nombre_razon_social')->get();
        $clientes = SujetoProcesal::where('es_cliente', true)->where('activo', true)->orderBy('nombre_razon_social')->get();

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

        $currentUser = AuditService::getCurrentUser();

        return view('admin.procesos.index', compact(
            'procesosFormatted',
            'stats',
            'materias',
            'estados',
            'etapas',
            'jurisdicciones',
            'juzgados',
            'salas',
            'jueces',
            'investigadores',
            'articulos',
            'rolesPartes',
            'sujetos',
            'clientes',
            'equipo',
            'currentUser'
        ));
    }

    public function show($id): JsonResponse
    {
        $proceso = Proceso::with($this->relations)->findOrFail($id);

        return response()->json([
            'success' => true,
            'proceso' => $this->formatProceso($proceso),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $currentUser = AuditService::getCurrentUser();

        // 1. Materia
        $materiaId = $request->materia_id;
        if (!$materiaId && $request->filled('materia')) {
            $m = Materia::where('nombre', $request->materia)->orWhere('codigo', $request->materia)->first();
            $materiaId = $m?->id;
        }
        if (!$materiaId) $materiaId = Materia::first()?->id;

        // 2. Jurisdicción
        $jurisdiccionId = $request->jurisdiccion_id;
        if (!$jurisdiccionId && $request->filled('jurisdiccion')) {
            $j = Jurisdiccion::where('nombre', 'like', "%{$request->jurisdiccion}%")->orWhere('codigo', $request->jurisdiccion)->first();
            $jurisdiccionId = $j?->id;
        }
        if (!$jurisdiccionId) $jurisdiccionId = Jurisdiccion::first()?->id;

        // 3. Demandante
        $demandanteId = $request->demandante_id;
        if (!$demandanteId && $request->filled('demandante_denunciante')) {
            $dem = SujetoProcesal::firstOrCreate(
                ['nombre_razon_social' => trim($request->demandante_denunciante)],
                ['tipo_persona' => 'NATURAL', 'es_cliente' => true, 'celular_whatsapp' => $request->telefono ?? null]
            );
            $demandanteId = $dem->id;
        }
        if (!$demandanteId) {
            $dem = SujetoProcesal::firstOrCreate(
                ['nombre_razon_social' => 'Demandante Por Asignar'],
                ['tipo_persona' => 'NATURAL', 'es_cliente' => true]
            );
            $demandanteId = $dem->id;
        }

        // 4. Demandado
        $demandadoId = $request->demandado_id;
        if (!$demandadoId && $request->filled('demandado_denunciado')) {
            $dem = SujetoProcesal::firstOrCreate(
                ['nombre_razon_social' => trim($request->demandado_denunciado)],
                ['tipo_persona' => 'NATURAL', 'es_cliente' => false]
            );
            $demandadoId = $dem->id;
        }
        if (!$demandadoId) {
            $dem = SujetoProcesal::firstOrCreate(
                ['nombre_razon_social' => 'Demandado Por Asignar'],
                ['tipo_persona' => 'NATURAL', 'es_cliente' => false]
            );
            $demandadoId = $dem->id;
        }

        // 5. Cliente
        $clienteId = $request->cliente_id ?? $demandanteId;
        if (!$clienteId && $request->filled('nuevo_cliente_nombre')) {
            $cli = SujetoProcesal::firstOrCreate(
                ['nombre_razon_social' => trim($request->nuevo_cliente_nombre)],
                ['tipo_persona' => 'NATURAL', 'es_cliente' => true, 'celular_whatsapp' => $request->telefono ?? null]
            );
            $clienteId = $cli->id;
        }

        // 6. Juzgado
        $juzgadoId = $request->juzgado_id;
        if (!$juzgadoId && $request->filled('juzgado_tribunal')) {
            $juz = Juzgado::firstOrCreate(
                ['nombre' => trim($request->juzgado_tribunal)],
                ['materia_id' => $materiaId, 'activo' => true]
            );
            $juzgadoId = $juz->id;
        }

        // 7. Delito / Tipificación
        $articuloId = $request->articulo_principal_id;
        if (!$articuloId && $request->filled('delito_accion')) {
            $art = ArticuloLey::firstOrCreate(
                ['epigrafe_delito' => trim($request->delito_accion), 'materia_id' => $materiaId],
                ['numero_articulo' => 'Art. Especial', 'activo' => true]
            );
            $articuloId = $art->id;
        }

        // 8. Estado
        $estadoId = $request->estado_id;
        if (!$estadoId && $request->filled('estado')) {
            $est = EstadoProceso::where('nombre', $request->estado)->orWhere('codigo', $request->estado)->first();
            $estadoId = $est?->id;
        }
        if (!$estadoId && $request->filled('estado_badge')) {
            $est = EstadoProceso::where('nombre', $request->estado_badge)->first();
            $estadoId = $est?->id;
        }
        if (!$estadoId) $estadoId = EstadoProceso::first()?->id;

        // 9. Etapa
        $etapaId = $request->etapa_procesal_id;
        if (!$etapaId) {
            $etapa = EtapaProcesal::where('materia_id', $materiaId)->orderBy('orden')->first();
            $etapaId = $etapa?->id ?? EtapaProcesal::first()?->id;
        }

        $lastId = Proceso::withTrashed()->max('id') ?? 0;
        $codigoInterno = $request->codigo ?: ($request->codigo_interno ?: sprintf('EXP-%s-%04d', date('Y'), $lastId + 1));

        $proceso = Proceso::create([
            'codigo_interno' => $codigoInterno,
            'cud' => $request->cud,
            'nurej' => ($request->nurej && $request->nurej !== 'N/A') ? $request->nurej : null,
            'codigo_caso' => $request->codigo_caso,
            'portal_fiscalia' => $request->boolean('portal_fiscalia'),
            'materia_id' => $materiaId,
            'jurisdiccion_id' => $jurisdiccionId,
            'demandante_id' => $demandanteId,
            'demandado_id' => $demandadoId,
            'cliente_id' => $clienteId,
            'rol_cliente_id' => $request->rol_cliente_id ?? 1,
            'articulo_principal_id' => $articuloId,
            'juzgado_id' => $juzgadoId,
            'sala_id' => $request->sala_id,
            'juez_id' => $request->juez_id,
            'investigador_id' => $request->investigador_id,
            'etapa_procesal_id' => $etapaId,
            'estado_id' => $estadoId,
            'situacion_actual' => $request->estado_detalle ?: ($request->situacion_actual ?: $request->estado),
            'abogado_id' => $request->abogado_id,
            'fecha_inicio' => $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : now(),
        ]);

        if ($articuloId) {
            $proceso->articulos()->syncWithoutDetaching([$articuloId => ['es_principal' => true]]);
        }

        // Auditoría
        AuditService::log(
            $proceso->id,
            'CREACION_PROCESO',
            "Registró el expediente {$proceso->codigo_interno} para " . ($proceso->demandante?->nombre_razon_social ?? 'Sujeto'),
            [
                'codigo' => $proceso->codigo_interno,
                'delito' => $proceso->articuloPrincipal?->epigrafe_delito,
                'materia' => $proceso->materia?->nombre,
                'juzgado' => $proceso->juzgado?->nombre,
            ],
            $currentUser ? $currentUser->id : null
        );

        if (!empty($proceso->situacion_actual)) {
            Actuacion::create([
                'proceso_id' => $proceso->id,
                'user_id' => $currentUser ? $currentUser->id : $proceso->abogado_id,
                'fecha_hora' => now(),
                'titulo_actuacion' => 'Registro Inicial de Causa',
                'tipo_actuacion' => 'Diligencia',
                'descripcion' => $proceso->situacion_actual,
                'es_hito_relevante' => true,
            ]);
        }

        $proceso->load($this->relations);

        return response()->json([
            'success' => true,
            'message' => 'Proceso registrado exitosamente.',
            'proceso' => $this->formatProceso($proceso),
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $proceso = Proceso::findOrFail($id);
        $currentUser = AuditService::getCurrentUser();

        $updateData = [];

        if ($request->filled('estado_id')) {
            $updateData['estado_id'] = $request->estado_id;
        } elseif ($request->filled('estado')) {
            $est = EstadoProceso::where('nombre', $request->estado)->first();
            if ($est) $updateData['estado_id'] = $est->id;
        }

        if ($request->filled('situacion_actual') || $request->filled('estado_detalle')) {
            $updateData['situacion_actual'] = $request->situacion_actual ?: $request->estado_detalle;
        }

        if ($request->filled('abogado_id')) $updateData['abogado_id'] = $request->abogado_id;
        if ($request->filled('juzgado_id')) $updateData['juzgado_id'] = $request->juzgado_id;
        if ($request->filled('etapa_procesal_id')) $updateData['etapa_procesal_id'] = $request->etapa_procesal_id;
        if ($request->filled('cud')) $updateData['cud'] = $request->cud;
        if ($request->filled('nurej')) $updateData['nurej'] = $request->nurej;

        $proceso->update($updateData);

        AuditService::log(
            $proceso->id,
            'MODIFICACION_DATOS',
            "Actualizó datos del expediente {$proceso->codigo_interno}",
            $updateData,
            $currentUser ? $currentUser->id : null
        );

        $proceso->load($this->relations);

        return response()->json([
            'success' => true,
            'message' => 'Proceso actualizado exitosamente.',
            'proceso' => $this->formatProceso($proceso),
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

    protected function formatProceso(Proceso $p): array
    {
        return [
            'id' => $p->id,
            'codigo' => $p->codigo_interno,
            'tipo' => $p->portal_fiscalia ? 'Portal Fis' : ($p->cud ? 'CUD' : ($p->nurej ? 'NUREJ' : 'CASO')),
            'nurej' => $p->nurej ?: ($p->cud ?: ($p->ianus ?: 'N/A')),
            'cud' => $p->cud,
            'codigo_caso' => $p->codigo_caso,
            'ubicacion' => $p->jurisdiccion ? $p->jurisdiccion->nombre : 'La Paz - Centro',
            'jurisdiccion_id' => $p->jurisdiccion_id,

            'denunciante' => $p->demandante ? $p->demandante->nombre_razon_social : 'Sin asignar',
            'denunciante_id' => $p->demandante_id,
            'denunciado' => $p->demandado ? $p->demandado->nombre_razon_social : 'Sin asignar',
            'demandado_id' => $p->demandado_id,
            'cliente' => $p->cliente ? $p->cliente->nombre_razon_social : ($p->demandante ? $p->demandante->nombre_razon_social : 'Sin cliente'),
            'cliente_id' => $p->cliente_id,
            'rol_cliente' => $p->rolCliente ? $p->rolCliente->nombre : 'Cliente',
            'rol_cliente_id' => $p->rol_cliente_id,

            'juzgado' => $p->juzgado ? $p->juzgado->nombre : ($p->sala ? $p->sala->nombre : 'Sin juzgado asignado'),
            'juzgado_id' => $p->juzgado_id,
            'sala' => $p->sala ? $p->sala->nombre : '',
            'sala_id' => $p->sala_id,
            'juez' => $p->juez ? $p->juez->nombre_completo : '',
            'juez_id' => $p->juez_id,
            'investigador' => $p->investigador ? trim(($p->investigador->grado ? $p->investigador->grado->abreviatura . ' ' : '') . $p->investigador->nombres . ' ' . $p->investigador->apellidos) : '',
            'investigador_id' => $p->investigador_id,
            'fiscal' => $p->juez ? $p->juez->nombre_completo : ($p->investigador ? $p->investigador->nombres : 'Asignado al caso'),

            'delito' => $p->articuloPrincipal ? $p->articuloPrincipal->epigrafe_delito : 'Acción Jurídica',
            'articulo_id' => $p->articulo_principal_id,
            'articulo' => $p->articuloPrincipal ? ($p->articuloPrincipal->numero_articulo . ' - ' . $p->articuloPrincipal->epigrafe_delito) : '',
            'materia' => $p->materia ? $p->materia->nombre : 'Penal',
            'materia_id' => $p->materia_id,
            'materia_badge' => $p->materia ? $p->materia->color_badge : 'bg-slate-100 text-slate-700',

            'etapa' => $p->etapaProcesal ? $p->etapaProcesal->nombre : 'Inicial',
            'etapa_id' => $p->etapa_procesal_id,
            'estado_badge' => $p->estado ? $p->estado->nombre : 'En Trámite',
            'estado_id' => $p->estado_id,
            'estado_color' => $p->estado ? $p->estado->color_badge : 'bg-slate-100 text-slate-700',
            'estado' => $p->situacion_actual ?: ($p->estado ? $p->estado->descripcion_estado : 'En trámite ordinario'),
            'situacion_actual' => $p->situacion_actual,

            'fecha' => $p->fecha_inicio ? $p->fecha_inicio->format('d/m/Y') : date('d/m/Y'),
            'fecha_inicio_raw' => $p->fecha_inicio ? $p->fecha_inicio->format('Y-m-d') : date('Y-m-d'),
            'abogado' => $p->abogado ? $p->abogado->name : 'Alan Sillerico Segurondo',
            'abogado_id' => $p->abogado_id,
            'telefono' => $p->cliente ? ($p->cliente->celular_whatsapp ?: '59177234317') : '59177234317',
            'correo' => $p->cliente ? ($p->cliente->email ?: 'legal@bufete-sillerico.com') : 'legal@bufete-sillerico.com',

            'hitos' => $p->actuaciones ? $p->actuaciones->map(function ($act) {
                return [
                    'id' => $act->id,
                    'fecha' => $act->fecha_hora ? $act->fecha_hora->format('d/m/Y H:i') : '',
                    'accion' => $act->titulo_actuacion,
                    'abogado' => $act->user ? $act->user->name : 'Alan Sillerico Segurondo',
                    'comentarios' => $act->descripcion,
                    'documentos' => $act->documentos ? $act->documentos->pluck('nombre_original')->toArray() : [],
                ];
            })->values()->toArray() : [],
            'documentos' => $p->documentos ? $p->documentos->pluck('nombre_original')->toArray() : [],
            'auditorias' => $p->auditorias ? $p->auditorias->map(function ($aud) {
                return [
                    'id' => $aud->id,
                    'accion' => $aud->accion,
                    'descripcion' => $aud->descripcion,
                    'usuario' => $aud->user ? $aud->user->name : 'Sistema',
                    'cargo' => $aud->user ? $aud->user->cargo : 'Personal',
                    'iniciales' => $aud->user ? $aud->user->iniciales : 'SB',
                    'color' => $aud->user ? $aud->user->color : 'bg-slate-700 text-white',
                    'fecha' => $aud->created_at ? $aud->created_at->format('d/m/Y H:i') : '',
                ];
            })->values()->toArray() : [],
        ];
    }
}