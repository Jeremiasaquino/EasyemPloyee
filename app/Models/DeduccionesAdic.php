<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeduccionesAdic extends Model
{
    use HasFactory;

    protected $table = 'dedduciones_adic';

    protected $fillable = [
        'empleado_id',
        'nombre',
        'monto',
        'tipo_deduccion',
        'estado',
        'fecha_inicio',
        'fecha_final',
        
    ];

    // Relación con el modelo "Empleado"
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}
