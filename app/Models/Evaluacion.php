<?php

namespace App\Models;

use App\Models\Empleado;
use App\Models\Pregunta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluacion extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones';

    protected $fillable = [
        'empleado_id',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
    ];

    // Definir relación con Empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    // Definir relación con Preguntas
    public function preguntas()
    {
        return $this->hasMany(Pregunta::class);
    }
}
