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

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            if ($user->estado == 'Inactivo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario inactivo, hablar con el administrador.',
                ], 401);
            }
            else{
                $token = $user->createToken($request->email)->plainTextToken;
                $user->update(['api_token' => $token]);
    
                return response()->json([
                    'success' => 'Autenticado',
                    'message' => 'Inicio de sesión exitoso',
                    'data' => $user,
                    'token' => $token,
                ],200);
            }
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
