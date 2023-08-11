<?php

namespace App\Http\Controllers;

use App\Models\Prestamos;
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

        if ($Prestamos->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay registros en la tabla.',
            ]);
        }

        $formattedPrestamos = $Prestamos->map(function ($prestamo) {
            $monto = 'RD$' . number_format($prestamo->monto, 2, '.', '');
            $prestamo->monto = $monto;
            return $prestamo;
        });

        return response()->json([
            'success' => true,
            'data' => $formattedPrestamos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|exists:empleados,id',
            'monto' => 'required|numeric',
            // 'fecha_prestamo' => 'required|date',
            'estado' => 'required|in:Activo,Pagado',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }
        $request['fecha_prestamo'] = now(); // Establecer la fecha actual
        $Prestamos = Prestamos::create($request->all());
        return response()->json(['success' => true, 'data' => $Prestamos]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $prestamo = Prestamos::find($id);

        if (!$prestamo) {
            return response()->json([
                'success' => false,
                'message' => 'Prestamo no encontrado.',
            ], 404);
        }

        $montoFormateado = 'RD$' . number_format($prestamo->monto, 2, '.', '');
        $prestamo->monto = $montoFormateado;

        return response()->json(['success' => true, 'data' => $prestamo]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $prestamo = Prestamos::find($id);

        if (!$prestamo) {
            return response()->json([
                'success' => false,
                'message' => 'Prestamo no encontrado.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'empleado_id' => 'exists:empleados,id',
            'monto' => 'numeric',
            'fecha_prestamo' => 'date',
            'estado' => 'in:Activo,Pagado',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $prestamo->update($request->all());

        return response()->json(['success' => true, 'data' => $prestamo]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prestamo = Prestamos::find($id);

        if (!$prestamo) {
            return response()->json([
                'success' => false,
                'message' => 'Prestamo no encontrado.',
            ], 404);
        }

        $prestamo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Prestamo eliminado exitosamente.',
        ]);
    }
}
