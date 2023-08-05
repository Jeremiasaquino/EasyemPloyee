<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Deducciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeduccionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deduccion = Deducciones::all();

        if ($deduccion->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay registros en la tabla.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $deduccion,
        ]);
    }

    /**
     * Store a newly created resource in storage.
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

        $sueldo_empleado = $empleado->sueldo;

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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $deduccion = Deducciones::find($id);

        if (!$deduccion) {
            return response()->json([
                'success' => false,
                'message' => 'Deducciones no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $deduccion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $deduccion = Deducciones::find($id);

        if (!$deduccion) {
            return response()->json(['success' => false, 'msg' => 'No se encontró la deducción'], 404);
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:255',
            'porcentaje_empleado' => 'nullable|numeric|min:0|max:100',
            'monto' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'msg' => 'Error en los datos enviados', 'errors' => $validator->errors()], 422);
        }

        // Obtener el sueldo del empleado desde la base de datos
        $empleado = Empleado::find($deduccion->empleado_id);
        if (!$empleado) {
            return response()->json(['success' => false, 'msg' => 'No se encontró el empleado asociado a la deducción'], 422);
        }

        // Verificar si se proporciona el porcentaje o el monto
        $porcentaje_empleado = $request->input('porcentaje_empleado');
        $monto = $request->input('monto');

        if ($porcentaje_empleado !== null) {
            // Si se proporciona el porcentaje, calcular el monto
            $monto = $empleado->sueldo * ($porcentaje_empleado / 100);
        } elseif ($monto === null) {
            // Si no se proporciona ni el porcentaje ni el monto, devolver un error
            return response()->json(['success' => false, 'msg' => 'Debe proporcionar el porcentaje o el monto'], 422);
        }

        $deduccion->update([
            'descripcion' => $request->input('descripcion'),
            'porcentaje_empleado' => $porcentaje_empleado,
            'monto' => $monto,
        ]);

        return response()->json(['success' => true, 'msg' => 'Deducción actualizada con éxito', 'deduccion' => $deduccion], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deduccion = Deducciones::find($id);

        if (!$deduccion) {
            return response()->json(['success' => false, 'msg' => 'No se encontró la deducción'], 404);
        }

        $deduccion->delete();

        return response()->json(['success' => true, 'msg' => 'Deducción eliminada con éxito'], 200);
    }
}
