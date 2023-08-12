<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\BeneficiosController;
use App\Http\Controllers\DeduccionesController;
use App\Http\Controllers\PrestamosController;
use App\Http\Controllers\TssController;
use App\Http\Controllers\NominaController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Ruta para iniciar sesión
Route::post('/login', [LoginController::class, 'login'])->name('login');

// Rutas protegidas por el middleware de autenticación 'auth:sanctum'
// Route::middleware('auth:sanctum')->group(function () {
    
        // Ruta para cerrar sesión
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:api');
// Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/validar-token', [TokenController::class, 'validarToken'])->middleware('auth:api');
// Route::middleware('auth:api')->get('/validar-token', function (Request $request) {
//     return $request->user();
// });

    // Rutas relacionadas con los usuarios
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::post('/admin', [UserController::class, 'adminCreate']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::put('/admin/{id}', [UserController::class, 'adminUpdate']);
    // ... otras rutas relacionadas con los usuarios

    // Ruta para obtener información del usuario autenticado
    Route::get('/user-info', [UserController::class, 'getUserInfo']);
});

// Rutas relacionadas con los empleados
Route::prefix('empleados')->group(function () {
    // Obtener todos los empleados
    Route::get('/', [EmpleadoController::class, 'index']);
    // Crear un nuevo empleado
    Route::post('/', [EmpleadoController::class, 'store']);
    // Obtener los detalles de un empleado específico
    Route::get('/{id}', [EmpleadoController::class, 'show']);
    // Obtener empleados por departamentos
    Route::get('/depart/{departId}', [EmpleadoController::class, 'getEmpleForDepart']);
    // Actualizar un empleado específico
    Route::put('/{id}', [EmpleadoController::class, 'update']);
    // Eliminar un empleado específico
    Route::delete('/{id}', [EmpleadoController::class, 'destroy']);
    // ... otras rutas relacionadas con los empleados
});

// Rutas relacionadas con los cargos
Route::prefix('cargo')->group(function () {
    // Obtener todos los cargos
    Route::get('/', [CargoController::class, 'index']);
    // Crear un nuevo cargo
    Route::post('/', [CargoController::class, 'store']);
    // Obtener los detalles de un cargo específico
    Route::get('/{id}', [CargoController::class, 'show']);
    // Actualizar un cargo específico
    Route::put('/{id}', [CargoController::class, 'update']);
    // Eliminar un cargo específico
    Route::delete('/{id}', [CargoController::class, 'destroy']);
    // Ruta para obtener los empleados de un cargo
    Route::get('/{cargoID}/empleados', [CargoController::class, 'getEmployees']);
});

// Rutas relacionadas con los departamentos
Route::prefix('departamentos')->group(function () {
    // Obtener todos los departamentos
    Route::get('/', [DepartamentoController::class, 'index']);
    // Crear un nuevo departamento
    Route::post('/', [DepartamentoController::class, 'store']);
    // Obtener los detalles de un departamento específico
    Route::get('/{id}', [DepartamentoController::class, 'show']);
    // Actualizar un departamento específico
    Route::put('/{id}', [DepartamentoController::class, 'update']);
    // Eliminar un departamento específico
    Route::delete('/{id}', [DepartamentoController::class, 'destroy']);
    // Ruta para obtener los empleados de un departamento
    //   Route::get('/{departmentId}/empleados', [DepartamentoController::class, 'getEmployees']);
});

// Rutas relacionadas con los horarios
Route::prefix('horarios')->group(function () {
    // Obtener todos los horarios
    Route::get('/', [HorarioController::class, 'index']);
    // Crear un nuevo horario
    Route::post('/', [HorarioController::class, 'store']);
    // Obtener los detalles de un horario específico
    Route::get('/{id}', [HorarioController::class, 'show']);
    // Actualizar un horario específico
    Route::put('/{id}', [HorarioController::class, 'update']);
    // Eliminar un horario específico
    Route::delete('/{id}', [HorarioController::class, 'destroy']);
    // Ruta para obtener los empleados de una hora
    Route::get('/{horarioId}/empleados', [HorarioController::class, 'getEmployees']);
});

// Rutas relacionadas con los asistencia
Route::prefix('asistencia')->group(function () {
    // Obtener todos las asistencia
    Route::get('/', [AsistenciaController::class, 'index']);
    // Traer asistencia de empleado en particular por Id empleado
    Route::get('/empleado/{id}', [AsistenciaController::class, 'asistenciaToday']);
    // Crear una nueva asistencia
    Route::post('/', [AsistenciaController::class, 'store']);
    // Actualizar
    Route::put('/{id}', [AsistenciaController::class, 'update']);
    // Ruta para obtener la asistencia de un empleado en una fecha específica
    // Route::get('/{empleadoId}/{fecha}', [AsistenciaController::class, 'show']);
    Route::get('/{fecha}', [AsistenciaController::class, 'asistenciaPasadas']);
});

// Rutas para obtener y gestionar deducciones de un empleado específico
Route::prefix('deducciones')->group(function () {
    Route::get('/', [DeduccionesController::class, 'index']);
    Route::get('/{id}', [DeduccionesController::class, 'show']);
    Route::post('/', [DeduccionesController::class, 'store']);
    Route::put('/{id}', [DeduccionesController::class, 'update']);
    Route::delete('/{id}', [DeduccionesController::class, 'destroy']);
});

// Rutas para obtener y gestionar beneficios de un empleado específico
Route::prefix('beneficios')->group(function () {
    Route::get('/', [BeneficiosController::class, 'index']);
    Route::get('/{id}', [BeneficiosController::class, 'show']);
    Route::post('/', [BeneficiosController::class, 'store']);
    Route::put('/{id}', [BeneficiosController::class, 'update']);
    Route::delete('/{id}', [BeneficiosController::class, 'destroy']);
});

// Rutas para obtener y gestionar prestamos de un empleado específico
Route::prefix('prestamos')->group(function () {
    Route::get('/', [PrestamosController::class, 'index']);
    Route::get('/{id}', [PrestamosController::class, 'show']);
    Route::post('/', [PrestamosController::class, 'store']);
    Route::put('/{id}', [PrestamosController::class, 'update']);
    Route::delete('/{id}', [PrestamosController::class, 'destroy']);
});

// Rutas para obtener y gestionar beneficios de un empleado específico
Route::prefix('tss')->group(function () {
    Route::get('/', [TssController::class, 'index']);
    Route::get('/{id}', [TssController::class, 'show']);
    Route::post('/', [TssController::class, 'store']);
    Route::put('/{id}', [TssController::class, 'update']);
    Route::delete('/{id}', [TssController::class, 'destroy']);
});

Route::prefix('nomina')->group(function () {
    // Obtener todos las asistencia
    Route::get('/', [NominaController::class, 'index']);
    // Buscar nomina por fecha
    Route::get('/nomina_pasada/{fecha}', [NominaController::class, 'nominasPasadas']);

    Route::get('/nomina_hoy', [NominaController::class, 'nominasToday']);
    // Obtener las nominas de hoy
    // Crear un nuevo horario
    Route::post('/', [NominaController::class, 'store']);
   
});

// });
