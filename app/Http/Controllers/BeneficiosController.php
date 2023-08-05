<?php

namespace App\Http\Controllers;

use App\Models\Beneficios;
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
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $Beneficios,
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
            'monto' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'msg' => 'Error en los datos enviados', 'errors' => $validator->errors()], 422);
        }

        $Beneficios = Beneficios::create([
            'empleado_id' => $request->input('empleado_id'),
            'descripcion' => $request->input('descripcion'),
            'monto' => $request->input('monto'),
        ]);

        return response()->json(['success' => true, 'msg' => 'Beneficios creado con éxito', 'Beneficios' => $Beneficios], 201);
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
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
        ]);

        $beneficio = Beneficios::find($id);

        if (!$beneficio) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado.',
            ]);
        }

        // Actualizar los campos del modelo con los datos del formulario
        $beneficio->update([
            'empleado_id' => $request->input('empleado_id'),
            'descripcion' => $request->input('descripcion'),
            'monto' => $request->input('monto'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado exitosamente.',
            'data' => $beneficio,
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
