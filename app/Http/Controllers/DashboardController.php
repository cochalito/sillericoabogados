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
        $casosPenales = Proceso::where('materia', 'Penal')->count();
        $casosCiviles = Proceso::where('materia', 'Civil')->count();
        $casosFamiliares = Proceso::where('materia', 'Familiar')->count();
        $casosLaborales = Proceso::where('materia', 'Laboral')->count();
        $casosCorporativos = Proceso::where('materia', 'Corporativo')->count();

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
        $casacion = Proceso::where('estado', 'Casación')->count();
        $apelacion = Proceso::where('estado', 'Apelación')->count();
        $sentencia = Proceso::where('estado', 'Sentencia')->count();
        $rebeldia = Proceso::where('estado', 'Rebeldía')->count();

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