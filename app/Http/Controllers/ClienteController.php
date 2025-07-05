<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Etapa;
use App\Services\ClienteService;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Contacto;


class ClienteController extends Controller
{
    protected $clienteService;

    public function __construct(ClienteService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $view = request('view');
        if ($view === 'create') {
            $etapas = Etapa::all();

            return view('sistema.cliente.create', compact('etapas'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClienteRequest $request)
    {
        $view = request('view');
        if ($view === 'store') {
            if (request('dni') != '') {
                $request->validate(
                    [
                        'nombre' => 'required|bail',
                        'dni' => 'required|numeric|digits:8|bail',
                        'cargo' => 'required|bail',
                    ],
                    [
                        'nombre.required' => 'El Nombre es obligatorio.',
                        'dni.required' => 'El DNI es obligatorio.',
                        'dni.numeric' => 'El DNI debe ser numérico.',
                        'dni.digits' => 'El DNI debe tener exactamente 8 dígitos.',
                        'cargo.required' => 'El Cargo es obligatorio.',
                    ]
                );
            }
            if (request('sucursal_nombre') != '') {
                $request->validate(
                    [
                        'sucursal_nombre' => 'required|bail',
                        'sucursal_direccion' => 'required|bail',
                        'sucursal_departamento_codigo' => 'required|bail',
                        'sucursal_provincia_codigo' => 'required|bail',
                        'sucursal_distrito_codigo' => 'required|bail',
                    ],
                    [
                        'sucursal_nombre.required' => 'El Nombre es obligatorio.',
                        'sucursal_direccion.required' => 'La Dirección es obligatoria.',
                        'sucursal_departamento_codigo.required' => 'El Departamento es obligatorio.',
                        'sucursal_provincia_codigo.required' => 'La Provincia es obligatoria.',
                        'sucursal_distrito_codigo.required' => 'El Distrito es obligatorio.',
                    ]
                );
            }
            $this->clienteService->clienteStore($request);

            // Establecer el mensaje de éxito en la sesión
            session()->flash('success', 'Cliente creado correctamente');

            return response()->json(['redirect' => true]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(string $id)
    {
        $cliente = Cliente::findOrFail($id);

        // Cargar contactos ordenados (más recientes primero)
        $contactos = $cliente->contactos()->orderByDesc('created_at')->get();

        return view('sistema.cliente.edit', compact('cliente', 'contactos'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
