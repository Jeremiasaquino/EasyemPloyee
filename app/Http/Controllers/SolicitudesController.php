<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Solicitudes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SolicitudesController extends Controller
{
    /**
     * Obtener todas las solicitudes de permisos/ausencias.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $solicitudes = Solicitudes::with('empleado')->get();
        
        return response()->json([
            'succs' => true,
            'data' => $solicitudes,
        ]);
    }

    /**
     * Crear una nueva solicitud de permiso/ausencia para un empleado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validar los datos de la solicitud antes de guardarla
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|exists:empleados,id',
            'tipo' => 'required|in:Permiso,Vacaciones,Licencia,Ausencia',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'required|string',
            'documento_apoyo' => 'nullable|mimes:pdf,jpeg,png|max:2048',
        ]);

        // Si la validación falla, devolver los errores en formato JSON
        if ($validator->fails()) {
            return response()->json([
                'succs' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Calcular la duración en días utilizando Carbon
        $fechaInicio = Carbon::parse($request->input('fecha_inicio'));
        $fechaFin = Carbon::parse($request->input('fecha_fin'));
        $duracionEnDias = $fechaInicio->diffInDays($fechaFin) + 1; // Sumamos 1 para incluir el día de inicio

        // Crear la solicitud con los datos validados, incluyendo la duración en días
        $solicitud = Solicitudes::create([
            'empleado_id' => $request->input('empleado_id'),
            'tipo' => $request->input('tipo'),
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin' => $request->input('fecha_fin'),
            'duracion' => $duracionEnDias,
            'motivo' => $request->input('motivo'),
            'estado' => 'Pendiente',
        ]);

        // Si se proporcionó un documento de apoyo, guardar el archivo
        if ($request->hasFile('documento_apoyo')) {
            $documentoApoyo = $request->file('documento_apoyo');
            // Aquí puedes implementar la lógica para guardar el archivo en el almacenamiento que desees
            // Por ejemplo, puedes usar el método store() de Laravel para almacenar el archivo en el sistema de archivos o en la nube.
            // Consulta la documentación de Laravel para obtener más información sobre el almacenamiento de archivos.
        }

        return response()->json([
            'succs' => true,
            'msg' => 'Solicitud creada exitosamente.',
            'data' => $solicitud,
        ], 201);
    }

    /**
     * Obtener una solicitud específica por su ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $solicitud = Solicitudes::with('empleado')->find($id);

        if (!$solicitud) {
            return response()->json([
                'succs' => false,
                'msg' => 'Solicitud no encontrada.',
            ], 404);
        }
        $duracionEnDias = $solicitud->duracion_en_dias;
        return response()->json([
            'succs' => true,
            'data' => $solicitud,
             'duracion_en_dias' => $duracionEnDias,
        ]);
    }

    /**
     * Actualizar una solicitud existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validar los datos actualizados de la solicitud antes de guardarlos
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'exists:empleados,id',
            'tipo' => 'in:Permiso,Vacaciones,Licencia,Ausencia',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date|after_or_equal:fecha_inicio',
            'motivo' => 'string',
            'documento_apoyo' => 'nullable|mimes:pdf,jpeg,png|max:2048',
        ]);

        // Si la validación falla, devolver los errores en formato JSON
        if ($validator->fails()) {
            return response()->json([
                'succs' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $solicitud = Solicitudes::find($id);

        if (!$solicitud) {
            return response()->json([
                'succs' => false,
                'msg' => 'Solicitud no encontrada.',
            ], 404);
        }

        // Calcular la duración en días utilizando Carbon si se actualizaron las fechas
        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $fechaInicio = Carbon::parse($request->input('fecha_inicio'));
            $fechaFin = Carbon::parse($request->input('fecha_fin'));
            $duracionEnDias = $fechaInicio->diffInDays($fechaFin) + 1; // Sumamos 1 para incluir el día de inicio
            $request->merge(['duracion' => $duracionEnDias]);
        }

        // Actualizar la solicitud con los datos validados
        $solicitud->update($request->all());

        // Si se proporcionó un documento de apoyo actualizado, guardar el archivo
        if ($request->hasFile('documento_apoyo')) {
            $documentoApoyo = $request->file('documento_apoyo');
            // Aquí puedes implementar la lógica para guardar el archivo actualizado en el almacenamiento que desees
            // Por ejemplo, puedes usar el método store() de Laravel para almacenar el archivo en el sistema de archivos o en la nube.
            // Consulta la documentación de Laravel para obtener más información sobre el almacenamiento de archivos.
        }

        return response()->json([
            'succs' => true,
            'msg' => 'Solicitud actualizada exitosamente.',
            'data' => $solicitud,
        ]);
    }

    /**
     * Eliminar una solicitud específica por su ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $solicitud = Solicitudes::find($id);

        if (!$solicitud) {
            return response()->json([
                'succs' => false,
                'msg' => 'Solicitud no encontrada.',
            ], 404);
        }

        // Aquí puedes implementar la lógica para eliminar el documento de apoyo, si existe, antes de eliminar la solicitud

        $solicitud->delete();

        return response()->json([
            'succs' => true,
            'msg' => 'Solicitud eliminada exitosamente.',
        ]);
    }
}
