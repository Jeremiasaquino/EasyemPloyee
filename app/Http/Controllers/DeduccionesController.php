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
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $deduccion,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deduccion' => 'required|unique:deducciones|string|max:255',
            'porcentaje_deduccion' => 'required|numeric|min:0|max:100',
        ], [
            'deduccion.required' => 'La deduccion es requerida',
            'deduccion.unique' => 'La deduccion debe ser unica',
            'porcentaje_deduccion.required' => 'El porcentaje es requerido',
            'porcentaje_deduccion.max' => 'El porcentaje Debe tener maximo 3 digitos',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Error en los datos enviados', 'errors' => $validator->errors()], 422);
        }

        // Verificar si se proporciona el porcentaje o el monto

        $deduccion = Deducciones::create([
            'deduccion' => $request->input('deduccion'),
            'porcentaje_deduccion' => $request->input('porcentaje_deduccion')
        ]);

        $value = $deduccion->porcentaje_deduccion;
        $deduccion->porcentaje_deduccion = number_format($value, 2);

        return response()->json(['success' => true, 'message' => 'Deducción creada con éxito', 'data' => $deduccion], 201);
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
            'deduccion' => 'required|unique:deducciones,deduccion,' . $id . '|string|max:255',
            'porcentaje_deduccion' => 'required|numeric|min:0|max:100',
        ],[
            'deduccion.required' => 'La deduccion es requerida',
            'porcentaje_deduccion.required' => 'El porcentaje es requerido',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Error en los datos enviados', 'errors' => $validator->errors()], 422);
        }

        // Verificar si se proporciona el porcentaje o el monto
        

        $deduccion->update([
            'deduccion' => $request->input('deduccion'),
            'porcentaje_deduccion' => $request->input('porcentaje_deduccion')
        ]);
        
        $value = $deduccion->porcentaje_deduccion;
        $deduccion->porcentaje_deduccion = number_format($value, 2);

        return response()->json(['success' => true, 'message' => 'Deducción actualizada con éxito', 'data' => $deduccion], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deduccion = Deducciones::find($id);

        if (!$deduccion) {
            return response()->json(['success' => false, 'message' => 'No se encontró la deducción'], 404);
        }

        $deduccion->delete();

        return response()->json(['success' => true, 'message' => 'Deducción eliminada con éxito'], 200);
    }
}