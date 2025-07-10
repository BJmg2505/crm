<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\Notificacion;
use App\Models\Notificaciontipo;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $notificaciones = $this->setNotificaciones($user);

        // filtros
        $equipo_id = request('filtro_equipo_id');
        $user_id = request('filtro_user_id');
        $equipos = Equipo::all();
        if ($equipo_id) {
            $users = Equipo::find($equipo_id)->users;
        } else {
            $users = User::role('ejecutivo')->get();
        }

        return view('sistema.notificacion.index', compact(
            'notificaciones',
            'equipos',
            'users',
        ));
    }

    private function setNotificaciones($user)
    {
        // Filtros
        $filtro_equipo_id = request('filtro_equipo_id');
        $filtro_user_id = request('filtro_user_id');

        $query = Notificacion::query()
            ->with(['user.equipos', 'notificaciontipo'])
            ->orderByDesc('fecha');

        // Filtro por usuario
        if ($filtro_user_id) {
            $query->where('user_id', $filtro_user_id);
        }

        // Filtro por equipo (a través del usuario)
        if ($filtro_equipo_id) {
            $query->whereHas('user.equipos', function ($q) use ($filtro_equipo_id) {
                $q->where('equipos.id', $filtro_equipo_id);
            });
        }
        if ($user->hasRole(['sistema', 'administrador', 'gerente comercial', 'gerente comercial'])) {
            $notificaciones = $query->paginate(25);
        } elseif ($user->hasRole('supervisor')) {
            $idsEjecutivos = $user->equipo->users->pluck('id');
            $notificaciones = $query->whereIn('user_id', $idsEjecutivos)
                ->paginate(10);
        } else {
            $notificaciones = $query->where('user_id', $user->id)
                ->paginate(10);
        }
        return $notificaciones;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (request('view') == 'create') {
            $tipos = Notificaciontipo::all();
            $fecha = now()->format('Y-m-d');

            return view('sistema.notificacion.create', compact('tipos', 'fecha'));
        } elseif (request('view') == 'historial') {
            $notificaciones = Notificacion::with('user')
                    ->where('user_id', auth()->user()->id)
                    ->orderBy('fecha')
                    ->get();

            return view('sistema.notificacion.historial', compact('notificaciones'));
        } elseif (request('view') == 'pendiente') {
            $notificaciones = Helpers::NotificacionRecordatorio(auth()->user());

            return view('sistema.notificacion.historial', compact('notificaciones'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (request('view') == 'store') {
            $request->validate([
                'notificaciontipo_id' => 'required|bail',
                'asunto' => 'required|bail',
                'mensaje' => 'required|bail',
                'fecha' => 'required|bail',
                'hora' => 'required|bail',
            ],
                [
                    'notificaciontipo_id.required' => 'El "Tipo de Notificación" es obligatorio.',
                    'asunto.required' => 'El "Asunto" es obligatorio.',
                    'mensaje.required' => 'El "Mensaje" es obligatorio.',
                    'fecha.required' => 'La "Fecha" es obligatorio.',
                    'hora.required' => 'La "Hora" es obligatorio.',
                ]);
            $notificacion = new Notificacion;
            $notificacion->asunto = request('asunto');
            $notificacion->mensaje = request('mensaje');
            $notificacion->fecha = request('fecha');
            $notificacion->hora = request('hora');
            $notificacion->notificaciontipo_id = request('notificaciontipo_id');
            $notificacion->user_id = auth()->user()->id;
            $notificacion->save();
        } elseif (request('view') == 'store_from_fichacliente') {
            $request->validate([
                'notificaciontipo_id' => 'required|bail',
                'mensaje' => 'required|bail',
                'fecha' => 'required|bail',
                'hora' => 'required|bail',
            ],
                [
                    'notificaciontipo_id.required' => 'El "Tipo de Notificación" es obligatorio.',
                    'mensaje.required' => 'El "Mensaje" es obligatorio.',
                    'fecha.required' => 'La "Fecha" es obligatorio.',
                    'hora.required' => 'La "Hora" es obligatorio.',
                ]);
            $asunto = '';
            $cliente = Cliente::find(request('cliente_id'));
            if (request('notificaciontipo_id') == 2) {
                $asunto = "Cita con el Cliente: $cliente->ruc - $cliente->razon_social";
            } elseif (request('notificaciontipo_id') == 3) {
                $asunto = "Llamada al Cliente: $cliente->ruc - $cliente->razon_social";
            }
            $notificacion = new Notificacion;
            $notificacion->asunto = $asunto;
            $notificacion->mensaje = request('mensaje');
            $notificacion->fecha = request('fecha');
            $notificacion->hora = request('hora');
            $notificacion->notificaciontipo_id = request('notificaciontipo_id');
            $notificacion->user_id = auth()->user()->id;
            $notificacion->cliente_id = request('cliente_id');
            $notificacion->save();
            $data_notificacions = $cliente->notificacions()->orderBy('notificacions.id', 'desc')->limit(4)->get();
            $notificacions = [];
            foreach ($data_notificacions as $value) {
                $notificacions[] = [
                    'id' => $value->id,
                    'asunto' => $value->asunto,
                    'mensaje' => trim($value->mensaje),
                    'fecha' => now()->parse($value->fecha)->format('d-m-Y'),
                    'hora' => now()->parse($value->hora)->format(' h:i:s A'),
                    'username' => $value->user->name,
                ];
            }

            return response()->json($notificacions);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $view = request('view');
        if ($view === 'show-select-sede') {
            $sede = Sede::find($id);
            if ($sede) {
                $equipos = Equipo::where('sede_id', $sede->id)->get();
                $users = User::role('ejecutivo')->where('sede_id', $sede->id)->get();
            } else {
                $equipos = Equipo::all();
                $users = User::role('ejecutivo')->get();
            }

            return response()->json([
                'equipos' => $equipos,
                'users' => $users,
            ]);
        } elseif ($view === 'show-select-equipo') {
            $equipo = Equipo::find($id);
            $users = $equipo->users;

            return response()->json([
                'users' => $users,
            ]);
        } elseif ($view === 'show-select-user') {
            $sede_id = request('sede_id');
            if ($sede_id) {
                $users = User::role('ejecutivo')->where('sede_id', $sede_id)->get();
            } else {
                $users = User::role('ejecutivo')->get();
            }

            return response()->json([
                'users' => $users,
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $view = request('view');
        if ($view === 'edit') {
            $notificacion = Notificacion::find($id);

            return view('sistema.notificacion.edit', compact('notificacion'));
        } elseif ($view === 'delete') {
            $notificacion = Notificacion::find($id);

            return view('sistema.notificacion.delete', compact('notificacion'));
        } elseif ($view === 'gestion-evaporacion') {
            $notificacion = Notificacion::find($id);

            return view('sistema.notificacion.gestion-evaporacion', compact('notificacion'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $view = request('view');
        if ($view == 'update') {
            $request->validate([
                'asunto' => 'required|bail',
                'mensaje' => 'required|bail',
                'fecha' => 'required|bail',
                'hora' => 'required|bail',
            ],
                [
                    'asunto.required' => 'El "Asunto" es obligatorio.',
                    'mensaje.required' => 'El "Mensaje" es obligatorio.',
                    'fecha.required' => 'La "Fecha" es obligatorio.',
                    'hora.required' => 'La "Hora" es obligatorio.',
                ]);
            $notificacion = Notificacion::find($id);
            $notificacion->asunto = request('asunto');
            $notificacion->mensaje = request('mensaje');
            $notificacion->fecha = request('fecha');
            $notificacion->hora = request('hora');
            $notificacion->notificacion = 0;
            $notificacion->atendido = filter_var(request('atendido'), FILTER_VALIDATE_BOOLEAN);
            $notificacion->save();
        } elseif ($view === 'update-gestion-evaporacion') {
            $request->validate(
                [
                    'comentario_gestion' => 'required',
                ],
                [
                    'comentario_gestion.required' => 'El "comentario" es obligatorio.',
                ]
            );
            $notificacion = Notificacion::find($id);
            $notificacion->comentario_gestion = request('comentario_gestion');
            $notificacion->save();

            // Establecer el mensaje de éxito en la sesión
            session()->flash('success', 'Gestión de evaporación creado correctamente');

            return response()->json(['redirect' => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (request('view') == 'destroy') {
            $notificacion = Notificacion::find($id);
            $notificacion->delete();
        }
    }
}
