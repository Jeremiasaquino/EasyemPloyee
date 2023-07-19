<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asistencia extends Model
{
    use HasFactory;


protected $table = 'asistencia';
    protected $fillable = [
        'empleado_id',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'hora_descanso_inicio',
        'hora_descanso_fin',
        'hora_extra',
        'estado',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function getDayOfWeek()
    {
        $dayOfWeek = date('l', strtotime($this->date));
        return $dayOfWeek;
    }

    public function calcularHoras()
    {
        $horaEntrada = strtotime($this->hora_entrada);
        $horaSalida = strtotime($this->hora_salida);

        $horaDescansoInicio = $horaEntrada + (6 * 60 * 60); // Agrega 6 horas a la hora de entrada
        $horaDescansoFin = $horaSalida - (1 * 60 * 60); // Resta 1 hora a la hora de salida

        $horasTrabajadas = ($horaSalida - $horaEntrada) - ($horaDescansoFin - $horaDescansoInicio);
        $horasTrabajadas = max($horasTrabajadas, 0);

        $horas = floor($horasTrabajadas / 3600);
        $minutos = floor(($horasTrabajadas % 3600) / 60);
        $segundos = $horasTrabajadas % 60;

        $horaCalculada = sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
        return $horaCalculada;
    }
}
