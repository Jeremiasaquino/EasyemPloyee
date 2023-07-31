<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Mostrar una lista de las empresas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtener todas las empresas
        $empresas = Empresa::all();

        return response()->json($empresas, 200);
    }

    /**
     * Almacenar una nueva empresa en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'razon_social' => 'required',
            'nombre_comercial' => 'required|unique:empresas',
            'direccion' => 'required',
            'correo_electronico' => 'required|email|unique:empresas',
            'rnc_cedula' => 'required|numeric|unique:empresas',
            'telefono' => 'required|numeric|unique:empresas',
            'provincia' => 'required',
            'municipio' => 'required',
            'sitio_web' => 'required',
            'regimen' => 'nullable|in:Régimen general,Régimen simplificado de tributación (RST),Regímenes especiales de tributación',
            'sector' => 'required',
            'numero_empleados' => 'nullable|in:1-10,11-50,51-100,101-500,500+',
            'moneda' => 'required',
            'separador_decimal' => 'nullable|in:, .',
        ]);

        // Crear una nueva empresa
        $empresa = Empresa::create($request->all());

        return response()->json(['message' => 'Empresa creada con éxito', 'data' => $empresa], 201);
    }

    /**
     * Mostrar los detalles de una empresa específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Obtener la empresa por su ID
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        return response()->json($empresa, 200);
    }

    /**
     * Actualizar los datos de una empresa existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Obtener la empresa por su ID
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        // Validar los datos de entrada
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'razon_social' => 'required',
            'nombre_comercial' => 'required|unique:empresas,nombre_comercial,' . $empresa->id,
            'direccion' => 'required',
            'correo_electronico' => 'required|email',
            'rnc_cedula' => 'required|unique:empresas,rnc_cedula,' . $empresa->id,
            'telefono' => 'required|unique:empresas,telefono,' . $empresa->id,
            'provincia' => 'required',
            'municipio' => 'required',
            'sitio_web' => 'required',
            'regimen' => 'nullable',
            'sector' => 'required',
            'numero_empleados' => 'required',
            'moneda' => 'required',
            'separador_decimal' => 'nullable',
        ]);

        // Actualizar los datos de la empresa
        $empresa->update($request->all());

        return response()->json(['message' => 'Empresa actualizada con éxito', 'data' => $empresa], 200);
    }

    /**
     * Eliminar una empresa específica de la base de datos.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Obtener la empresa por su ID
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        // Eliminar la empresa
        $empresa->delete();

        return response()->json(['message' => 'Empresa eliminada con éxito'], 200);
    }
}
