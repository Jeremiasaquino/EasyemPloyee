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
    $beneficios = Beneficios::all();

    // Agregar atributos dinámicos a cada objeto de beneficios
    $beneficios = $beneficios->map(function ($beneficio) {
        $empleado = Empleado::findOrFail($beneficio->empleado_id);
        $beneficio->setAttribute('codigo_empleado', $empleado->codigo_empleado);
        $beneficio->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);
        return $beneficio;
    });

    if ($beneficios->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No hay registros en la tabla.',
        ]);
    }

    return response()->json([
        'success' => true,
        'data' => $beneficios,
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|exists:empleados,id',
            'beneficio' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
        ], [
            'empleado_id.required' => 'El id de empleado es requerido',
            'empleado_id.exists' => 'El empleado no existe',
            'beneficio.required' => 'El Beneficio es requerido',
            'beneficio.string' => 'El Beneficio debe ser una descripcion',
            'monto.required' => 'El monto es requerido',
            'monto.numeric' => 'El monto debe ser numeros',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'msg' => 'Error en los datos enviados', 'errors' => $validator->errors()], 422);
        }

        $empleado = Empleado::findOrFail($request->empleado_id);
        $beneficios = Beneficios::create([
            'empleado_id' => $request->input('empleado_id'),
            'beneficio' => $request->input('beneficio'),
            'monto' => $request->input('monto'),
        ]);
        $beneficios->setAttribute('codigo_empleado', $empleado->codigo_empleado);
        $beneficios->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);

        return response()->json([
        'success' => true, 
        'message' => 'Beneficios creado con éxito', 
        'data' => $beneficios,
    ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Beneficios = Beneficios::find($id);

        if (!$Beneficios) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficios no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $Beneficios,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validar los datos recibidos del formulario
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'beneficio' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
        ],
        [
            'empleado_id.required' => 'El id de empleado es requerido',
            'empleado_id.exists' => 'El empleado no existe',
            'beneficio.required' => 'El Beneficio es requerido',
            'beneficio.string' => 'El Beneficio debe ser una descripcion',
            'monto.required' => 'El monto es requerido',
            'monto.numeric' => 'El monto debe ser numeros',
        ]);

        $beneficios = Beneficios::find($id);
        if (!$beneficios) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado.',
            ]);
        }

        // Actualizar los campos del modelo con los datos del formulario
        $beneficios->update([
            'empleado_id' => $request->input('empleado_id'),
            'beneficio' => $request->input('beneficio'),
            'monto' => $request->input('monto'),
        ]);

         $empleado = Empleado::findOrFail($beneficios->empleado_id);
        $beneficios->setAttribute('codigo_empleado', $empleado->codigo_empleado);
        $beneficios->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);
        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado exitosamente.',
            'data' => $beneficios,
        ]);
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
                'message' => 'Registro no encontrado.',
            ]);
        }

        $beneficio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro eliminado exitosamente.',
        ]);
    }
}