<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InformacionLarabol extends Model
{
    use HasFactory;

    protected $table = 'informacion_laboral';

    protected $fillable = [
        'fecha_contrato',
        'finalizacion_contrato',
        'tipo_contrato',
        'tipo_salario',
        'salario',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
