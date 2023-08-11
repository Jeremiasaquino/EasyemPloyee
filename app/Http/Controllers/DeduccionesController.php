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
            ]);
        }

        $formattedDeducciones = $Deducciones->map(function ($deduccion) {
            $monto = $deduccion->monto;

            if ($deduccion->tipo_deduccion === 'Fijo') {
                $monto = 'RD$' . number_format($monto, 2, '.', '');
            } elseif ($deduccion->tipo_deduccion === 'Porcentaje') {
                $monto = number_format($monto, 2, '.', '') . '%';
            }

            $deduccion->monto = $monto;
            return $deduccion;
        });

        return response()->json(['success' => true, 'data' => $formattedDeducciones]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|exists:empleados,id',
            'nombre' => 'required|string|max:255',
            'monto' => 'required|numeric',
            'tipo_deduccion' => 'required|in:Fijo,Porcentaje',
            'estado' => 'required|in:Activa,Inactiva',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $Deducciones = Deducciones::create($request->all());
        return response()->json(['success' => true, 'data' => $Deducciones]);
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

        if ($deduccion->tipo_deduccion === 'Fijo') {
            $monto = 'RD$' . number_format($monto, 2, '.', '');
        } elseif ($deduccion->tipo_deduccion === 'Porcentaje') {
            $monto = number_format($monto, 2, '.', '') . '%';
        }

        $deduccion->monto = $monto;

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
            'nombre' => 'string|max:255',
            'monto' => 'numeric',
            'tipo_deduccion' => 'in:Fijo,Porcentaje',
            'estado' => 'in:Activa,Inactiva',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $deduccion->update($request->all());

        return response()->json(['success' => true, 'data' => $deduccion]);
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
