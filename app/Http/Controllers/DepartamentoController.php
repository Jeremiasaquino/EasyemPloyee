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
            'departamento' => 'required|unique:departamento|string|max:255',
            //'user_id' => 'nullable|exists:users,id',
        ], [
            'departamento.required' => 'El campo Nombre es obligatorio.',
            'departamento.unique' => 'Ya existe un departamento con este nombre.',
        ]);

        $departamento = Departamento::create([
            'departamento' => $request->departamento,
            //'user_id' => $request->user_id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $departamento,
            'message' => 'Departamento creado exitosamente.',
            'msgDescription' => 'Departamento Registrado!',
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
            'departamento' => 'required|unique:departamento,departamento,' . $id . '|string|max:255',
          //  'user_id' => 'nullable|exists:users,id',
        ], [
            'departamento.required' => 'El campo Nombre es obligatorio.',
            'departamento.unique' => 'Ya existe un departamento con este nombre.',
         //   'user_id.required' => 'El campo user_id es obligatorio.',
           // 'user_id.exists' => 'El user_id proporcionado no existe.',
        ]);

        $departamento->update([
            'departamento' => $request->departamento,
            //'user_id' => $request->user_id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $departamento,
            'message' => 'Departamento actualizado exitosamente.',
            'msgDescription' => 'Departamento Modificado!',
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
    $deleted = $departamento->delete();
    echo($deleted);
    
    if (!$deleted) {
        // El departamento no fue eliminado
        return response()->json([
            'success' => false,
            'message' => 'No se puede eliminar el departamento porque hay empleados asignados a él.',
        ], 404);
    }
    else{
        return response()->json([
            'success' => true,
            'message' => 'Departamento eliminado exitosamente.',
        ],201);
    }
    
}
}
