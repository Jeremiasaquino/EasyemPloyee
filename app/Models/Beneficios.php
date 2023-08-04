<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Beneficios extends Model
{
    use HasFactory;

    protected $table = 'beneficios';

    protected $fillable = [
        'empleado_id',
        'descripcion',
        'monto',
    ];


    // Relación con el modelo "Empleado"
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}
