<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horario';

    protected $fillable = [
        'name',
        'dias_semana',
        'hora_entrada',
        'hora_salida',
        'estado',
    ];
    
}
