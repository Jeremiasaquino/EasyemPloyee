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
        'empleado_id',
        'deduccion',
        'monto',
        'tipo_deduccion',
        'estado',
    ];

    // Relación con el modelo "Empleado"
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}