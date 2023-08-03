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
        'fecha_nacimiento',
        'genero',
        'edad',
        'nacionalidad',
        'estado_civil',
        'tipo_identificacion',
        'numero_identificacion',
        'numero_seguro_social',
        'foto',
        'foto_id',
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
        'fecha_nacimiento' => 'date:Y-m-d',
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


    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function informacionDireccion()
    {
        return $this->hasOne(Direccion::class);
    }

    public function informacionBancaria()
    {
        return $this->hasOne(InformacionBancaria::class);
    }

    public function contactoEmergencia()
    {
        return $this->hasOne(ContactoEmergencia::class);
    }

    public function informacionLarabol()
    {
        return $this->hasOne(InformacionLarabol::class);
    }

    public function documentoRequirido()
    {
        return $this->hasOne(DocumentoRequirido::class);
    }

    public function historialEmpresaAnterior()
    {
        return $this->hasOne(HistorialEmpresaAnterior::class);
    }
    
}
