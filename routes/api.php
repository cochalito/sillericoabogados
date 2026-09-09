<?php

use App\Http\Controllers\Api\ArticuloLeyController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\EstadoProcesoController;
use App\Http\Controllers\Api\EtapaProcesalController;
use App\Http\Controllers\Api\GradoPolicialController;
use App\Http\Controllers\Api\InvestigadorController;
use App\Http\Controllers\Api\JuezController;
use App\Http\Controllers\Api\JurisdiccionController;
use App\Http\Controllers\Api\JuzgadoController;
use App\Http\Controllers\Api\MateriaController;
use App\Http\Controllers\Api\MetaController;
use App\Http\Controllers\Api\PermisoController;
use App\Http\Controllers\Api\ProcesoController;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\RolParteController;
use App\Http\Controllers\Api\SalaController;
use App\Http\Controllers\Api\SujetoProcesalController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Support\Facades\Route;

// 1. Meta / Lookup para inicialización rápida
Route::get('/meta/bootstrap', [MetaController::class, 'bootstrap'])->name('api.meta.bootstrap');

// 2. Paramétricas Base
Route::apiResource('materias', MateriaController::class);
Route::apiResource('jurisdicciones', JurisdiccionController::class);
Route::apiResource('roles-partes', RolParteController::class);
Route::apiResource('estados-proceso', EstadoProcesoController::class);
Route::apiResource('etapas-procesales', EtapaProcesalController::class);

// 3. Estrados Judiciales y Policía
Route::apiResource('juzgados', JuzgadoController::class);
Route::apiResource('salas', SalaController::class);
Route::apiResource('jueces', JuezController::class);
Route::apiResource('grados-policiales', GradoPolicialController::class);
Route::apiResource('investigadores', InvestigadorController::class);

// 4. Catálogo de Normativa Legal
Route::apiResource('articulos-ley', ArticuloLeyController::class);

// 5. Sujetos Procesales & Clientes
Route::apiResource('sujetos-procesales', SujetoProcesalController::class);
Route::apiResource('clientes', ClienteController::class);

// 6. Seguridad & RBAC
Route::apiResource('roles', RolController::class);
Route::post('roles/{id}/permisos', [RolController::class, 'syncPermisos'])->name('api.roles.permisos');
Route::get('permisos', [PermisoController::class, 'index'])->name('api.permisos.index');
Route::get('permisos/{id}', [PermisoController::class, 'show'])->name('api.permisos.show');
Route::apiResource('usuarios', UsuarioController::class);

// 7. Procesos Judiciales y Sub-recursos
Route::apiResource('procesos', ProcesoController::class);
Route::post('procesos/{id}/articulos', [ProcesoController::class, 'syncArticulos'])->name('api.procesos.articulos');
Route::post('procesos/{id}/actuaciones', [ProcesoController::class, 'storeActuacion'])->name('api.procesos.actuaciones');