<?php

namespace App\Models;

use App\Models\Respuesta;
use App\Models\Evaluacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pregunta extends Model
{
    use HasFactory;

    protected $table = 'preguntas';
    protected $fillable = [
        'evaluacion_id',
        'texto',
        'factor',
    ];

    // Definir relación con Evaluacion
    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }

    // Definir relación con Respuestas
    public function respuestas()
    {
        return $this->hasMany(Respuesta::class);
    }
}