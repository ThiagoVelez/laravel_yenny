<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;

class PersonaController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validación de los datos entrantes
        $validatedData = $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email|unique:personas,email',
            'telefono' => 'nullable|string|max:20',
        ]);

        // 2. Inserción usando Eloquent
        $persona = Persona::create($validatedData);

        // 3. Respuesta JSON estandarizada (Status 201: Created)
        return response()->json([
            'mensaje' => 'Persona registrada exitosamente',
            'data' => $persona
        ], 201);
    }
}
