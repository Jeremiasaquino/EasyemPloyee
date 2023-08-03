<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CargoController extends Controller
{
    /**
     * Obtener todos los cargos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cargos = Cargo::all();
        return response()->json([
            'success' => true,
            'data' => $cargos,
        ]);
    }

    /**
     * Crear un nuevo cargo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'cargo' => 'required|unique:cargo|string|max:255',
            ],
            [
                'cargo.required' => 'El cargo es obligatorio.',
                'cargo.unique' => 'Ya existe un cargo con este nombre.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un cargo con este nombre',
                'errors' => $validator->errors(),
            ], 400);
        }

        $cargo = Cargo::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cargo creado exitosamente',
            'msgDescription' => 'Cargo Registrado!',
            'data' => $cargo,
        ], 201);
    }

    /**
     * Obtener un cargo específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cargo = Cargo::find($id);

        if (!$cargo) {
            return response()->json([
                'success' => false,
                'message' => 'Cargo no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $cargo,
        ]);
    }

    /**
     * Actualizar un cargo existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $cargo = Cargo::find($id);

        if (!$cargo) {
            return response()->json([
                'success' => false,
                'message' => 'Cargo no encontrado',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'cargo' => 'required|unique:cargo,cargo,' . $id . '|string|max:255',
        ], [
            'cargo.required' => 'El cargo es obligatorio.',
            'cargo.unique' => 'Ya existe un cargo con este nombre.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación',
                'errors' => $validator->errors(),
            ], 400);
        }

        $cargo->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cargo actualizado exitosamente',
            'msgDescription' => 'Cargo Modificado!',
            'data' => $cargo,
        ]);
    }

    /**
     * Eliminar un cargo existente.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cargo = Cargo::find($id);

        if (!$cargo) {
            return response()->json([
                'success' => false,
                'message' => 'Cargo no encontrado',
            ], 404);
        }

        $cargo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cargo eliminado exitosamente',
        ]);
    }
}
