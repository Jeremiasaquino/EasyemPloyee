<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
            'separador_decimal' => 'required|in:, .',
        ]);

        // Crear una nueva empresa
        $empresa = Empresa::create($request->all());

        return response()->json(['message' => 'Empresa creada con éxito', 'data' => $empresa], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Obtener la empresa por su ID
        $empresa = Empresa::findOrFail($id);

        return response()->json($empresa, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Obtener la empresa por su ID
        $empresa = Empresa::findOrFail($id);

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
            'separador_decimal' => 'required',
        ]);

        // Actualizar los datos de la empresa
        $empresa->update($request->all());

        return response()->json(['message' => 'Empresa actualizada con éxito', 'data' => $empresa], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Obtener la empresa por su ID
        $empresa = Empresa::findOrFail($id);

        // Eliminar la empresa
        $empresa->delete();

        return response()->json(['message' => 'Empresa eliminada con éxito'], 200);
    }
}
