<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View|JsonResponse
    {
        $usuarios = User::withCount('procesos')
            ->orderBy('id', 'asc')
            ->get();

        $currentUser = AuditService::getCurrentUser();

        return view('admin.usuarios.index', compact('usuarios', 'currentUser'));
    }

    public function switchUser($id): RedirectResponse|JsonResponse
    {
        $user = User::findOrFail($id);
        session(['active_user_id' => $user->id]);
        Auth::login($user);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Sesión cambiada a {$user->name}",
                'user' => $user,
            ]);
        }

        return redirect()->back()->with('success', "Sesión cambiada a {$user->name}");
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cargo' => 'required|string|max:100',
            'password' => 'nullable|string|min:6',
            'telefono' => 'nullable|string|max:50',
            'color' => 'nullable|string',
        ]);

        $nameParts = explode(' ', trim($validated['name']));
        $iniciales = '';
        foreach (array_slice($nameParts, 0, 2) as $part) {
            $iniciales .= strtoupper(substr($part, 0, 1));
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'] ?? 'password123'),
            'cargo' => $validated['cargo'],
            'iniciales' => $iniciales ?: 'AB',
            'color' => $validated['color'] ?? 'bg-brand-green text-white',
            'telefono' => $validated['telefono'] ?? null,
            'es_abogado' => true,
            'activo' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente.',
            'user' => $user,
        ], 201);
    }
}