<?php

namespace App\Models;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InformacionBancaria extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = 'informacion_bancaria';

    protected $fillable = [
        'nombre_banco',
        'numero_cuenta_bancaria',
        'tipo_cuenta',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
