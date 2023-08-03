<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Maneja la solicitud de inicio de sesión.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validación de campos de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El campo de Correo electrónico es obligatorio.',
            'email.email' => 'El formato del Correo electrónico es inválido.',
            'password.required' => 'El campo de Contraseña es obligatorio.',
        ]);

        // Comprobar si hay errores de validación
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verificar si el correo electrónico existe y autenticar al usuario
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Aquí puedes agregar el código adicional que necesites después de autenticar al usuario
            // Generar un token de acceso con tiempo de expiración
            // $token = $user->createToken($request->email, ['expires_at' => now()->addHours(2)])->plainTextToken;
            // Generar un token de acceso
            $token = $user->createToken($request->email)->plainTextToken;
            $user->update(['api_token' => $token]);

            return response()->json([
                'success' => 'Autenticado',
                'message' => 'Inicio de sesión exitoso',
                'data' => $user,
                'token' => $token,
            ],200);
        }

        // La autenticación falló
        return response()->json([
            'success' => false,
            'message' => 'Credenciales inválidas',
        ], 401);
    }

    /**
     * Maneja la solicitud de cierre de sesión.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->update(['api_token' => null]);
        // Revocar todos los tokens del usuario
        $user->tokens()->delete();
    
        return response()->json([
            'success' => 'No_Autenticado',
            'message' => 'Cierre de sesión exitoso',
        ]);
    }
}
