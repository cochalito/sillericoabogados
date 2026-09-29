<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Muestra la pantalla de inicio de sesión estilo Microsoft Account.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $teamUsers = User::where('activo', true)
            ->orderBy('id', 'asc')
            ->get(['id', 'name', 'email', 'cargo', 'iniciales', 'color']);

        return view('auth.login', compact('teamUsers'));
    }

    /**
     * Localiza al usuario por email exacto, alias/nombre de usuario (prefijo del correo) o nombre completo.
     */
    private function findUser(string $login): ?User
    {
        $login = trim($login);

        return User::where('email', $login)
            ->orWhere('name', $login)
            ->orWhere('email', $login . '@sillericoabogados.com')
            ->orWhereRaw("SUBSTRING_INDEX(email, '@', 1) = ?", [$login])
            ->first();
    }

    /**
     * Valida la existencia del identificador (correo o usuario) en el Paso 1 de Microsoft.
     */
    public function checkUser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'login' => 'required|string|max:255',
        ]);

        $user = $this->findUser($validated['login']);

        if (!$user) {
            return response()->json([
                'exists' => false,
                'message' => 'No pudimos encontrar una cuenta institucional con ese correo o usuario.',
            ], 404);
        }

        if (!$user->activo) {
            return response()->json([
                'exists' => false,
                'message' => 'Esta cuenta institucional se encuentra temporalmente inactiva. Contacte a la administración del bufete.',
            ], 403);
        }

        return response()->json([
            'exists' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'cargo' => $user->cargo,
                'iniciales' => $user->iniciales ?: strtoupper(substr($user->name, 0, 2)),
                'color' => $user->color ?: 'bg-brand-green text-brand-gold',
            ],
        ]);
    }

    /**
     * Procesa la autenticación de credenciales en el Paso 2 de Microsoft.
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        $remember = $request->boolean('remember');
        $user = $this->findUser($validated['login']);

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            $errorMessage = 'La contraseña que escribió para esta cuenta no es correcta. Asegúrese de que no tenga activado el bloqueo de mayúsculas.';
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 422);
            }

            return back()->withErrors([
                'password' => $errorMessage,
            ])->withInput($request->only('login', 'remember'));
        }

        if (!$user->activo) {
            $inactiveMessage = 'Esta cuenta institucional se encuentra inactiva. Comuníquese con el departamento de sistemas.';
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $inactiveMessage,
                ], 403);
            }

            return back()->withErrors([
                'login' => $inactiveMessage,
            ])->withInput($request->only('login', 'remember'));
        }

        // Iniciar sesión y fijar operador activo en session
        Auth::login($user, $remember);
        $request->session()->regenerate();
        session(['active_user_id' => $user->id]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Bienvenido al Sistema Jurídico, {$user->name}",
                'redirect' => route('dashboard'),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'cargo' => $user->cargo,
                ],
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Cierra la sesión activa del usuario y limpia la memoria de sesión.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->forget('active_user_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Ha cerrado sesión en su cuenta institucional de manera segura.');
    }
}
