@props([
    'botonHeader' => '',
    'botonFooter' => '',
    'contactos' => false,
])
<x-sistema.card class="m-2" x-data="contactoForm()">
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
                    <input class="form-control" type="hidden" id="contacto_id" name="contacto_id" x-model="form.contacto_id">
                    <div class="form-group">
                        <label for="dni" class="form-control-label">DNI:</label>
                        <input class="form-control" type="text" id="dni" name="dni" x-model="form.dni">
                    </div>
                    <div class="form-group">
                        <label for="cargo" class="form-control-label">Cargo:</label>
                        <select class="form-control" id="cargo" name="cargo" x-model="form.cargo">
                            <option value="Gerente General">Gerente General</option>
                            <option value="Apoderado">Apoderado</option>
                            <option value="Administrador (a)">Administrador (a)</option>
                            <option value="Empleado (a)">Empleado (a)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="celular" class="form-control-label">Celular:</label>
                        <input class="form-control" type="text" id="celular" name="celular" x-model="form.celular">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="nombre" class="form-control-label">Nombre:</label>
                        <input class="form-control" type="text" id="nombre" name="nombre" x-model="form.nombre">
                    </div>
                    <div class="form-group">
                        <label for="correo" class="form-control-label">Correo:</label>
                        <input class="form-control" type="text" id="correo" name="correo" x-model="form.correo">
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
                            <th></th>
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
                                <td class="align-middle text-center">
                                    <button class="btn btn-sm btn-primary" type="button"
                                        @click="editarContacto({ 
                                            contacto_id: '{{ $contacto['id'] }}', 
                                            dni: '{{ $contacto['dni'] }}', 
                                            nombre: '{{ $contacto['nombre'] }}', 
                                            celular: '{{ $contacto['celular'] }}', 
                                            cargo: '{{ $contacto['cargo'] }}', 
                                            correo: '{{ $contacto['correo'] }}' 
                                        })">
                                        Editar
                                    </button>
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
<script>
    function contactoForm() {
        return {
            form: {
                contacto_id: null,
                dni: '',
                nombre: '',
                celular: '',
                cargo: '',
                correo: '',
            },
            editarContacto(contacto) {
                this.form = { ...contacto };
            }
        };
    }
</script>
