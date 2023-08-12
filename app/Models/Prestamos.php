<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prestamos extends Model
{
    use HasFactory;
    protected $table = 'prestamos';

    protected $fillable = [
        'empleado_id',
        'fecha_prestamo',
        'monto',
        'estado',
        
    ];

    // Relación con el modelo "Empleado"
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}