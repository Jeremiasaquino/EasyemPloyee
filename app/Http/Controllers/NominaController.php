<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use App\Models\Empleado;
use App\Models\Deducciones;
use App\Models\Beneficios;
use App\Models\Prestamos;
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
            'hora_extra' => 'numeric',
            'total_beneficios' => 'numeric',
            'total_deducciones' => 'numeric',
            'total_prestamos_adelanto' => 'numeric',
            'cuenta_bancaria' => 'nullable|string',
            'salario_neto' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $empleado = Empleado::with(['Beneficios', 'Deducciones', 'Prestamos'])->find($request->empleado_id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado.',
            ], 404);
        }

        // Calcular total de beneficios
        $totalBeneficios = $empleado->Beneficios->isEmpty() ? 0 : $empleado->Beneficios->sum('amount');

        // Calcular total de deducciones
        $totalDeducciones = $empleado->Deducciones->isEmpty() ? 0 : $empleado->Deducciones->sum('amount');

        // Calcular total de préstamos por adelantado
        $totalPrestamosAdelanto = $empleado->Prestamos->isEmpty() ? 0 : $empleado->Prestamos->sum('amount');

        // Calcular salario neto
        $salarioNeto = $empleado->salario + $request->hora_extra + $totalBeneficios - $totalDeducciones - $totalPrestamosAdelanto;

        // Calcular método de pago y cuenta bancaria
        $metodoPago = $empleado->cuenta_bancaria ? 'Transferencia' : 'Cheque';
        $cuentaBancaria = $empleado->cuenta_bancaria;

        $datosNomina = [
            'empleado_id' => $empleado->id,
            'salario' => $empleado->salario,
            'hora_extra' => $request->hora_extra,
            'total_beneficios' => $totalBeneficios,
            'total_deducciones' => $totalDeducciones,
            'total_prestamos_adelanto' => $totalPrestamosAdelanto,
            'salario_neto' => $salarioNeto,
            'metodo_pago' => $metodoPago,
            'cuenta_bancaria' => $cuentaBancaria,
            'fecha_nomina' => $request->fecha_nomina,
        ];

        $nomina = Nomina::create($datosNomina);

        return response()->json(['success' => true, 'data' => $nomina]);
    }
}
