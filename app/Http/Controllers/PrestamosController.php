<?php

namespace App\Http\Controllers;

use App\Models\Prestamos;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrestamosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Prestamos = Prestamos::all();

        $Prestamos = $Prestamos->map(function ($prestamo) {
            $empleado = Empleado::findOrFail($prestamo->empleado_id);
            $prestamo->setAttribute('codigo_empleado', $empleado->codigo_empleado);
            $prestamo->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);
            return $prestamo;
        });
        if ($Prestamos->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay registros en la tabla.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $Prestamos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date', 'date_format:Y-m-d',
            'monto' => 'required|numeric',
            'empleado_id' => 'required|exists:empleados,id',
        ],[
            'fecha.required' => 'La fecha es requerida',
            'fecha.date' => 'La fecha debe ser tipo y-m-d',
            'monto.required' => 'El monto es requerido',
            'monto.numeric' => 'El monto debe ser numerico',
            'empleado_id.required' => 'El id del empleado es requerido',
            'empleado_id.exists' => 'No se encontro ningun empleado con ese id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'msg' => 'Error en los datos enviados', 'errors' => $validator->errors()], 422);
        }

        $Prestamos = Prestamos::create([
            'empleado_id' => $request->input('empleado_id'),
            'fecha' => $request->input('fecha'),
            'monto' => $request->input('monto'),
        ]);
        $empleado = Empleado::findOrFail($request->empleado_id);
        $Prestamos->setAttribute('codigo_empleado', $empleado->codigo_empleado);
        $Prestamos->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);

        return response()->json(['success' => true, 'message' => 'Prestamo creado con éxito', 'data' => $Prestamos], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Prestamos = Prestamos::find($id);

        if (!$Prestamos) {
            return response()->json([
                'success' => false,
                'message' => 'Prestamo no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $Prestamos,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date', 'date_format:Y-m-d',
            'monto' => 'required|numeric',
            'empleado_id' => 'required|exists:empleados,id',
        ], [
            'fecha.required' => 'La fecha es requerida',
            'fecha.date' => 'La fecha debe ser tipo y-m-d',
            'monto.required' => 'El monto es requerido',
            'monto.numeric' => 'El monto debe ser numerico',
            'empleado_id.required' => 'El id del empleado es requerido',
            'empleado_id.exists' => 'No se encontro ningun empleado con ese id',
        ]);

        $Prestamos = Prestamos::find($id);

        if (!$Prestamos) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado.',
            ]);
        }

        // Actualizar los campos del modelo con los datos del formulario
        $Prestamos->update([
            'empleado_id' => $request->input('empleado_id'),
            'fecha' => $request->input('fecha'),
            'monto' => $request->input('monto'),
        ]);
        $empleado = Empleado::findOrFail($request->empleado_id);
        $Prestamos->setAttribute('codigo_empleado', $empleado->codigo_empleado);
        $Prestamos->setAttribute('nombre', $empleado->nombre . ' ' . $empleado->apellidos);
        
        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado exitosamente.',
            'data' => $Prestamos,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Prestamos = Prestamos::find($id);

        if (!$Prestamos) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado.',
            ]);
        }

        $Prestamos->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro eliminado exitosamente.',
        ]);
    }
}