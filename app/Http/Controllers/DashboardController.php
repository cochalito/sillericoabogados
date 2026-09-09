<?php

namespace App\Http\Controllers;

use App\Models\Actuacion;
use App\Models\Cliente;
use App\Models\EventoCalendario;
use App\Models\Proceso;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalProcesos = Proceso::count();
        $casosPenales = Proceso::whereHas('materia', fn($q) => $q->where('nombre', 'like', '%Penal%'))->count();
        $casosCiviles = Proceso::whereHas('materia', fn($q) => $q->where('nombre', 'like', '%Civil%'))->count();
        $casosFamiliares = Proceso::whereHas('materia', fn($q) => $q->where('nombre', 'like', '%Familiar%'))->count();
        $casosLaborales = Proceso::whereHas('materia', fn($q) => $q->where('nombre', 'like', '%Laboral%'))->count();
        $casosCorporativos = Proceso::whereHas('materia', fn($q) => $q->where('nombre', 'like', '%Comercial%')->orWhere('nombre', 'like', '%Corporativo%'))->count();

        $totalClientes = Cliente::count();

        // Upcoming hearings and deadlines
        $proximosEventos = EventoCalendario::with(['proceso', 'user'])
            ->where('estado', 'Pendiente')
            ->orderBy('fecha_hora_inicio', 'asc')
            ->take(5)
            ->get();

        // Recent activity feed
        $ultimasActuaciones = Actuacion::with(['proceso', 'user'])
            ->orderBy('fecha_hora', 'desc')
            ->take(6)
            ->get();

        // Lawyer workload
        $abogados = User::where('es_abogado', true)
            ->withCount('procesos')
            ->orderBy('procesos_count', 'desc')
            ->get();

        $equipo = $abogados->map(function ($u) {
            return [
                'id' => $u->id,
                'nombre' => $u->name,
                'cargo' => $u->cargo,
                'iniciales' => $u->iniciales,
                'color' => $u->color,
                'activos' => $u->procesos_count,
            ];
        });

        // Formatted top processes for dashboard table
        $procesos = Proceso::with(['cliente', 'abogado', 'actuaciones'])
            ->orderBy('id', 'desc')
            ->take(15)
            ->get()
            ->map(function ($p) {
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
                    'hitos' => [],
                    'documentos' => [],
                ];
            });

        // Status counts
        $casacion = Proceso::whereHas('estado', fn($q) => $q->where('nombre', 'like', '%Casación%'))->count();
        $apelacion = Proceso::whereHas('estado', fn($q) => $q->where('nombre', 'like', '%Apelación%'))->count();
        $sentencia = Proceso::whereHas('estado', fn($q) => $q->where('nombre', 'like', '%Sentencia%'))->count();
        $rebeldia = Proceso::whereHas('estado', fn($q) => $q->where('nombre', 'like', '%Rebeldía%'))->count();

        return view('admin.dashboard', compact(
            'totalProcesos',
            'casosPenales',
            'casosCiviles',
            'casosFamiliares',
            'casosLaborales',
            'casosCorporativos',
            'totalClientes',
            'proximosEventos',
            'ultimasActuaciones',
            'abogados',
            'equipo',
            'procesos',
            'casacion',
            'apelacion',
            'sentencia',
            'rebeldia'
        ));
    }
}