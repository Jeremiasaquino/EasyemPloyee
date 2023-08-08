<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horario';

    protected $fillable = [
        'turno',
        'dias_semana',
        'hora_entrada',
        'hora_salida',
        'estado',
    ];

    protected $casts = [
        'dias_semana' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        // Convierte las horas de entrada y salida a objetos Carbon al guardar y cargar desde la base de datos
        static::saving(function (Horario $horario) {
            $horario->hora_entrada = Carbon::createFromFormat('h:i A', trim($horario->hora_entrada));
            $horario->hora_salida = Carbon::createFromFormat('h:i A', trim($horario->hora_salida));
        });

        static::retrieved(function (Horario $horario) {
            $horario->hora_entrada = Carbon::parse($horario->hora_entrada)->format('h:i A');
            $horario->hora_salida = Carbon::parse($horario->hora_salida)->format('h:i A');
        });
    }

    public function getHoraEntradaAttribute($value)
    {
        return Carbon::parse($value)->format('h:i A');
    }

    public function getHoraSalidaAttribute($value)
    {
        return Carbon::parse($value)->format('h:i A');
    }

    // Relaciones u otros métodos relacionados con el modelo Horario

    public function empleado()
    {
        return $this->hasMany(Empleado::class);
    }

    public function delete()
    {
        if ($this->empleado()->count() > 0) {
            // No permitir la eliminación si hay algún empleado asignado al departamento
            return false;
        }
    
        parent::delete();
        return true;
    }
    
}