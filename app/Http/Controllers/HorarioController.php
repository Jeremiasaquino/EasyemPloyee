<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    /**
     * Muestra una lista de todos los horarios.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $horarios = Horario::all();

        return response()->json([
            'success' => true,
            'data' => $horarios,
        ]);
    }

    /**
     * Almacena un nuevo horario en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'turno' => 'required|string|max:255',
            'hora_entrada' => 'required|date_format:h:i A', // Ejemplo: 12:00 pm
            'hora_salida' => 'required|date_format:h:i A', // Ejemplo: 01:00 am
            'dias_semana' => 'required|array',
            'dias_semana.*' => 'required|string|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'estado' => 'required|in:Abierto,Cerrado',
        ], [
            'turno.required' => 'El campo Nombre es obligatorio.',
            'hora_entrada.required' => 'La hora de entrada es requerida.',
            'hora_entrada.date_format' => 'La hora de entrada debe estar en formato HH:MM am/pm.',
            'hora_salida.required' => 'La hora de salida es requerida.',
            'hora_salida.date_format' => 'La hora de salida debe estar en formato HH:MM am/pm.',
            'dias_semana.required' => 'Los días de la semana son requeridos.',
            'dias_semana.array' => 'Los días de la semana deben ser proporcionados como un arreglo.',
            'dias_semana.*.in' => 'El valor :attribute no es un día válido de la semana.',
            'estado.required' => 'El campo Estado es obligatorio.',
            'estado.in' => 'El campo Estado debe ser Abierto o Cerrado.',
        ]);

        $horario = Horario::create([
            'turno' => $request->turno,
            'dias_semana' => $request->dias_semana,
            'hora_entrada' => $request->hora_entrada,
            'hora_salida' => $request->hora_salida,
            'estado' => $request->estado,
        ]);

        return response()->json([
            'success' => true,
            'data' => $horario,
            'message' => 'Horario creado exitosamente.',
            'msgDescription' => 'Horario Registrado!'
        ]);
    }

    /**
     * Muestra los detalles de un horario específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $horario = Horario::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $horario,
        ]);
    }

    /**
     * Actualiza un horario existente en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'turno' => 'required|string|max:255',
            'hora_entrada' => 'required|date_format:h:i A', // Ejemplo: 12:00 pm
            'hora_salida' => 'required|date_format:h:i A', // Ejemplo: 01:00 am
            'dias_semana' => 'required|array',
            'dias_semana.*' => 'required|string|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'estado' => 'required|in:Abierto,Cerrado',
        ], [
            'turno.required' => 'El campo Nombre es obligatorio.',
            'hora_entrada.required' => 'La hora de entrada es requerida.',
            'hora_entrada.date_format' => 'La hora de entrada debe estar en formato HH:MM am/pm.',
            'hora_salida.required' => 'La hora de salida es requerida.',
            'hora_salida.date_format' => 'La hora de salida debe estar en formato HH:MM am/pm.',
            'dias_semana.required' => 'Los días de la semana son requeridos.',
            'dias_semana.array' => 'Los días de la semana deben ser proporcionados como un arreglo.',
            'dias_semana.*.in' => 'El valor :attribute no es un día válido de la semana.',
            'estado.required' => 'El campo Estado es obligatorio.',
            'estado.in' => 'El campo Estado debe ser Abierto o Cerrado.',
        ]);

        $horario = Horario::findOrFail($id);
        $horario->turno = $request->turno;
        $horario->dias_semana = $request->dias_semana;
        $horario->hora_entrada = $request->hora_entrada;
        $horario->hora_salida = $request->hora_salida;
        $horario->estado = $request->estado;
        $horario->save();

        return response()->json([
            'success' => true,
            'data' => $horario,
            'message' => 'Horario actualizado exitosamente.',
            'msgDescription' => 'Horario Modificado!'
        ]);
    }

    /**
     * Elimina un horario de la base de datos.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $horario = Horario::findOrFail($id);
        $deleted = $horario->delete();

        if (!$deleted) {
            // El departamento no fue eliminado
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el horario porque hay empleados asignados a él.',
            ], 404);
        }
        else{
            return response()->json([
                'success' => true,
                'message' => 'Horario eliminado exitosamente.',
            ], 201);
        }
    }
}
