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
    // Función index para obtener los datos solicitados
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
            if ($asistencias->isEmpty()) {
                // Respuesta con código 204 (sin contenido) indicando que no hay asistencia registrada para el día actual
                return response()->json(null, Response::HTTP_NO_CONTENT);
            }

            // Formatear los datos de asistencia para la respuesta
            $data = [];
            foreach ($asistencias as $asistencia) {
                // Convertimos las horas trabajadas a formato de 8 horas y 0 minutos
                $horasTrabajadas = sprintf('%d:%02d', floor($asistencia->horas_trabajadas), ($asistencia->horas_trabajadas - floor($asistencia->horas_trabajadas)) * 60);

                $data[]  = [
                    'dia_semana' => $asistencia->dia_semana,
                    'fecha' => $asistencia->fecha,
                    'departamento' => $asistencia->empleado->departamento->name,
                    'codigo_empleado' => $asistencia->empleado->codigo_empleado,
                    'empleado' => $asistencia->empleado->getNombreCompletoAttribute(), // Asumiendo que el campo se llama 'nombre'
                    'hora_entrada' => $asistencia->hora_entrada,  // Cambiamos el formato de la hora a 'H:i A'
                    'hora_salida' => $asistencia->hora_salida,  // Cambiamos el formato de la hora a 'H:i A'
                    'estado' => $asistencia->estado,
                    'hora_trabajada' => $horasTrabajadas, // Mostramos las horas trabajadas en formato de 8 horas y 0 minutos
                ];
            }

            // Respuesta con código 200 (éxito) y los datos en formato JSON
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            echo ($e);
            // En caso de error, respondemos con un mensaje de error y un código de error 500 (Error interno del servidor)
            return response()->json(['message' => 'Ha ocurrido un error al obtener las asistencias'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Mostrar la asistencia de un empleado en una fecha específica.
     *
     * @param  int  $empleadoId
     * @param  string  $fecha
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($empleadoId, $fecha)
    {
        try {
            $fechaCarbon = Carbon::parse($fecha);

            $asistencia = Asistencia::where('empleado_id', $empleadoId)
                ->where('fecha', $fechaCarbon->toDateString())
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $asistencia,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la asistencia para el empleado en la fecha especificada.',
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
            ]);

            // Obtener el empleado por su ID
            $empleado = Empleado::findOrFail($request->empleado_id);

            // Convertir las horas a formato de 24 horas antes de guardar en el modelo Asistencia
            $horaEntrada = Carbon::createFromFormat('h:i A', $request->hora_entrada)->format('H:i');
            $horaSalida = ($request->has('hora_salida')) ? Carbon::createFromFormat('h:i A', $request->hora_salida)->format('H:i') : null;
            $horaDescansoInicio = Carbon::createFromFormat('h:i A', $request->hora_descanso_inicio)->format('H:i');
            $horaDescansoFin = Carbon::createFromFormat('h:i A', $request->hora_descanso_fin)->format('H:i');

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

            // Calcular las horas trabajadas y las horas extra
            $horasTrabajadas = ($horaSalida) ? $horaSalida->diffInHours($horaEntrada) : 0;
            $horasNormales = 8; // Ejemplo: 8 horas diarias como límite de horas normales
            $horaExtra = max(0, $horasTrabajadas - $horasNormales);

            // Obtener la fecha y hora actual
            $fechaHoraActual = Carbon::now();

            // Obtener el día de la semana actual en formato texto (por ejemplo, "lunes")
            $diaSemanaActual = $fechaHoraActual->format('l');

            // Crear la instancia de Asistencia y guardarla en la base de datos
            $asistencia = new Asistencia([
                'empleado_id' => $empleado->id,
                'dia_semana' => $diaSemanaActual,
                'fecha' =>  $fechaHoraActual,
                'hora_entrada' => $horaEntrada,
                'hora_salida' => $horaSalida,
                'hora_descanso_inicio' => $horaDescansoInicio,
                'hora_descanso_fin' => $horaDescansoFin,
                'hora_extra' => $horaExtra,
                'estado' => $estado,
                'created_at' => $fechaHoraActual,
                'updated_at' => $fechaHoraActual,
            ]);

            $asistencia->save();

            return response()->json([
                'success' => true,
                'data' => $asistencia,
            ], Response::HTTP_CREATED);
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
}
