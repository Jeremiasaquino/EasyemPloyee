<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NominaController extends Controller
{
    // Obtener lista de nóminas
    public function index()
    {
        $nominas = Nomina::all();

        if ($nominas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay registros de nóminas.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $nominas,
        ]);
    }

    // Crear una nueva nómina
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_nomina' => 'required|date',
            'empleado_id' => 'required|exists:empleados,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        // Obtener datos del empleado
        $empleado = Empleado::find($request->empleado_id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado.',
            ], 404);
        }

        // Combinar datos del empleado con los valores proporcionados
        $datosNominas = [
            'empleado_id' => $empleado->id,
            'salario' => $empleado->salario,
            'hora_extra' => $request->hora_extra,
            'total_beneficios' => $request->total_beneficios,
            'total_deducciones' => $request->total_deducciones,
            'total_prestamos_adelanto' => $request->total_prestamos_adelanto,
            'salario_neto' => $empleado->salario + $request->hora_extra + $request->total_beneficios - $request->total_deducciones - $request->total_prestamos_adelanto,
            'metodo_pago' => $empleado->cuenta_bancaria ? 'Transferencia' : 'Cheque',
            'cuenta_bancaria' => $empleado->cuenta_bancaria,
        ];

        $nomina = Nomina::create($datosNominas);
        return response()->json(['success' => true, 'data' => $nomina]);
    }
}
