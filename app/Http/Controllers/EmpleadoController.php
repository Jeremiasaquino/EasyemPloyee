<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use App\Http\Requests\CreateEmpleadoRequest;
use App\Http\Requests\UpdateEmpleadoRequest;

class EmpleadoController extends Controller
{
    /**
     * Mostrar todos los empleados.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // $empleados = Empleado::all();
        $empleados = Empleado::with('informacionDireccion', 'informacionBancaria', 'contactoEmergencia', 'informacionLarabol', 'documentoRequirido', 'historialEmpresaAnterior', 'departamento', 'cargo', 'horario')->get();

        if ($empleados->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No hay empleados registrados'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $empleados
        ]);
    }
    
    /**
     * Mostrar un empleado específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // $empleado = Empleado::find($id);
        $empleado = Empleado::with('informacionDireccion', 'informacionBancaria', 'contactoEmergencia', 'informacionLarabol', 'documentoRequirido', 'historialEmpresaAnterior', 'departamento', 'cargo', 'horario')->find($id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $empleado
        ]);
    }


     /**
     * Mostrar un empleado específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmpleForDepart($id)
    {
        $empleados = Empleado::with('departamento', 'cargo', 'horario')
    ->join('users', 'empleados.id', '=', 'users.empleado_id')
    ->where('empleados.departamento_id', $id)
    ->orderByRaw("CASE WHEN users.role = 'Gerente' THEN 1 ELSE 2 END")
    ->select('empleados.*')
    ->get();

        // $empleados = Empleado::with('departamento', 'cargo', 'horario')
        // ->where('departamento_id', $id)
        // ->get();    

        if (!$empleados) {
            return response()->json([
                'success' => false,
                'message' => 'Empleados no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $empleados
        ]);
    }
    
    /**
     * Crear un nuevo empleado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateEmpleadoRequest $request)
    {
        $validator = Validator::make($request->all(), $request->rules(), $request->messages());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
       
        try {
            $empleadoData = $request->all();
            $empleadoData['codigo_empleado'] = $this->generarCodigoEmpleado();
            $empleadoData['edad'] = $this->calcularEdad($empleadoData['fecha_nacimiento']);

            $empleado = Empleado::create($empleadoData);
            
            $this->createInformacionDireccion($empleado, $request);
            $this->createInformacionBancaria($empleado, $request);
            $this->createContactoEmergencia($empleado, $request);
            $this->createInformacionLarabol($empleado, $request);
            $this->createDocumentoRequirido($empleado, $request);
            $this->createHistorialEmpresaAnterior($empleado, $request);
    
            $empleado = Empleado::with('informacionDireccion', 'informacionBancaria', 'contactoEmergencia', 'informacionLarabol', 'documentoRequirido', 'historialEmpresaAnterior', 'departamento', 'cargo', 'horario')->find($empleado);
    
            return response()->json([
                'success' => true,
                'message' => 'Empleado creado exitosamente',
                'msgDescription' => 'Empleado Registrado!',
                'data' => $empleado
            ], 201);

        } catch (\Exception $e) {
            // echo($e);
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el empleado',
                'errors' => [$e->getMessage()],
                'lala' => 'leele'
            ], 500);
        }
        
    }
  
    private function createInformacionDireccion(Empleado $empleado, Request $request)
    {
        $empleado->informacionDireccion()->create([
            // Campos de InformacionDireccion
            'calle' => $request->input('calle'),
            'provincia' => $request->input('provincia'),
            'municipio' => $request->input('municipio'),
            'sector' => $request->input('sector'),
            'numero_residencia' => $request->input('numero_residencia'),
            'referencia_ubicacion' => $request->input('referencia_ubicacion'),
        ]);
    }

    private function createInformacionBancaria(Empleado $empleado, Request $request)
    {
        $empleado->informacionBancaria()->create([
            // Campos de InformacionBancaria
            'nombre_banco' => $request->input('nombre_banco'),
            'numero_cuenta_bancaria' => $request->input('numero_cuenta_bancaria'),
            'tipo_cuenta' => $request->input('tipo_cuenta'),
        ]);
    }

    private function createContactoEmergencia(Empleado $empleado, Request $request)
    {
        $empleado->contactoEmergencia()->create([
            // Campos de ContactoEmergencia
            'nombre_contacto1' => $request->input('nombre_contacto1'),
            'telefono_contacto1' => $request->input('telefono_contacto1'),
            'direccion_contacto1' => $request->input('direccion_contacto1'),
            'nombre_contacto2' => $request->input('nombre_contacto2'),
            'telefono_contacto2' => $request->input('telefono_contacto2'),
            'direccion_contacto2' => $request->input('direccion_contacto2'),
        ]);
    }

    private function createInformacionLarabol(Empleado $empleado, Request $request)
    {

        $empleado->informacionLarabol()->create([
            // Campos de InformacionLarabol
            'fecha_contrato' => $request->input('fecha_contrato'),
            'finalizacion_contrato' => $request->input('finalizacion_contrato'),
            'tipo_contrato' => $request->input('tipo_contrato'),
            'tipo_salario' => $request->input('tipo_salario'),
            'salario' => $request->input('salario'),
        ]);
    }

    private function createDocumentoRequirido(Empleado $empleado, Request $request)
    {
        $empleado->documentoRequirido()->create([
            'documentos' => $request->input('documentos'),
            'documentos_id' => $request->input('documentos_id'),
        ]);
    }

    private function createHistorialEmpresaAnterior(Empleado $empleado, Request $request)
    {
        $empleado->historialEmpresaAnterior()->create([
            // Campos de HistorialEmpresaAnterior
            'nombre_empresa_anterior' => $request->input('nombre_empresa_anterior'),
            'cargo_anterior' => $request->input('cargo_anterior'),
            'fecha_inicio_trabajo_anterior' => $request->input('fecha_inicio_trabajo_anterior'),
            'fecha_salida_trabajo_anterior' => $request->input('fecha_salida_trabajo_anterior'),
            'motivo_salida' => $request->input('motivo_salida'),
        ]);
    }
    

    /**
     * Actualizar un empleado existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateEmpleadoRequest $request, $id)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), $request->rules(), $request->messages());
            // echo($validator);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $empleadoData = $request->all();
            $empleadoData['codigo_empleado'] = $empleado->codigo_empleado;
            if (array_key_exists('fecha_nacimiento', $empleadoData)) {
                $empleadoData['edad'] = $this->calcularEdad($empleadoData['fecha_nacimiento']);
            }            
            // $empleadoData['edad'] = $this->calcularEdad($empleadoData['fecha_nacimiento']);

            $empleado->update($empleadoData);
            $this->updateInformacionDireccion($empleado, $request);
            $this->updateInformacionBancaria($empleado, $request);
            $this->updateContactoEmergencia($empleado, $request);
            $this->updateInformacionLarabol($empleado, $request);
            $this->updateDocumentoRequirido($empleado, $request);
            $this->updateHistorialEmpresaAnterior($empleado, $request);

            $empleado = Empleado::with('informacionDireccion', 'informacionBancaria', 'contactoEmergencia', 'informacionLarabol', 'documentoRequirido', 'historialEmpresaAnterior', 'departamento', 'cargo', 'horario')->find($id);

            return response()->json([
                'success' => true,
                'message' => 'Empleado actualizado exitosamente',
                'msgDescription' => 'Empleado Modificado!',
                'data' => $empleado
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el empleado',
                'errors' => [$e->getMessage()]
            ], 500);
        }
        
    }
   
    private function updateInformacionDireccion(Empleado $empleado, Request $request)
    {
        $direccion = $empleado->informacionDireccion;
        $direccion->fill($request->all());
        $direccion->save();

        // $empleado->informacionDireccion()->update([
            // Campos de InformacionDireccion para actualizar
            // 'calle' => $request->input('calle'),
            // 'provincia' => $request->input('provincia'),
            // 'municipio' => $request->input('municipio'),
            // 'sector' => $request->input('sector'),
            // 'numero_residencia' => $request->input('numero_residencia'),
            // 'referencia_ubicacion' => $request->input('referencia_ubicacion'),
        // ]);
    }

    private function updateInformacionBancaria(Empleado $empleado, Request $request)
    {
        $informacionBancaria = $empleado->informacionBancaria;
        $informacionBancaria->fill($request->all());
        $informacionBancaria->save();
        // $empleado->informacionBancaria()->update([
        //     // Campos de InformacionBancaria para actualizar
        //     'nombre_banco' => $request->input('nombre_banco'),
        //     'numero_cuenta_bancaria' => $request->input('numero_cuenta_bancaria'),
        //     'tipo_cuenta' => $request->input('tipo_cuenta'),
        // ]);
    }

    private function updateContactoEmergencia(Empleado $empleado, Request $request)
    {
        $informacionEmergencia = $empleado->ContactoEmergencia;
        $informacionEmergencia->fill($request->all());
        $informacionEmergencia->save();
        // $empleado->contactoEmergencia()->update([
        //     // Campos de ContactoEmergencia para actualizar
        //     'nombre_contacto1' => $request->input('nombre_contacto1'),
        //     'telefono_contacto1' => $request->input('telefono_contacto1'),
        //     'direccion_contacto1' => $request->input('direccion_contacto1'),
        //     'nombre_contacto2' => $request->input('nombre_contacto2'),
        //     'telefono_contacto2' => $request->input('telefono_contacto2'),
        //     'direccion_contacto2' => $request->input('direccion_contacto2'),
        // ]);
    }

    private function updateInformacionLarabol(Empleado $empleado, Request $request)
    {
        $informacionLarabol = $empleado->informacionLarabol;
        $informacionLarabol->fill($request->all());
        $informacionLarabol->save();
        // $empleado->informacionLarabol()->update([
        //     // Campos de InformacionLarabol para actualizar
        //     'fecha_contrato' => $request->input('fecha_contrato'),
        //     'finalizacion_contrato' => $request->input('finalizacion_contrato'),
        //     'tipo_contrato' => $request->input('tipo_contrato'),
        //     'tipo_salario' => $request->input('tipo_salario'),
        //     'salario' => $request->input('salario'),
        // ]);
    }

    private function updateDocumentoRequirido(Empleado $empleado, Request $request)
    {
        $informacionDocumento = $empleado->documentoRequirido;
        $informacionDocumento->fill($request->all());
        $informacionDocumento->save();

        // $empleado->documentoRequirido()->update([
        //     'documentos' => $request->input('documentos'),
        //     'documentos_id' => $request->input('documentos_id'),
        // ]);
    }

    private function updateHistorialEmpresaAnterior(Empleado $empleado, Request $request)
    {
        $informacionHistorial = $empleado->historialEmpresaAnterior;
        $informacionHistorial->fill($request->all());
        $informacionHistorial->save();
        // $empleado->historialEmpresaAnterior()->update([
        //     // Campos de HistorialEmpresaAnterior para actualizar
        //     'nombre_empresa_anterior' => $request->input('nombre_empresa_anterior'),
        //     'cargo_anterior' => $request->input('cargo_anterior'),
        //     'fecha_inicio_trabajo_anterior' => $request->input('fecha_inicio_trabajo_anterior'),
        //     'fecha_salida_trabajo_anterior' => $request->input('fecha_salida_trabajo_anterior'),
        //     'motivo_salida' => $request->input('motivo_salida'),
        // ]);
    }

    /**
     * Eliminar un empleado existente.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        $empleado->delete();

        return response()->json([
            'success' => true,
            'message' => 'Empleado eliminado exitosamente'
        ]);
    }

    /**
     * Generar un código de empleado único.
     *
     * @return string
     */
    private function generarCodigoEmpleado()
    {
        $codigo = 'EMP-';
        $codigo .= strtoupper(substr(md5(uniqid()), 0, 8));

        // Verificar si el código ya existe en la base de datos
        if (Empleado::where('codigo_empleado', $codigo)->exists()) {
            return $this->generarCodigoEmpleado(); // Generar un nuevo código si ya existe
        }

        return $codigo;
    }

