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
    
        $empleado = Empleado::find($request->empleado_id);
    
        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado.',
            ], 404);
        }
    
        // Calcular total de beneficios
        $totalBeneficios = $empleado->Beneficios->sum('amount');
    
        // Calcular total de deducciones
        $totalDeducciones = $empleado->Deducciones->sum('amount');
    
        // Calcular total de préstamos por adelantado
        $totalPrestamosAdelanto = $empleado->Prestamos->sum('amount');
    
        // Calcular salario neto
        $salarioNeto = $empleado->salario + $request->hora_extra + $totalBeneficios - $totalDeducciones - $totalPrestamosAdelanto;
    
        // Calcular método de pago y cuenta bancaria
        $metodoPago = $empleado->cuenta_bancaria ? 'Transferencia' : 'Cheque';
        $cuentaBancaria = $empleado->cuenta_bancaria;
    
        $datosNominas = [
            'empleado_id' => $empleado->id,
            'salario' => $empleado->salario,
            'hora_extra' => $request->hora_extra,
            'total_beneficios' => $totalBeneficios,
            'total_deducciones' => $totalDeducciones,
            'total_prestamos_adelanto' => $totalPrestamosAdelanto,
            'salario_neto' => $salarioNeto,
            'metodo_pago' => $metodoPago,
            'cuenta_bancaria' => $cuentaBancaria,
            'fecha_nomina' => $request->fecha_nomina, // Asegúrate de agregar la fecha
        ];
    
        $nomina = Nomina::create($datosNominas);
        return response()->json(['success' => true, 'data' => $nomina]);
    }
}
