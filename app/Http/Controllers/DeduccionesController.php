<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Deducciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeduccionesController extends Controller
{
    /**
     * Mostrar una lista de todas las deducciones.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $deducciones = Deducciones::all();
        return response()->json([
            'success' => true,
            'data' => $deducciones,
        ], 200);
    }

    /**
     * Mostrar la deducción especificada por ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $deduccion = Deducciones::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $deduccion,
        ], 200);
    }

    /**
     * Almacenar una nueva deducción en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|exists:empleados,id',
            'descripcion' => 'required|string|max:255',
            'porcentaje_empleado' => 'nullable|numeric|min:0|max:100',
            'monto' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'msg' => 'Error en los datos enviados', 'errors' => $validator->errors()], 422);
        }

        // Obtener el sueldo del empleado desde la base de datos
        $empleado = Empleado::find($request->input('empleado_id'));
        if (!$empleado) {
            return response()->json(['success' => false, 'msg' => 'No se encontró el empleado'], 404);
        }

        $sueldo_empleado = $empleado->salario;

        // Verificar si se proporciona el porcentaje o el monto
        $porcentaje_empleado = $request->input('porcentaje_empleado');
        $monto = $request->input('monto');

        if ($porcentaje_empleado !== null) {
            // Si se proporciona el porcentaje, calcular el monto
            $monto = $sueldo_empleado * ($porcentaje_empleado / 100);
        } elseif ($monto === null) {
            // Si no se proporciona ni el porcentaje ni el monto, devolver un error
            return response()->json(['success' => false, 'msg' => 'Debe proporcionar el porcentaje o el monto'], 422);
        }

        $deduccion = Deducciones::create([
            'empleado_id' => $request->input('empleado_id'),
            'descripcion' => $request->input('descripcion'),
            'porcentaje_empleado' => $porcentaje_empleado,
            'monto' => $monto,
        ]);

        return response()->json(['success' => true, 'msg' => 'Deducción creada con éxito', 'deduccion' => $deduccion], 201);
    }


}
