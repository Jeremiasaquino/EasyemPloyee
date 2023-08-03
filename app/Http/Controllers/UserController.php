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
        // $users = User::where('role', '<>', 'Developer')->get();
    
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
            'codigo_empleado' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:Administrador,Recursos Humanos,Gerente,Empleado',
            'estado' => 'required|in:Activo,Inactivo',
            'password' => 'required|min:6',
        ], [
            'codigo_empleado.required' => 'El Codigo es obligatorio.',
            'codigo_empleado.unique' => 'Ya existe un usuario con este codigo.',
            'email.required' => 'El email es obligatorio.',
            'email.unique' => 'El email debe ser unico.',
            'role.required' => 'El rol es obligatorio.',
            'password.required' => 'El password es obligatorio.',
            'password.min' => 'El password debe tener al menos 6 caracteres.',
        ]);

        // Buscar el empleado por código_empleado
        $empleado = Empleado::where('codigo_empleado', $request->input('codigo_empleado'))->first();

        if (!$empleado) {
            return response()->json(['
                message' => 'No se encontró el empleado correspondiente al'  . ' ' .$request->codigo_empleado
            ], 404);
        }

        // Crear el usuario
        $user = new User();
        $user->nombre = $empleado->nombre . ' ' . $empleado->apellidos;
        $user->codigo_empleado = $empleado->codigo_empleado;
        $user->email = $request->input('email');
        $user->role = $request->input('role');
        $user->estado = $request->input('estado');
        $user->password = bcrypt($request->input('password'));
        $user->empleado_id = $empleado->id;
        $user->save();

        return response()->json([
            'message' => 'Usuario creado con éxito',
            'msgDescription' => 'Usuario registrado!',
            'data' => $user
        ], 201);
    }
    

    public function adminCreate (Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:Administrador',
            'estado' => 'required|in:Activo,Inactivo',
        ], [
            'email.required' => 'El email es obligatorio.',
            'email.unique' => 'El email debe ser unico.',
            'password.required' => 'El password es obligatorio.',
            'password.min' => 'El password debe tener al menos 6 caracteres.',
        ]);

        // Crear el usuario
        $user = new User();
        $user->nombre = $request->input('nombre');
        $user->email = $request->input('email');
        $user->role = $request->input('role');
        $user->estado = $request->input('estado');
        $user->password = bcrypt($request->input('password'));
        $user->save();

        return response()->json([
            'message' => 'Usuario creado con éxito',
            'msgDescription' => 'Usuario registrado!',
            'data' => $user
        ], 201);
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
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $request->validate([
            'codigo_empleado' => 'required|unique:users,codigo_empleado,' . $id,
            'role' => 'required|in:Recursos Humanos,Gerente,Empleado',
            'estado' => 'required|in:Activo',
            'foto' => 'nullable|string',
            'foto_id' => 'nullable|string',
        ], [
            'codigo_empleado.required' => 'El Codigo es obligatorio.',
            'codigo_empleado.unique' => 'Ya existe un empleado con este codigo' . ' ' .$user->codigo_empleado,
            'role.required' => 'El rol es obligatorio.',
        ]);

        // Actualizar los datos del usuario
        // $user->email = $request->input('email');
        $empleado = Empleado::where('codigo_empleado', $request->input('codigo_empleado'))->first();

        if (!$empleado) {
            return response()->json([
                'message' => 'No se encontró el empleado correspondiente al codigo' . ' ' .$user->codigo_empleado
            ], 404);
        }
        
        $user->codigo_empleado = $empleado->codigo_empleado;
        $user->role = $request->input('role');
        $user->estado = $request->input('estado');
        $user->foto = $request->input('foto');
        $user->foto_id = $request->input('foto_id');
        $user->empleado_id = $empleado->id;
        $user->save();

        return response()->json([
            'message' => 'Usuario actualizado con éxito',
            'msgDescription' => 'Usuario modificado!',
            'data' => $user
        ], 200);
    }


    public function adminUpdate (Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $request->validate([
            'codigo_empleado' => 'nullable|string',
            'email' => 'required|unique:users,email,' . $id,
            'nombre' => 'required|string|sometimes',
            'role' => 'required|in:Administrador',
            'foto_id' => 'nullable|string',
            'foto' => 'nullable|string',
            'estado' => 'required|in:Activo,Inactivo',
            // 'password' => 'required|min:6',
        ], [
            'email.required' => 'El email es obligatorio.'. ' '.  $user->email,
            'email.unique' => 'Existe un usuario con este email.' . ' '.  $user->email,
            'nombre.required' => 'El nombre es obligatorio.'. ' '.  $user->nombre,
            // 'password.required' => 'El password es obligatorio.',
            // 'password.min' => 'El password debe tener al menos 6 caracteres.',
        ]);

        $user->codigo_empleado = $request->input('codigo_empleado');
        $user->nombre = $request->input('nombre');
        $user->email = $request->input('email');
        $user->role = $request->input('role');
        $user->estado = $request->input('estado');
        $user->foto = $request->input('foto');
        $user->foto_id = $request->input('foto_id');
        $user->empleado_id = null;
        $user->save();

        return response()->json([
            'message' => 'Usuario actualizado con éxito',
            'msgDescription' => 'Usuario modificado!',
            'data' => $user
        ], 200);
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
            $userName = $user->nombre;
            $userEmail = $user->email;
            // ...

            // Retornar los datos del usuario como respuesta
            return response()->json([
                'user_id' => $userId,
                'nombre' => $userName,
                'email' => $userEmail,
                // ...
            ], 200);
        } else {
            // No hay usuario autenticado
            return response()->json(['message' => 'No se encontró un usuario autenticado'], 404);
        }
    }
}
