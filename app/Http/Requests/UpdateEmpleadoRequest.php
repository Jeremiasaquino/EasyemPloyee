<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmpleadoRequest extends FormRequest
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
            'nombre' => 'required|string|sometimes',
            'apellidos' => 'required|string|sometimes',
            'fecha_nacimiento' => 'required|date_format:Y-m-d|sometimes',
            'genero' => 'required|in:Femenino,Masculino,Otro|sometimes',
            // 'edad' => 'required|integer',
            'nacionalidad' => 'required|string|sometimes',
            'estado_civil' => 'required|in:Soltero,Casado,Divorciado,Viudo|sometimes',
            'tipo_identificacion' => 'required|in:Cedula,Pasaporte|sometimes',
            'numero_identificacion' => 'sometimes|required|string|unique:empleados,numero_identificacion,' . $this->route('id'),
            'numero_seguro_social' => 'nullable|string|unique:empleados,numero_seguro_social,' . $this->route('id'),
            'telefono' => 'sometimes|required|string|unique:empleados,telefono,' . $this->route('id'),
            'email' =>  'sometimes|required|email|unique:empleados,email,' . $this->route('id'),
            'estado' => 'required|in:Activo,Inactivo,Suspendido,Vacaciones,Licencia,Terminado|sometimes',
            'foto' => 'nullable|string',
            'foto_id' => 'nullable|string',

            'cargo_id' => 'required|exists:cargo,id|sometimes',
            'departamento_id' => 'required|exists:departamento,id|sometimes',
            'horario_id' => 'required|exists:horario,id|sometimes',
            
            'calle' => 'required|string|sometimes',
            'provincia' => 'required|string|sometimes',
            'municipio' => 'required|string|sometimes',
            'sector' => 'required|string|sometimes',
            'numero_residencia' => 'required|string|sometimes',
            'referencia_ubicacion' => 'nullable|string|sometimes',

            'nombre_banco' => 'nullable|string',
            'numero_cuenta_bancaria' => [
                'sometimes',
                'nullable',
                'string',
                Rule::unique('informacion_bancaria')->ignore($this->route('id'), 'empleado_id')
            ],
            
            'tipo_cuenta' => 'nullable|in:Cuenta Corriente,Cuenta Ahorro|',

            'nombre_contacto1' => 'nullable|string',
            'telefono_contacto1' => [
                'nullable',
                'string',
                Rule::unique('contacto_emergencia')->ignore($this->route('id'), 'empleado_id')
            ],
            'direccion_contacto1' => 'nullable|string',
            'nombre_contacto2' => 'nullable|string',
            'telefono_contacto2' => [
                'nullable',
                'string',
                Rule::unique('contacto_emergencia')->ignore($this->route('id'), 'empleado_id')
            ],
            'direccion_contacto2' => 'nullable|string',
            
            'fecha_contrato' => 'required|date_format:Y-m-d|sometimes',
            'finalizacion_contrato' => 'nullable|date_format:Y-m-d',
            'tipo_contrato' => 'required|sometimes',
            'tipo_salario' => 'required|sometimes',
            'salario'=> 'required|sometimes',

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