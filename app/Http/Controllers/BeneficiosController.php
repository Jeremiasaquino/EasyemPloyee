<?php

namespace App\Http\Controllers;

use App\Models\Beneficios;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BeneficiosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Beneficios = Beneficios::all();

        if ($Beneficios->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay registros en la tabla.',
            ], 404);
        }

        $formattedBeneficios = $Beneficios->map(function ($beneficio) {
            $empleado = Empleado::findOrFail($beneficio->empleado_id);
            $beneficio->setAttribute('codigo_empleado', $empleado->codigo_empleado);
            $beneficio->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);

            return $beneficio;
        });

        return response()->json([
            'success' => true,
            'data' => $formattedBeneficios,
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|exists:empleados,id',
            'beneficio' => 'required|string|max:255',
            'monto' => 'required|numeric',
            'tipo_beneficio' => 'required||string|max:255',
            'estado' => 'required|in:Activo,Inactivo',
        ],[
            'empleado_id.required' => 'El empleado es requerido',
            'empleado_id.exists' => 'El empleado no existe',
            'beneficio.required' => 'El nombre de beneficio es requerida',
            'monto.required' => 'El monto es requerido',
            'monto.numeric' => 'El monto debe ser numero',
            'tipo_beneficio.required' => 'El Tipo de Beneficio es requerido',
            'estado.required' => 'El estado es requerido',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        // Validar si el empleado ya tiene la misma deducción
    $deduccionExistente = Beneficios::where('empleado_id', $request->empleado_id)
    ->where('beneficio', $request->beneficio)
    ->first();

    if ($deduccionExistente) {
        return response()->json(['success' => false, 'message' => 'El empleado ya tiene este beneficio.'], 404);
    }

        $Beneficios = Beneficios::create($request->all());
        $empleado = Empleado::findOrFail($request->empleado_id);
        $Beneficios->setAttribute('codigo_empleado', $empleado->codigo_empleado);
        $Beneficios->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);
        return response()->json(['success' => true,  'message' => 'Beneficio Registrado.', 'data' => $Beneficios]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $beneficio = Beneficios::find($id);

        if (!$beneficio) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficio no encontrado.',
            ], 404);
        }

        $montoFormateado = 'RD$' . number_format($beneficio->monto, 2, '.', '');
        $beneficio->monto = $montoFormateado;

        return response()->json(['success' => true, 'data' => $beneficio]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $beneficio = Beneficios::find($id);

        if (!$beneficio) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficio no encontrado.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'empleado_id' => 'exists:empleados,id',
            'beneficio' => 'string|max:255',
            'monto' => 'numeric',
            'tipo_beneficio' => 'string|max:255',
            'estado' => 'in:Activo,Inactivo',
        ],[
            'empleado_id.required' => 'El empleado es requerido',
            'empleado_id.exists' => 'El empleado no existe',
            'beneficio.required' => 'El nombre de beneficio es requerida',
            'monto.required' => 'El monto es requerido',
            'monto.numeric' => 'El monto debe ser numero',
            'tipo_beneficio.required' => 'El Tipo de Beneficio es requerido',
            'estado.required' => 'El estado es requerido',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $deduccionExistente = Beneficios::where('empleado_id', $request->empleado_id)
    ->where('beneficio', $request->beneficio)
     ->whereNotIn('id', [$id]) 
    ->first();

    if ($deduccionExistente) {
        return response()->json(['success' => false, 'message' => 'El empleado ya tiene este beneficio.'], 404);
    }
    
        $beneficio->update($request->all());
        $empleado = Empleado::findOrFail($request->empleado_id);
        $beneficio->setAttribute('codigo_empleado', $empleado->codigo_empleado);
        $beneficio->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);
        return response()->json(['success' => true, 'message' => 'Beneficio actualizado.','data' => $beneficio]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $beneficio = Beneficios::find($id);

        if (!$beneficio) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficio no encontrado.',
            ], 404);
        }

        $beneficio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Beneficio eliminado exitosamente.',
        ]);
    }
}