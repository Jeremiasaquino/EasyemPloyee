<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Empleado;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AsistenciaController extends Controller
{
    
    public function index()
{
    try {
        // Obtener la fecha actual
        $currentDate = now()->format('Y-m-d');

        // Obtenemos las asistencias con la relación empleado para el día actual
        $asistencias = Asistencia::with('empleado')
            ->whereDate('fecha', $currentDate)
            ->get();
            
        // Verificar si hay asistencias encontradas
        // if ($asistencias->isEmpty()) {
        //     // Respuesta con código 204 (sin contenido) indicando que no hay asistencia registrada para el día actual
        //     return response()->json(null, Response::HTTP_NO_CONTENT);
        // }

        // Formatear los datos de asistencia para la respuesta
        $data = [];
        foreach ($asistencias as $asistencia) {
            $data[]  = [
                'id' => $asistencia->id,
                'empleado_id' => $asistencia->empleado_id,
                'fecha' => $asistencia->fecha = Carbon::parse($asistencia->fecha)->format('Y-m-d'),
                'dia_semana' => $asistencia->dia_semana,
                'descripcion' => $asistencia->descripcion,
                'departamento' => $asistencia->empleado->departamento->departamento,
                'codigo_empleado' => $asistencia->empleado->codigo_empleado,
                'nombre' => $asistencia->empleado->getNombreCompletoAttribute(), // Asumiendo que el campo se llama 'nombre'
                'hora_entrada' => Carbon::parse($asistencia->hora_entrada)->format('h:i A'),
                'hora_salida' => ($asistencia->hora_salida) ? Carbon::parse($asistencia->hora_salida)->format('h:i A') : null,
                'estado' => $asistencia->estado,
                'horas_trabajas' => $asistencia->horas_trabajas,
                'hora_extra' => $asistencia->hora_extra,
                'hora_descanso_inicio' => ($asistencia->hora_descanso_inicio) ? Carbon::parse($asistencia->hora_descanso_inicio)->format('h:i A') : null,
                'hora_descanso_fin' => ($asistencia->hora_descanso_fin) ? Carbon::parse($asistencia->hora_descanso_fin)->format('h:i A') : null,
            ];
        }

        // Respuesta con código 200 (éxito) y los datos en formato JSON
        return response()->json(['data' => $data], Response::HTTP_OK);
    } catch (\Exception $e) {
        // En caso de error, respondemos con un mensaje de error y un código de error 500 (Error interno del servidor)
        return response()->json(['message' => 'Ha ocurrido un error al obtener las asistencias'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


  /**
 * Mostrar las asistencias pasadas de un empleado.
 *
 * @param  int  $empleadoId
 * @param  string  $fecha
 * @return \Illuminate\Http\JsonResponse
 */
public function asistenciaPasadas($fecha)
{
    // $empleadoId,
    try {
        $fechaCarbon = Carbon::parse($fecha);

        // $asistencias = Asistencia::where('fecha', $fecha)
        //     ->where('fecha', '<=', $fechaCarbon->toDateString())
        //     ->get();

        $asistencias = Asistencia::join('Empleados', 'Asistencia.empleado_id', '=', 'Empleados.id')
        ->join('Departamento', 'Empleados.departamento_id', '=', 'Departamento.id')
        ->where('fecha', $fecha)
        ->where('fecha', '<=', $fechaCarbon->toDateString())
        ->select('Asistencia.*', 'Empleados.nombre', 'Empleados.codigo_empleado', 'Departamento.departamento')
        ->get();


        foreach ($asistencias as $asistencia) {
            $asistencia->fecha = Carbon::parse($asistencia->fecha)->format('Y-m-d');
        }

        return response()->json([
            'success' => true,
            'data' => $asistencias,
        ],201);

    } catch (\Exception $e) {
        echo($e);
        return response()->json([
            'success' => false,
            'message' => 'No se encontraron asistencias para la fecha especificada.',
        ], 404);
    }
}


 /**
 * Mostrar las asistencias pasadas de un empleado.
 *
 * @param  int  $empleadoId
 * @param  string  $fecha
 * @return \Illuminate\Http\JsonResponse
 */
    public function asistenciaToday($empleadoId)
{
    try {
        $fechaCarbon = Carbon::today();
        $asistencia = Asistencia::where('empleado_id', $empleadoId)
        ->where('fecha', $fechaCarbon->toDateString())
        ->firstOrFail();
        $asistencia->fecha = Carbon::parse($asistencia->fecha)->format('Y-m-d');
        $asistencia->hora_descanso_inicio =Carbon::parse($asistencia->hora_descanso_inicio)->format('h:i A');
        $asistencia->hora_descanso_fin = Carbon::parse($asistencia->hora_descanso_fin)->format('h:i A');
        $asistencia->hora_entrada = Carbon::parse($asistencia->hora_entrada)->format('h:i A');
        $asistencia->hora_salida = $asistencia->hora_salida ? Carbon::parse($asistencia->hora_salida)->format('h:i A') : null;

        return response()->json([
            'success' => true,
            'data' => $asistencia,
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'descripcion' => '',
        ], 404);
    }
}

    /**
     * Registrar la asistencia de un empleado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos de la solicitud
            $this->validate($request, [
                'empleado_id' => 'required|exists:empleados,id',
                'hora_salida' => 'sometimes|nullable|date_format:h:i A', // hh:mm AM/PM
                'hora_entrada' => 'required|date_format:h:i A', // hh:mm AM/PM
                'hora_descanso_inicio' => 'required|date_format:h:i A', // hh:mm AM/PM
                'hora_descanso_fin' => 'required|date_format:h:i A', // hh:mm AM/PM
                'descripcion' => 'required|string', 
            ],);

            // Obtener el empleado por su ID
            $empleado = Empleado::findOrFail($request->empleado_id);

            // Convertir las horas a formato de 24 horas antes de guardar en el modelo Asistencia
            $horaEntrada = Carbon::createFromFormat('h:i A', $request->hora_entrada)->format('H:i');
            $horaSalida = ($request->has('hora_salida')) ? Carbon::createFromFormat('h:i A', $request->hora_salida)->format('H:i') : null;
            $horaDescansoInicio = ($request->has('hora_descanso_inicio')) ? Carbon::createFromFormat('h:i A', $request->hora_descanso_inicio)->format('H:i') : null;
            $horaDescansoFin = ($request->has('hora_descanso_fin')) ? Carbon::createFromFormat('h:i A', $request->hora_descanso_fin)->format('H:i') : null;

            // Verificar la existencia del modelo Horario asociado al empleado antes de acceder a sus atributos
            if (!$empleado->horario) {
                return response()->json([
                    'success' => false,
                    'message' => 'El empleado no tiene un horario asociado.',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Obtener el horario del empleado
            $horarioEntrada = Carbon::parse($empleado->horario->hora_entrada);
            $horarioSalida = Carbon::parse($empleado->horario->hora_salida);

            // Obtener las horas de entrada y salida de la solicitud
            $horaEntrada = Carbon::parse($request->hora_entrada);
            $horaSalida = ($request->has('hora_salida')) ? Carbon::parse($request->hora_salida) : null;

            // Verificar que la hora de salida sea posterior a la hora de entrada
            if ($horaSalida && $horaSalida->lessThanOrEqualTo($horaEntrada)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La hora de salida debe ser posterior a la hora de entrada.',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Verificar si el empleado llegó tarde o está ausente
            if ($horaEntrada > $horarioEntrada) {
                $estado = 'Tarde';
            } elseif ($horaEntrada->equalTo($horarioEntrada)) {
                $estado = 'Presente';
            } else {
                $estado = 'Ausente';
            }

            Carbon::setLocale('es');
            $fechaHoraActual = Carbon::now();
            $diaSemanaActual = ucfirst($fechaHoraActual->isoFormat('dddd'));


            // Crear la instancia de Asistencia y guardarla en la base de datos
            $asistencia = new Asistencia([
                'empleado_id' => $empleado->id,
                'dia_semana' => $diaSemanaActual,
                'fecha' =>  $fechaHoraActual,
                'hora_entrada' => $horaEntrada,
                'hora_salida' => $horaSalida,
                'hora_descanso_inicio' => $horaDescansoInicio,
                'hora_descanso_fin' => $horaDescansoFin,
                'hora_extra' => '0',
                'horas_trabajas' => '0',
                'estado' => $estado,
                'descripcion' => $request->descripcion,
                'created_at' => $fechaHoraActual,
                'updated_at' => $fechaHoraActual,
            ]);

            $asistencia->save();

            return response()->json([
                'success' => true,
                'data' => [
                    'nombre' => $empleado->nombre . ' ' .$empleado->apellidos,
                    'codigo_empleado' => $empleado->codigo_empleado,
                    'departamento' => $empleado->departamento->departamento,
                    'id' => $asistencia->id,
                    'empleado_id' => $asistencia->empleado_id,
                    'dia_semana' => $asistencia->dia_semana,
                    'fecha' => $asistencia->fecha = Carbon::parse($asistencia->fecha)->format('Y-m-d'),
                    'hora_entrada' => Carbon::parse($asistencia->hora_entrada)->format('h:i A'),
                    'hora_salida' => ($asistencia->hora_salida) ? Carbon::parse($asistencia->hora_salida)->format('h:i A') : null,
                    'hora_descanso_inicio' => ($asistencia->hora_descanso_inicio) ? Carbon::parse($asistencia->hora_descanso_inicio)->format('h:i A') : null,
                    'hora_descanso_fin' => ($asistencia->hora_descanso_fin) ? Carbon::parse($asistencia->hora_descanso_fin)->format('h:i A') : null,
                    'hora_extra' => $asistencia->hora_extra,
                    'horas_trabajas' => $asistencia->horas_trabajas,
                    'estado' => $asistencia->estado,
                    'descripcion' => $asistencia->descripcion,
                    'created_at' => $asistencia->created_at,
                    'updated_at' => $asistencia->updated_at,
                ],
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación. Asegúrate de enviar los datos correctamente.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al guardar la asistencia.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
    public function update(Request $request, $id)
{
    try {
        // Find the record to update
        $asistencia = Asistencia::findOrFail($id);

        // Convert the time to 12-hour format before updating the model
        // if ($request->has('hora_salida')) {
        //     $request->merge([
        //         'hora_salida' => Carbon::createFromFormat('h:i A', $request->hora_salida)->format('H:i')
        //     ]);
        // }


         if ($request->has('hora_salida')) {
            if ($request->hora_salida === null) {
                // Reiniciar la hora de salida y dejarla en null
                $asistencia->hora_salida = null;
            } else {
                $request->merge([
                    'hora_salida' => Carbon::createFromFormat('h:i A', $request->hora_salida)->format('H:i')
                ]);
            }
        }
        $empleado = Empleado::findOrFail($asistencia->empleado_id);
        // Update only the fields that are sent in the request
        $asistencia->fill($request->only([
            'fecha',
            'dia_semana',
            'descripcion',
            'departamento',
            'codigo_empleado',
            'nombre',
            'hora_entrada',
            'hora_salida',
            'estado',
            'horas_trabajas',
            'hora_extra',
            'descripcion' 
        ]));
        // Save the changes
        $asistencia->save();

        // Return a success response
        return response()->json([
            'data' => [
                'id' => $asistencia->id,
                'nombre' => $empleado->nombre . ' ' .$empleado->apellidos,
                'codigo_empleado' => $empleado->codigo_empleado,
                'departamento' => $empleado->departamento->departamento,
                'empleado_id' => $asistencia->empleado_id,
                'dia_semana' => $asistencia->dia_semana,
                'fecha' => $asistencia->fecha = Carbon::parse($asistencia->fecha)->format('Y-m-d'),
                'hora_entrada' => Carbon::parse($asistencia->hora_entrada)->format('h:i A'),
                'hora_salida' => ($asistencia->hora_salida) ? Carbon::parse($asistencia->hora_salida)->format('h:i A') : null,
                'estado' => $asistencia->estado,
                'horas_trabajas' => $asistencia->horas_trabajas,
                'hora_extra' => $asistencia->hora_extra,
                'descripcion' => $asistencia->descripcion,
                'hora_descanso_inicio' => Carbon::parse($asistencia->hora_descanso_inicio)->format('h:i A'),
                'hora_descanso_fin' => Carbon::parse($asistencia->hora_descanso_fin)->format('h:i A'),
                // ...
            ],
        ], Response::HTTP_OK);
    } catch (\Exception $e) {
        echo($e);
        // In case of error, respond with an error message and a 500 error code (Internal Server Error)
        return response()->json(['message' => 'Ha ocurrido un error al actualizar la asistencia'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

}

