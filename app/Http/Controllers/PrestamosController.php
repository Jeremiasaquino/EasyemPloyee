<?php

namespace App\Http\Controllers;

use App\Models\Prestamos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrestamosController extends Controller
{
    /**
     * Mostrar una lista de los préstamos de un empleado.
     *
     * @param  int  $empleadoId
     * @return \Illuminate\Http\Response
     */
    public function index($empleadoId)
    {
        try {
            // Obtener los préstamos del empleado
            $prestamos = Prestamos::where('empleado_id', $empleadoId)->get();

            return response()->json([
                'success' => true,
                'data' => $prestamos,
            ], 200);
        } catch (\Exception $e) {
            // En caso de error, devolver una respuesta de error
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los préstamos del empleado.',
            ], 500);
        }
    }

    /**
     * Almacenar un nuevo préstamo para un empleado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $empleadoId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $empleadoId)
    {
        try {
            // Validar los datos enviados en el request
            $validator = Validator::make($request->all(), [
                'fecha' => 'required|date',
                'monto' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos.',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Crear el nuevo préstamo
            $prestamo = Prestamos::create([
                'fecha' => $request->input('fecha'),
                'monto' => $request->input('monto'),
                'empleado_id' => $empleadoId,
            ]);

            return response()->json([
                'success' => true,
                'data' => $prestamo,
            ], 201);
        } catch (\Exception $e) {
            // En caso de error, devolver una respuesta de error
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el préstamo.',
            ], 500);
        }
    }

    /**
     * Eliminar un préstamo específico de un empleado.
     *
     * @param  int  $empleadoId
     * @param  int  $prestamoId
     * @return \Illuminate\Http\Response
     */
    public function destroy($empleadoId, $prestamoId)
    {
        try {
            // Buscar el préstamo por id y empleado_id para asegurar que pertenece al empleado
            $prestamo = Prestamos::where('id', $prestamoId)->where('empleado_id', $empleadoId)->first();

            if (!$prestamo) {
                return response()->json([
                    'success' => false,
                    'message' => 'El préstamo no existe o no pertenece al empleado.',
                ], 404);
            }

            // Eliminar el préstamo
            $prestamo->delete();

            return response()->json([
                'success' => true,
                'message' => 'El préstamo ha sido eliminado exitosamente.',
            ], 200);
        } catch (\Exception $e) {
            // En caso de error, devolver una respuesta de error
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el préstamo.',
            ], 500);
        }
    }
}
