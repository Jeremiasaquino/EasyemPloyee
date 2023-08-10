<?php

namespace App\Models;

use App\Models\Pregunta;
use App\Models\Evaluacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Respuesta extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluacion_id',
        'pregunta_id',
        'puntuacion',
        'comentario',
    ];

    // Definir relación con Evaluacion
    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }

    // Definir relación con Pregunta
    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class);
    }
}
