<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'logo',
        'razon_social',
        'nombre_comercial',
        'direccion',
        'correo_electronico',
        'rnc_cedula',
        'telefono',
        'provincia',
        'municipio',
        'sitio_web',
        'regimen',
        'sector',
        'numero_empleados',
        'moneda',
        'separador_decimal',
    ];

    protected $casts = [
        'numero_empleados' => 'string',
        'separador_decimal' => 'string',
    ];
}
