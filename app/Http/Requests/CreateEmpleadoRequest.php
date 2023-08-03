<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmpleadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
             // Reglas de validación aquí
             'nombre' => 'required|string',
             'apellidos' => 'required|string',
             'fecha_nacimiento' => 'required|date_format:Y-m-d',
             'genero' => 'required|in:Femenino,Masculino,Otro',
            //  'edad' => 'required|integer',
             'nacionalidad' => 'required|string',
             'estado_civil' => 'required|in:Soltero,Casado,Divorciado,Viudo',
             'tipo_identificacion' => 'required|in:Cedula,Pasaporte',
             'numero_identificacion' => 'required|string|unique:empleados',
             'numero_seguro_social' => 'string|unique:empleados',
             'telefono' => 'required|string|unique:empleados',
             'email' => 'required|email|unique:empleados',
             'estado' => 'required|in:Activo,Inactivo,Suspendido,Vacaciones,Licencia,Terminado',
             'foto' => 'nullable|string',
             'foto_id' => 'nullable|string',

             'cargo_id' => 'required|exists:cargo,id',
             'departamento_id' => 'required|exists:departamento,id',
             'horario_id' => 'required|exists:horario,id',
             
             'calle' => 'required|string',
             'provincia' => 'required|string',
             'municipio' => 'required|string',
             'sector' => 'required|string',
             'numero_residencia' => 'required|string',
             'referencia_ubicacion' => 'nullable|string',

             'nombre_banco' => 'nullable|string',
             'numero_cuenta_bancaria' => 'nullable|string|unique:informacion_bancaria',
             'tipo_cuenta' => 'nullable|in:Cuenta Corriente,Cuenta Ahorro|',

             'nombre_contacto1' => 'nullable|string',
             'telefono_contacto1' => 'nullable|unique:contacto_emergencia|string',
             'direccion_contacto1' => 'nullable|string',
             'nombre_contacto2' => 'nullable|string',
             'telefono_contacto2' => 'nullable|unique:contacto_emergencia|string',
             'direccion_contacto2' => 'nullable|string',
             
             'fecha_contrato' => 'required|date_format:Y-m-d',
             'finalizacion_contrato' => 'nullable|date_format:Y-m-d',
             'tipo_contrato' => 'required|',
             'tipo_salario' => 'required|',
             'salario'=> 'required|',

             'curriculum_vitae' => 'nullable|string',
             'cedula_identidad' => 'nullable|string',
             'seguro_social' => 'nullable|string',
             'titulos_certificados' => 'nullable|string',
             'otros_documentos' => 'nullable|string',

             'nombre_empresa_anterior' => 'nullable|string',
             'cargo_anterior' => 'nullable|string',
             'fecha_inicio_trabajo_anterior' => 'nullable|date_format:Y-m-d',
             'fecha_salida_trabajo_anterior' => 'nullable|date_format:Y-m-d',
             'motivo_salida' => 'nullable|string',
        ];
    }
}
