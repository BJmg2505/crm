<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'dni' => 'required|string|max:20',
            'nombre' => 'required|string|max:100',
            'celular' => 'required|string|max:20',
            'cargo' => 'required|string|max:50',
            'correo' => 'required|email|max:100',
        ]);

        $contacto = Contacto::create($validated);

        return response()->json($contacto);
    }
}