    /**
     * Calcular la edad a partir de la fecha de nacimiento.
     *
     * @param  string  $fechaNacimiento
     * @return int|null
     */
    private function calcularEdad($fechaNacimiento)
    {
        $fechaActual = new \DateTime();
        $fechaNacimiento = new \DateTime($fechaNacimiento);
        $edad = $fechaNacimiento->diff($fechaActual)->y;

        return $edad;
    }

    // /**
    //  * Generar una foto automáticamente con la letra del nombre y apellido.
    //  *
    //  * @param  string  $nombre
    //  * @param  string  $apellidos
    //  * @return string
    //  */
    // private function generarFotoAutomatica($nombre, $apellidos)
    // {
    //     $letraNombre = strtoupper(substr($nombre, 0, 1));
    //     $letraApellido = strtoupper(substr($apellidos, 0, 1));

    //     $fileName = $letraNombre . $letraApellido . '.jpg';
    //     $path = public_path('storage/empleados/' . $fileName);

    //     // Generar una imagen con la letra del nombre y apellido
    //     $image = Image::canvas(200, 200, '#ccc');
    //     $image->text($letraNombre, 100, 100, function ($font) {
    //         $font->file(public_path('fonts/arial.ttf'));
    //         $font->size(80);
    //         $font->color('#ffffff');
    //         $font->align('center');
    //         $font->valign('middle');
    //     });
    //     $image->text($letraApellido, 100, 150, function ($font) {
    //         $font->file(public_path('fonts/arial.ttf'));
    //         $font->size(80);
    //         $font->color('#ffffff');
    //         $font->align('center');
    //         $font->valign('middle');
    //     });
    //     $image->save($path, 90);

    //     return '/storage/empleados/' . $fileName;
    // }
}
