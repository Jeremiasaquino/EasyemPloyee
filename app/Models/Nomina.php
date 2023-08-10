<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nomina extends Model
{
    use HasFactory;

    protected $table = 'nomina';

    protected $fillable = [
        'fecha_nomina', 'empleado_id', 'salario', 'hora_extra', 'total_beneficios',
        'total_deducciones', 'total_prestamos_adelanto', 'salario_neto',
        'metodo_pago', 'cuenta_bancaria'
    ];

    // Relación con el modelo Empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}
