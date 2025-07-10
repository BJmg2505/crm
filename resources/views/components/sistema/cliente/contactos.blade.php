@props([
    'botonHeader' => '',
    'botonFooter' => '',
    'contactos' => false,
    'cliente' => null, // Asegúrate de pasar esta variable
])

<x-sistema.card class="p-4 m-2 mb-2 mx-0" x-data="contactoForm()">
    {{-- Título y botón --}}
    <div class="d-flex flex-row flex-wrap justify-between items-center mb-3">
        <x-sistema.titulo title="Contactos" />
        <div class="d-flex gap-2">
            {{ $botonHeader }}
        </div>
    </div>

    {{-- Formulario --}}
    @role('ejecutivo')
        <div class="row g-3 mb-3 px-2 py-3 rounded bg-gray-100">
            <style>
                .form-control::placeholder { color: #e67e22; opacity: 1; }
                select.form-control option:first-child { color: #e67e22; }
            </style>

            <input class="form-control" type="hidden" id="contacto_id" name="contacto_id" x-model="form.contacto_id">
            <div class="col-md-4">
                <input type="text"
                    name="dni"
                    id="dni"
                    class="form-control"
                    placeholder="DNI*"
                    x-model="form.dni">
            </div>
            <div class="col-md-4">
                <input type="text"
                    name="nombre"
                    id="nombre"
                    class="form-control"
                    placeholder="Nombre*"
                    x-model="form.nombre">
            </div>
            <div class="col-md-4">
                <select class="form-control"
                    name="cargo"
                    id="cargo"
                    x-model="form.cargo">
                    <option value="">Cargo*</option>
                    <option value="Gerente General">Gerente General</option>
                    <option value="Apoderado">Apoderado</option>
                    <option value="Administrador (a)">Administrador (a)</option>
                    <option value="Empleado (a)">Empleado (a)</option>
                </select>
            </div>
            <div class="col-md-6">
                <input type="email"
                    name="correo"
                    id="correo"
                    class="form-control"
                    placeholder="Correo*"
                    x-model="form.correo">
            </div>
            <div class="col-md-6">
                <input type="text"
                    name="celular"
                    id="celular"
                    class="form-control"
                    placeholder="Celular*"
                    x-model="form.celular">
            </div>
            <div class="col-12 text-end">
                {{ $botonFooter }}
            </div>
        </div>
    @endrole

    {{-- Tabla dinámica --}}
    <div class="table-responsive">
        <table class="table table-sm table-hover bg-white shadow-sm rounded border">
            <thead class="bg-light text-sm text-center align-middle">
                <tr>
                    <th style="color: #ff7700;">DNI</th>
                    <th style="color: #ff7700;">Nombre</th>
                    <th style="color: #ff7700;">Celular</th>
                    <th style="color: #ff7700;">Cargo</th>
                    <th style="color: #ff7700;">Correo</th>
                    <th style="color: #ff7700;">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm" id="contactos">
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
</x-sistema.card>
<script>
    function saveContacto() {
        const dialog = document.querySelector("#dialog");
        dialog.querySelectorAll('.is-invalid, .invalid-feedback').forEach(element => {
            element.classList.contains('is-invalid') ? element.classList.remove('is-invalid') : element
                .remove();
        });
        let cliente_id = $('#cliente_id').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: `cliente-gestion/${cliente_id}`,
            method: "PUT",
            data: {
                view: 'update-contacto',
                contacto_id: $('#contacto_id').val(),
                dni: $('#dni').val(),
                nombre: $('#nombre').val(),
                celular: $('#celular').val(),
                cargo: $('#cargo').val(),
                correo: $('#correo').val(),
            },
            success: function(result) {
                $('#contacto_id').val('');
                $('#nombre').val('');
                $('#dni').val('');
                $('#celular').val('');
                $('#cargo').val('');
                $('#correo').val('');
                listContactos(result);
            },
            error: function(response) {
                mostrarError(response);
            }
        });
    }
    function listContactos(contactos) {
        let html = "";
        contactos.forEach(function(contacto) {
            html += `<tr id="${contacto.id}">
                        <td class="align-middle text-uppercase text-sm">
                            <span class="text-secondary text-xs font-weight-normal">${contacto.dni}</span>
                        </td>
                        <td class="align-middle text-uppercase text-sm">
                            <span class="text-secondary text-xs font-weight-normal">${contacto.nombre}</span>
                        </td>
                        <td class="align-middle text-uppercase text-sm">
                            <span class="text-secondary text-xs font-weight-normal">${contacto.celular}</span>
                        </td>
                        <td class="align-middle text-uppercase text-sm">
                            <span class="text-secondary text-xs font-weight-normal">${contacto.cargo}</span>
                        </td>
                        <td class="align-middle text-uppercase text-sm">
                            <span class="text-secondary text-xs font-weight-normal">${contacto.correo}</span>
                        </td>
                        <td class="align-middle text-center">
                            <button class="btn btn-sm btn-primary" type="button"
                                @click="editarContacto({ 
                                    contacto_id: '${contacto.id}', 
                                    dni: '${contacto.dni}', 
                                    nombre: '${contacto.nombre}', 
                                    celular: '${contacto.celular}', 
                                    cargo: '${contacto.cargo}', 
                                    correo: '${contacto.correo}' 
                                })">
                                Editar
                            </button>
                        </td>
                    </tr>`;
        })
        $('#contactos').html(html);
    }
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
