<?php

namespace App\Http\Controllers;

use App\Models\Tss;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TssController extends Controller
{
    public function index()
    {
        $Tss = Tss::all();

        $tssList = Tss::all();

        $tssList->transform(function ($item) {
            $item->porcentaje = $item->porcentaje . '%'; // Transformar el porcentaje
            return $item;
        });

        if ($tssList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay registros en la tabla.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $tssList,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:tss',
            'porcentaje' => 'required|numeric|min:0|max:100',

        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $Tss = Tss::create($request->all());
        return response()->json(['success' => true, 'data' => $Tss]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Tss = Tss::find($id);

        if (!$Tss) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficio no encontrado.',
            ], 404);
        }

        $Tss->porcentaje = $Tss->porcentaje . '%'; // Transformar el porcentaje
        return response()->json(['success' => true, 'data' => $Tss]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $Tss = Tss::find($id);

        if (!$Tss) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficio no encontrado.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:255|unique:tss,nombre,' . $id,
            'porcentaje' => 'required|numeric|min:0|max:100',

        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $Tss->update($request->all());

        return response()->json(['success' => true, 'data' => $Tss]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Tss = Tss::find($id);

        if (!$Tss) {
            return response()->json([
                'success' => false,
                'message' => 'TSS no encontrado.',
            ], 404);
        }

        $Tss->delete();

        return response()->json([
            'success' => true,
            'message' => 'TSS eliminado exitosamente.',
        ]);
    }
}
