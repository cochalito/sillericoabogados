<?php

use App\Http\Controllers\ActuacionController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProcesoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// 1. Dashboard Principal
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// 2. Control de Procesos
Route::get('/procesos', [ProcesoController::class, 'index'])->name('procesos.index');
Route::post('/procesos', [ProcesoController::class, 'store'])->name('procesos.store');
Route::get('/procesos/{id}', [ProcesoController::class, 'show'])->name('procesos.show');
Route::put('/procesos/{id}', [ProcesoController::class, 'update'])->name('procesos.update');
Route::delete('/procesos/{id}', [ProcesoController::class, 'destroy'])->name('procesos.destroy');

// 3. Actuaciones / Bitácora del Proceso
Route::post('/procesos/{procesoId}/actuaciones', [ActuacionController::class, 'store'])->name('actuaciones.store');

// 4. Clientes
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('clientes.show');
Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');

// 5. Calendario y Audiencias
Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
Route::get('/api/calendario/eventos', [CalendarioController::class, 'apiEvents'])->name('calendario.api.events');
Route::post('/api/calendario/eventos', [CalendarioController::class, 'store'])->name('calendario.api.store');
Route::put('/api/calendario/eventos/{id}', [CalendarioController::class, 'update'])->name('calendario.api.update');
Route::delete('/api/calendario/eventos/{id}', [CalendarioController::class, 'destroy'])->name('calendario.api.destroy');

// 6. Usuarios del Bufete & Cambio Rápido de Sesión
Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
Route::match(['get', 'post'], '/switch-user/{id}', [UsuarioController::class, 'switchUser'])->name('usuarios.switch');

// 7. Registro de Actividades / Auditoría
Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');

// 8. Enlaces del Nuevo Menú Multinivel (Navegación)
Route::get('/documentos', fn() => view('admin.placeholder', ['modulo' => 'Documentos']))->name('documentos.index');
Route::get('/parametros', fn() => view('admin.placeholder', ['modulo' => 'Parámetros del Sistema']))->name('parametros.index');
Route::get('/articulos', fn() => view('admin.placeholder', ['modulo' => 'Artículos y Leyes']))->name('articulos.index');
Route::get('/roles', fn() => view('admin.placeholder', ['modulo' => 'Roles y Permisos (RBAC)']))->name('roles.index');