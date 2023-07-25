<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direcion';

    protected $fillable = [
        'calle',
        'provincia',
        'municipio',
        'sector',
        'numero_residencia',
        'referencia_ubicacion',
    ];


    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
