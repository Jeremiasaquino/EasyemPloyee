<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los usuarios
        $users = User::all();

        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Crear un nuevo usuario utilizando el nombre y apellido del empleado.

        // Validar los datos de entrada
        $request->validate([
            'codigo_empleado' => 'required',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:Administrador,Recursos Humanos,Gerente,Empleado',
            'estado' => 'required|in:Activo,Inactivo',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|min:6',
        ]);

        // Buscar el empleado por código_empleado
        $empleado = Empleado::where('codigo_empleado', $request->input('codigo_empleado'))->first();

        if (!$empleado) {
            return response()->json(['message' => 'No se encontró el empleado correspondiente al código_empleado'], 404);
        }

        // Crear el usuario
        $user = new User();
        $user->name = $empleado->nombre . ' ' . $empleado->apellidos;
        $user->email = $request->input('email');
        $user->role = $request->input('role');
        $user->estado = $request->input('estado');
        $user->password = bcrypt($request->input('password'));
        $user->empleado_id = $empleado->id;
        $user->save();

        return response()->json(['message' => 'Usuario creado con éxito'], 201);
    }

    /**
     * Display the specified resouarce.
     */
    public function show(string $id)
    {
        // Obtener el usuario por su ID
        $user = User::findOrFail($id);

        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Obtener el usuario por su ID
        $user = User::findOrFail($id);

        // Validar los datos de entrada
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:Administrador,Recursos Humanos,Gerente,Empleado',
            'estado' => 'required|in:Activo,Inactivo',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Actualizar los datos del usuario
        $user->email = $request->input('email');
        $user->role = $request->input('role');
        $user->estado = $request->input('estado');
        $user->save();

        return response()->json(['message' => 'Usuario actualizado con éxito'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Obtener el usuario por su ID
        $user = User::findOrFail($id);

        // Eliminar el usuario
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado con éxito'], 200);
    }

    /**
     * Obtener información del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo(Request $request)
    {
        // Verificar si hay un usuario autenticado
        if (Auth::check()) {
            // Obtener el usuario autenticado
            $user = Auth::user();

            // Acceder a la información del usuario
            $userId = $user->id;
            $userName = $user->name;
            $userEmail = $user->email;
            // ...

            // Retornar los datos del usuario como respuesta
            return response()->json([
                'user_id' => $userId,
                'name' => $userName,
                'email' => $userEmail,
                // ...
            ], 200);
        } else {
            // No hay usuario autenticado
            return response()->json(['message' => 'No se encontró un usuario autenticado'], 404);
        }
    }
}
