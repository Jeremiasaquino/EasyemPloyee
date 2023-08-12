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
        $Deducciones = Deducciones::all();
        if ($Deducciones->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay registros en la tabla.',
            ], 404);
        }

        $formattedDeducciones = $Deducciones->map(function ($deduccion) {
            $monto = $deduccion->monto;

            $empleado = Empleado::findOrFail($deduccion->empleado_id);
            $deduccion->setAttribute('codigo_empleado', $empleado->codigo_empleado);
            $deduccion->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);

            return $deduccion;
        });

        return response()->json(['success' => true, 'data' => $formattedDeducciones], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'empleado_id' => 'required|exists:empleados,id',
        'deduccion' => 'required|string|max:255',
        'monto' => 'required|numeric',
        'tipo_deduccion' => 'required|in:Fijo,Porcentaje',
        'estado' => 'required|in:Activa,Inactiva',
    ], [
        'empleado_id.required' => 'El empleado es requerido',
        'empleado_id.exists' => 'El empleado no existe',
        'deduccion.required' => 'La deduccion es requerida',
        'monto.required' => 'El monto es requerido',
        'monto.numeric' => 'El monto debe ser numero',
        'tipo_deduccion.required' => 'El Tipo de deduccion es requerido',
        'estado.required' => 'El estado es requerido',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
    }

    // Validar si el empleado ya tiene la misma deducción
    $deduccionExistente = Deducciones::where('empleado_id', $request->empleado_id)
        ->where('deduccion', $request->deduccion)
        ->first();

    if ($deduccionExistente) {
        return response()->json(['success' => false, 'message' => 'El empleado ya tiene esta deducción.'], 404);
    }

    $Deducciones = Deducciones::create($request->all());
    $empleado = Empleado::findOrFail($request->empleado_id);
    $Deducciones->setAttribute('codigo_empleado', $empleado->codigo_empleado);
    $Deducciones->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);

    $monto = $Deducciones->monto;

    return response()->json(['success' => true, 'message' => 'Deducción Registrada con exito!.', 'data' => $Deducciones]);
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
                'message' => 'Deducción no encontrada.',
            ], 404);
        }

        $monto = $deduccion->monto;

        return response()->json(['success' => true, 'data' => $deduccion]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $deduccion = Deducciones::find($id);

        if (!$deduccion) {
            return response()->json([
                'success' => false,
                'message' => 'Deducción no encontrada.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'empleado_id' => 'exists:empleados,id',
            'deduccion' => 'string|max:255',
            'monto' => 'numeric',
            'tipo_deduccion' => 'in:Fijo,Porcentaje',
            'estado' => 'in:Activa,Inactiva',
        ], [
            'empleado_id.required' => 'El empleado es requerido',
            'empleado_id.exists' => 'El empleado no existe',
            'deduccion.required' => 'La deduccion es requerida',
            'monto.required' => 'El monto es requerido',
            'monto.numeric' => 'El monto debe ser numero',
            'tipo_deduccion.required' => 'El Tipo de deduccion es requerido',
            'estado.required' => 'El estado es requerido',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $deduccionExistente = Deducciones::where('empleado_id', $request->empleado_id)
        ->where('deduccion', $request->deduccion)
        ->whereNotIn('id', [$id]) 
        ->first();

    if ($deduccionExistente) {
        return response()->json(['success' => false, 'message' => 'El empleado ya tiene esta deducción.'], 404);
    }

        $deduccion->update($request->all());
        $empleado = Empleado::findOrFail($request->empleado_id);
        $deduccion->setAttribute('codigo_empleado', $empleado->codigo_empleado);
        $deduccion->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);

            
        return response()->json(['success' => true,  'message' => 'Deducción actualizada exitosamente.', 'data' => $deduccion]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deduccion = Deducciones::find($id);

        if (!$deduccion) {
            return response()->json([
                'success' => false,
                'message' => 'Deducción no encontrada.',
            ], 404);
        }

        $deduccion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deducción eliminada exitosamente.',
        ]);
    }
}