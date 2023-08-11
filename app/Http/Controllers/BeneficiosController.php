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

        $formattedBeneficios = $Beneficios->map(function ($beneficio) {
            $monto = 'RD$' . number_format($beneficio->monto, 2, '.', '');
            $beneficio->monto = $monto;
            return $beneficio;
        });

        return response()->json([
            'success' => true,
            'data' => $formattedBeneficios,
        ]);
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
            'tipo_beneficio' => 'required||string|max:255',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $Beneficios = Beneficios::create($request->all());
        return response()->json(['success' => true, 'data' => $Beneficios]);
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
            'nombre' => 'string|max:255',
            'monto' => 'numeric',
            'tipo_beneficio' => 'string|max:255',
            'estado' => 'in:Activo,Inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $beneficio->update($request->all());

        return response()->json(['success' => true, 'data' => $beneficio]);
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
