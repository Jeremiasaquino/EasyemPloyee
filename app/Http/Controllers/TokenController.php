<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TokenController extends Controller
{
    
    // $authorizationHeader = $request->header('Authorization');
    // // echo($authorizationHeader);
    // $token = $request->bearerToken();
    //     echo($token);
public function validarToken(Request $request)
{
    $user = Auth::user();
    if ($user) {
        // El token es válido y el usuario está autenticado
        return response()->json([
            'data' => $user,
            'success' => 'Autenticado',
        ], 200);
    } else {
        // El token no es válido o el usuario no está autenticado
        return response()->json([
            'message' => 'No autenticado',
        ], 401);
    }
}
}