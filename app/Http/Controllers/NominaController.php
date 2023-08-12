<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use App\Models\Empleado;
use App\Models\Deducciones;
use App\Models\Beneficios;
use App\Models\Prestamos;
use App\Models\Asistencia;
use App\Models\Tss;
use Illuminate\Http\Request;
use Carbon\Carbon;
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
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $nominas,
        ], 202);
    }

    // Crear una nueva nómina
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|exists:empleados,id',
            'hora_extra' => 'numeric',
            'total_beneficios' => 'numeric',
            'total_deducciones' => 'numeric',
            'total_prestamos_adelanto' => 'numeric',
            'cuenta_bancaria' => 'nullable|string',
            'salario_neto' => 'numeric',
        ], [
            'empleado_id.required' => 'Es Id del empleado requerido',
            'empleado_id.exists' => 'Es empleado no existe',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $empleado = Empleado::with(['Beneficios', 'Deducciones', 'Prestamos', 'informacionLarabol', 'Asistencia'])->find($request->empleado_id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado.',
            ], 404);
        }
        // Salario del empleado
        $salario = $empleado->informacionLarabol->salario;

        // Hora extra del empleado
        if ($empleado->Asistencia) {
            $hora_extra = $empleado->Asistencia->hora_extra;
        } else {
            $hora_extra = 0;
        }
        $horasTrabajadasPorMes = 160;
        $pagoPorHora = $salario / $horasTrabajadasPorMes;
        $pagoPorHoraExtra = $pagoPorHora * $hora_extra;
    
        // Calcular total de beneficios
        $totalBeneficios = $empleado->Beneficios->isEmpty() ? 0 : $empleado->Beneficios->sum('monto');
    
        // Calcular total de deducciones
        $totalDeducciones = $empleado->Deducciones->isEmpty() ? 0 : $empleado->Deducciones->sum('monto');
    
        // Calcular total de préstamos por adelantado
        $totalPrestamosAdelanto = $empleado->Prestamos->isEmpty() ? 0 : $empleado->Prestamos->sum('monto');
    
        // Obtener el valor de la columna Tss para el empleado
        $tssRegistros = Tss::all();
        if (!$tssRegistros->isEmpty()) {
            $tssValor = 0;
            foreach ($tssRegistros as $tss) {
                $tssValor += ($tss->porcentaje / 100) * $salario;
            }
            // Añadir el valor total de la columna Tss al total de deducciones y al salario neto
            $totalDeducciones += $tssValor;
            // Calcular salario neto
            $salarioNeto = $salario + $totalBeneficios +$pagoPorHoraExtra - $totalDeducciones - $totalPrestamosAdelanto;
        } else {
            // Calcular salario neto sin Tss
            $salarioNeto = $salario +$totalBeneficios +$pagoPorHoraExtra -$totalDeducciones -$totalPrestamosAdelanto;
            // Establecer el valor de la columna Tss en cero si no se encuentra para el empleado
            $tssValor = 0;
        }
        

        
        $metodoPago = $empleado->cuenta_bancaria ? 'Transferencia' : 'Cheque';
        $cuentaBancaria = $empleado->cuenta_bancaria;
        $request['fecha_nomina'] = now()->format('Y-m-d');
        $datosNomina = [
            'empleado_id' => $empleado->id,
            'salario' => $salario,
            'hora_extra' => $pagoPorHoraExtra,
            'total_beneficios' => $totalBeneficios,
            'total_deducciones' => $totalDeducciones,
            'total_prestamos_adelanto' => $totalPrestamosAdelanto,
            'salario_neto' => $salarioNeto,
            'metodo_pago' => $metodoPago,
            'cuenta_bancaria' => $cuentaBancaria,
            'fecha_nomina' => $request->fecha_nomina,
            'tss' => $tssValor,
        ];

        $nomina = Nomina::create($datosNomina);
        $empleado = Empleado::findOrFail($request->empleado_id);
        $nomina->setAttribute('codigo_empleado', $empleado->codigo_empleado);
        $nomina->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);
        return response()->json(['success' => true,  'data' => $nomina]);
    }


     /**
 * Mostrar las asistencias pasadas de un empleado.
 *
 * @param  int  $empleadoId
 * @param  string  $fecha
 * @return \Illuminate\Http\JsonResponse
 */
    public function nominasPasadas($fecha)
    {
        try {
            $fechaCarbon = Carbon::parse($fecha);
    
            $nominas = Nomina::join('Empleados', 'Nomina.empleado_id', '=', 'Empleados.id')
                ->where('fecha_nomina', '=', $fechaCarbon->toDateString()) // aquí se cambió el operador de comparación
                ->select('Nomina.*', 'Empleados.nombre', 'Empleados.codigo_empleado')
                ->get();
    
            foreach ($nominas as $nomina) {
                $nomina->fecha_nomina = Carbon::parse($nomina->fecha_nomina)->format('Y-m-d');
            }
    
            return response()->json([
                'success' => true,
                'data' => $nominas,
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron nóminas para la fecha especificada.',
            ], 404);
        }
    }
    

     /**
 * Mostrar las asistencias pasadas de un empleado.
 *
 * @param  int  $empleadoId
 * @param  string  $fecha
 * @return \Illuminate\Http\JsonResponse
 */
    public function nominasToday()
{
    try {
        $fechaCarbon = Carbon::today();
        $nominas = Nomina::join('Empleados', 'Nomina.empleado_id', '=', 'Empleados.id')
            ->where('fecha_nomina', $fechaCarbon->toDateString())
            ->select('Nomina.*', 'Empleados.nombre', 'Empleados.codigo_empleado')
            ->get();

        foreach ($nominas as $nomina) {
            $nomina->fecha_nomina = Carbon::parse($nomina->fecha_nomina)->format('Y-m-d');
        }

        return response()->json([
            'success' => true,
            'data' => $nominas,
        ], 201);
    } catch (\Exception $e) {
        echo($e);
        return response()->json([
            'success' => false,
            'message' => 'No se encontraron nóminas para la fecha de hoy.',
        ], 404);
    }
}


}
