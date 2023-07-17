<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\Auth\LoginController;





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
Route::middleware('auth:sanctum')->group(function () {

    // Ruta para cerrar sesión
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Rutas relacionadas con los usuarios
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
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
        // Actualizar un empleado específico
        Route::put('/{id}', [EmpleadoController::class, 'update']);
        // Eliminar un empleado específico
        Route::delete('/{id}', [EmpleadoController::class, 'destroy']);
        // ... otras rutas relacionadas con los empleados
    });

});