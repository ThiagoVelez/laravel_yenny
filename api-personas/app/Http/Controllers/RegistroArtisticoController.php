<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroArtistico;

class RegistroArtisticoController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validación de los datos entrantes
        $validatedData = $request->validate([
            'disciplina_artistica' => 'required|string|max:80',
            'nivel_artistico'      => 'nullable|string|max:40',
        ]);

        // 2. Inserción usando Eloquent
        $registro = RegistroArtistico::create($validatedData);

        // 3. Respuesta JSON estandarizada (Status 201: Created)
        return response()->json([
            'mensaje' => 'Registro artístico creado exitosamente',
            'data'    => $registro
        ], 201);
    }
}
