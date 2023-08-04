<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deducciones extends Model
{
    use HasFactory;

    protected $table = 'deducciones';

    protected $fillable = [
        'descripcion',
        'porcentaje_empleado',
        'monto',
        'empleado_id',
    ];

    // Relación con el modelo "Empleado"
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}
