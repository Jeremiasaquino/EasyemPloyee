<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class EmpleadoController extends Controller
{
    /**
     * Mostrar todos los empleados.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $empleados = Empleado::all();

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
        $empleado = Empleado::find($id);

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
     * Crear un nuevo empleado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'apellidos' => 'required',
            'email' => 'required|email|unique:empleados',
            'telefono' => 'required|unique:empleados',
            'celular' => 'required|unique:empleados',
            'fecha_nacimiento' => 'required|date_format:Y-m-d',
            'genero' => 'required|in:masculino,femenino',
            'nacionalidad' => 'required',
            'estado_civil' => 'required|in:soltero,casado,divorciado,viudo',
            'tipo_identificacion' => 'required|in:cedula,pasaporte',
            'numero_identificacion' => 'required|unique:empleados',
            'numero_seguro_social' => 'required|unique:empleados',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'required|in:activo,suspendido,inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $empleadoData = $request->all();
        $empleadoData['codigo_empleado'] = $this->generarCodigoEmpleado();
        $empleadoData['edad'] = $this->calcularEdad($empleadoData['fecha_nacimiento']);

        // Generar foto automáticamente si no se ha subido ninguna
        if (!$request->hasFile('foto')) {
            $empleadoData['foto'] = $this->generarFotoAutomatica($empleadoData['nombre'], $empleadoData['apellidos']);
        }

        $empleado = Empleado::create($empleadoData);

        return response()->json([
            'success' => true,
            'data' => $empleado,
            'message' => 'Empleado creado exitosamente'
        ], 201);
    }

    /**
     * Actualizar un empleado existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'success' => false,
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'apellidos' => 'required',
            'email' => 'required|email|unique:empleados,email,' . $empleado->id,
            'telefono' => 'required|unique:empleados,telefono,' . $empleado->id,
            'celular' => 'required|unique:empleados,celular,' . $empleado->id,
            'fecha_nacimiento' => 'required|date_format:Y-m-d',
            'genero' => 'required|in:masculino,femenino',
            'nacionalidad' => 'required',
            'estado_civil' => 'required|in:soltero,casado,divorciado,viudo',
            'tipo_identificacion' => 'required|in:cedula,pasaporte',
            'numero_identificacion' => 'required|unique:empleados,numero_identificacion,' . $empleado->id,
            'numero_seguro_social' => 'required|unique:empleados,numero_seguro_social,' . $empleado->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'required|in:activo,suspendido,inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $empleadoData = $request->all();
        $empleadoData['codigo_empleado'] = $empleado->codigo_empleado;
        $empleadoData['edad'] = $this->calcularEdad($empleadoData['fecha_nacimiento']);

        // Generar foto automáticamente si no se ha subido ninguna
        if (!$request->hasFile('foto')) {
            $empleadoData['foto'] = $this->generarFotoAutomatica($empleadoData['nombre'], $empleadoData['apellidos']);
        }

        $empleado->update($empleadoData);

        return response()->json([
            'success' => true,
            'data' => $empleado,
            'message' => 'Empleado actualizado exitosamente'
        ]);
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

    /**
     * Generar una foto automáticamente con la letra del nombre y apellido.
     *
     * @param  string  $nombre
     * @param  string  $apellidos
     * @return string
     */
    private function generarFotoAutomatica($nombre, $apellidos)
    {
        $letraNombre = strtoupper(substr($nombre, 0, 1));
        $letraApellido = strtoupper(substr($apellidos, 0, 1));

        $fileName = $letraNombre . $letraApellido . '.jpg';
        $path = public_path('storage/empleados/' . $fileName);

        // Generar una imagen con la letra del nombre y apellido
        $image = Image::canvas(200, 200, '#ccc');
        $image->text($letraNombre, 100, 100, function ($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(80);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });
        $image->text($letraApellido, 100, 150, function ($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(80);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });
        $image->save($path, 90);

        return '/storage/empleados/' . $fileName;
    }
}
