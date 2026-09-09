<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaProceso;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditoriaController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = AuditoriaProceso::with(['proceso', 'user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('user_id') && $request->user_id !== 'Todos') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('accion') && $request->accion !== 'Todas') {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('descripcion', 'like', "%{$term}%")
                  ->orWhereHas('proceso', function ($pq) use ($term) {
                      $pq->where('codigo_interno', 'like', "%{$term}%")
                         ->orWhere('demandante_denunciante', 'like', "%{$term}%");
                  });
            });
        }

        $auditorias = $query->take(200)->get()->map(function ($a) {
            return [
                'id' => $a->id,
                'proceso_id' => $a->proceso_id,
                'proceso_codigo' => $a->proceso ? $a->proceso->codigo_interno : 'N/A',
                'proceso_materia' => $a->proceso ? $a->proceso->materia : '',
                'proceso_cliente' => $a->proceso ? $a->proceso->demandante_denunciante : '',
                'accion' => $a->accion,
                'descripcion' => $a->descripcion,
                'detalles' => $a->detalles,
                'ip_address' => $a->ip_address,
                'user_id' => $a->user_id,
                'user_nombre' => $a->user ? $a->user->name : 'Sistema',
                'user_cargo' => $a->user ? $a->user->cargo : 'Operador',
                'user_iniciales' => $a->user ? $a->user->iniciales : 'SO',
                'user_color' => $a->user ? $a->user->color : 'bg-slate-700 text-white',
                'fecha_hora' => $a->created_at ? $a->created_at->format('d/m/Y H:i') : '',
                'hace_tiempo' => $a->created_at ? $a->created_at->diffForHumans() : '',
            ];
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'auditorias' => $auditorias,
            ]);
        }

        $abogados = User::where('es_abogado', true)->orderBy('id', 'asc')->get();

        return view('admin.auditoria.index', compact('auditorias', 'abogados'));
    }
}