<?php

namespace App\Services;

use App\Models\AuditoriaProceso;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function getCurrentUser(): ?User
    {
        $userId = session('active_user_id');
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                return $user;
            }
        }

        return auth()->user() ?? User::where('es_abogado', true)->first();
    }

    public static function log(
        int $procesoId,
        string $accion,
        string $descripcion,
        ?array $detalles = null,
        ?int $userId = null
    ): AuditoriaProceso {
        if (!$userId) {
            $current = self::getCurrentUser();
            $userId = $current ? $current->id : null;
        }

        return AuditoriaProceso::create([
            'proceso_id' => $procesoId,
            'user_id' => $userId,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'detalles' => $detalles,
            'ip_address' => Request::ip(),
            'created_at' => now(),
        ]);
    }
}