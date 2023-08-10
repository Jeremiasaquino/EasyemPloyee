<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    /**
     * Muestra una lista de todos los departamentos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departamentos = Departamento::all();

        return response()->json([
            'success' => true,
            'data' => $departamentos,
        ]);
    }

    /**
     * Almacena un nuevo departamento en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:departamento|string|max:255',
            
        ], [
            'name.required' => 'El campo Nombre es obligatorio.',
            'name.unique' => 'Ya existe un departamento con este nombre.',
          
        ]);

        $departamento = Departamento::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'data' => $departamento,
            'message' => 'Departamento creado exitosamente.',
        ]);
    }

    /**
     * Muestra los detalles de un departamento específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $departamento = Departamento::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $departamento,
        ]);
    }

    /**
     * Actualiza los datos de un departamento específico en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $departamento = Departamento::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:departamento,name,' . $id . '|string|max:255',
         
        ], [
            'name.required' => 'El campo Nombre es obligatorio.',
            'name.unique' => 'Ya existe un departamento con este nombre.',
         
        ]);

        $departamento->update([
            'name' => $request->name,
           
        ]);

        return response()->json([
            'success' => true,
            'data' => $departamento,
            'message' => 'Departamento actualizado exitosamente.',
        ]);
    }

    /**
     * Elimina un departamento específico de la base de datos.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Departamento eliminado exitosamente.',
        ]);
    }
}
