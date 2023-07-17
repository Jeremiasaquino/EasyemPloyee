<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    /**
     * Los atributos asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'codigo_empleado',
        'nombre',
        'apellidos',
        'email',
        'telefono',
        'celular',
        'fecha_nacimiento',
        'genero',
        'edad',
        'nacionalidad',
        'estado_civil',
        'tipo_identificacion',
        'numero_identificacion',
        'numero_seguro_social',
        'foto',
        'estado',
        'cargo_id',
        'horario_id',
        'departamento_id',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'edad' => 'integer',
        'estado' => 'string',
    ];

    /**
     * Obtener el estado completo del empleado.
     *
     * @return string
     */
    public function getEstadoCompletoAttribute()
    {
        switch ($this->estado) {
            case 'activo':
                return 'Activo';
            case 'suspendido':
                return 'Suspendido';
            case 'inactivo':
                return 'Inactivo';
            default:
                return 'Desconocido';
        }
    }

    /**
     * Obtener el nombre completo del empleado.
     *
     * @return string
     */
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellidos;
    }
    
}
