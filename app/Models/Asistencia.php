<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencia';
    // Atributos asignables masivamente
    protected $fillable = [
        'empleado_id',
        'dia_semana',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'hora_descanso_inicio',
        'hora_descanso_fin',
        'hora_extra',
        'horas_trabajas',
        'estado',
        'descripcion'
    ];

    // Atributos tratados como objetos de tipo Carbon (fechas y horas)
    protected $dates = [
        'fecha',
        'hora_entrada',
        'hora_salida',
        'hora_descanso_inicio',
        'hora_descanso_fin',

    ];

    // Atributos virtuales que se agregarán automáticamente a las instancias del modelo
    protected $appends = [
        // 'horas_trabajadas', // Nuevo atributo virtual

    ];

    // Relación con el modelo Empleado (asumiendo que existe un modelo Empleado)
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    // Accesor para obtener el día de la semana a partir de la fecha
    // public function getDiaSemanaAttribute()
    // {
        
    // // Creamos una instancia de Carbon a partir de la fecha recibida
    //     $fechaCarbon = \Carbon\Carbon::createFromFormat('Y-m-d', $this->fecha);
    
    //     $diaSemana = $fechaCarbon->isoFormat('dddd');
    //     return ucfirst($diaSemana);
    // }

    // Accesor para calcular las horas trabajadas (hora_salida - hora_entrada)
    // public function getHorasTrabajadasAttribute()
    // {
    //     // Verificamos que los valores de hora_entrada y hora_salida sean instancias de Carbon\Carbon
    //     if ($this->hora_entrada instanceof \Carbon\Carbon && $this->hora_salida instanceof \Carbon\Carbon) {
    //         return $this->hora_entrada->diffInHours($this->hora_salida);
    //     }

    //     // Si falta alguna de las horas o no son instancias de Carbon, retornamos 0
    //     return 0;
    // }
//     // Accesor para calcular las horas extra (horas_trabajadas - horas_normales)
//     public function getHoraExtraAttribute()
//     {
//         $horasTrabajadas = $this->horas_trabajadas;
//         $horasNormales = 8; // Ejemplo: 8 horas diarias como límite de horas normales

//         if ($horasTrabajadas > $horasNormales) {
//             return $horasTrabajadas - $horasNormales;
//         }

//         return 0;
//     }
}