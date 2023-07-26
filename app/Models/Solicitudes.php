<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Solicitudes extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'empleado_id',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'duracion',
        'motivo',
        'estado',
        'documento_apoyo',
    ];

    protected $dates = ['fecha_inicio', 'fecha_fin']; // Convertir a instancias de Carbon automáticamente

    // Relación con el modelo Empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    // Accesor para obtener la duración en días
    public function getDuracionEnDiasAttribute()
    {
        if ($this->duracion) {
            $duracion = intval($this->duracion);
            return $duracion . ' ' . Str::plural('día', $duracion);
        }

        $fechaInicio = $this->fecha_inicio;
        $fechaFin = $this->fecha_fin;

        if ($fechaInicio && $fechaFin) {
            $duracionEnDias = $fechaInicio->diffInDays($fechaFin) + 1; // Sumamos 1 para incluir el día de inicio
            return $duracionEnDias . ' ' . Str::plural('día', $duracionEnDias);
        }

        return null;
    }

    // Mutator para formatear el campo "duracion" antes de guardarlo
    public function setDuracionAttribute($value)
    {
        $this->attributes['duracion'] = intval($value);
    }
}
