<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactoEmergencia extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = 'contacto_emergencia';

    protected $fillable = [
        'nombre_contacto1',
        'telefono_contacto1',
        'direccion_contacto1',
        'nombre_contacto2',
        'telefono_contacto2',
        'direccion_contacto2',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
