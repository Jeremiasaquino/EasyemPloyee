<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AsistenciaController extends Controller
{
    public function index()
    {
        $asistencias = Asistencia::with('empleado')->get();

        $data = [];
        foreach ($asistencias as $asistencia) {
            $dia = $asistencia->getDayOfWeek();
            $fecha = $asistencia->fecha;
            $departamento = $asistencia->empleado->departamento;
            $codigoEmpleado = $asistencia->empleado->codigo_empleado;
            $nombreEmpleado = $asistencia->empleado->nombre;
            $horaEntrada = $asistencia->hora_entrada;
            $horaSalida = $asistencia->hora_salida;
            $estado = $asistencia->estado;
            $horaCalculada = $asistencia->calcularHoras();

            $data[] = [
                'dia' => $dia,
                'fecha' => $fecha,
                'departamento' => $departamento,
                'codigo_empleado' => $codigoEmpleado,
                'empleado' => $nombreEmpleado,
                'hora_entrada' => $horaEntrada,
                'hora_salida' => $horaSalida,
                'estado' => $estado,
                'hora_trabajada' => $horaCalculada,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Registros de asistencia recuperados correctamente.',
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empleado_id' => 'required|exists:empleados,id',
            'fecha' => 'required|date',
            'hora_entrada' => 'required|date_format:H:i',
            'hora_salida' => 'required|date_format:H:i|after:hora_entrada',
            'hora_descanso_inicio' => 'required|date_format:H:i',
            'hora_descanso_fin' => 'required|date_format:H:i|after:hora_descanso_inicio',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $empleado = Empleado::find($request->empleado_id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado.',
            ], 404);
        }

        $horario = $empleado->horario;
        $horaEntradaEstablecida = $horario->hora_entrada;
        $horaSalidaEstablecida = $horario->hora_salida;

        $horaEntrada = $request->hora_entrada;
        $horaSalida = $request->hora_salida;
        $horaDescansoInicio = $request->hora_descanso_inicio;
        $horaDescansoFinal = $request->hora_descanso_final;

        $estado = 'A tiempo';
        if ($horaEntrada > $horaEntradaEstablecida) {
            $estado = 'Tarde';
        }

        $horaExtra = null;
        if ($horaSalida > $horaSalidaEstablecida) {
            $horaExtra = $this->calcularHoraExtra($horaSalidaEstablecida, $horaSalida);
        }

        $asistencia = Asistencia::create([
            'empleado_id' => $request->empleado_id,
            'fecha' => $request->fecha,
            'hora_entrada' => $horaEntrada,
            'hora_salida' => $horaSalida,
            'hora_descanso_inicio' => $horaDescansoInicio,
            'hora_descanso_fin' => $horaDescansoFinal,
            'estado' => $estado,
            'hora_extra' => $horaExtra,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro de asistencia creado correctamente.',
            'data' => $asistencia,
        ]);
    }

    private function calcularHoraExtra($horaSalidaEstablecida, $horaSalida)
    {
        $horaSalidaEstablecida = strtotime($horaSalidaEstablecida);
        $horaSalida = strtotime($horaSalida);

        $segundosHoraExtra = $horaSalida - $horaSalidaEstablecida;
        $horasHoraExtra = $segundosHoraExtra / 3600;

        return $horasHoraExtra;
    }
}
