<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistorialEmpresaAnterior extends Model
{
    use HasFactory;

    protected $table = 'historial_empresa_anterior';

    protected $fillable = [
        'nombre_empresa_anterior',
        'cargo_anterior',
        'fecha_inicio_trabajo_anterior',
        'fecha_salida_trabajo_anterior',
        'motivo_salida',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}