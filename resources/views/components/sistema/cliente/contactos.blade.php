@props([
    'botonHeader' => '',
    'botonFooter' => '',
    'contactos' => false,
])
<x-sistema.card class="m-2">
    <div class="d-flex flex-row flex-wrap justify-content-between">
        <x-sistema.titulo title="Contactos" />
        <div class="flex flex-row gap-2">
            {{ $botonHeader }}
        </div>
    </div>
    <div class="row">
        @role('ejecutivo')
        <div class="col-12">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="dni" class="form-control-label">DNI:</label>
                        <input class="form-control" type="text" id="dni" name="dni">
                    </div>
                    <div class="form-group">
                        <label for="cargo" class="form-control-label">Cargo:</label>
                        <select class="form-control" id="cargo" name="cargo">
                            <option value="Gerente General">Gerente General</option>
                            <option value="Apoderado">Apoderado</option>
                            <option value="Administrador (a)">Administrador (a)</option>
                            <option value="Empleado (a)">Empleado (a)</option>
                        </select>
                        {{-- <input class="form-control" type="text" id="cargo" name="cargo"> --}}
                    </div>
                    <div class="form-group">
                        <label for="celular" class="form-control-label">Celular:</label>
                        <input class="form-control" type="text" id="celular" name="celular">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="nombre" class="form-control-label">Nombre:</label>
                        <input class="form-control" type="text" id="nombre" name="nombre">
                    </div>
                    <div class="form-group">
                        <label for="correo" class="form-control-label">Correo:</label>
                        <input class="form-control" type="text" id="correo" name="correo">
                    </div>
                    {{ $botonFooter }}
                </div>
            </div>
        </div>
        @endrole
        <div class="col-12">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">DNI</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nombre</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Celular</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Cargo</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Correo</th>
                        </tr>
                    </thead>
                    <tbody id="contactos">
                        @if ($contactos)
                            @foreach ($contactos as $contacto)
                            <tr id="{{ $contacto['id'] }}">
                                <td class="align-middle text-uppercase text-sm">
                                    <span class="text-secondary text-xs font-weight-normal">{{ $contacto['dni'] }}</span>
                                </td>
                                <td class="align-middle text-uppercase text-sm">
                                    <span class="text-secondary text-xs font-weight-normal">{{ $contacto['nombre'] }}</span>
                                </td>
                                <td class="align-middle text-uppercase text-sm">
                                    <span class="text-secondary text-xs font-weight-normal">{{ $contacto['celular'] }}</span>
                                </td>
                                <td class="align-middle text-uppercase text-sm">
                                    <span class="text-secondary text-xs font-weight-normal">{{ $contacto['cargo'] }}</span>
                                </td>
                                <td class="align-middle text-uppercase text-sm">
                                    <span class="text-secondary text-xs font-weight-normal">{{ $contacto['correo'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-sistema.card>
