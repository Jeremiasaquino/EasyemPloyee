<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'empleado_id',
        'fecha',
        'dias',
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
}
