@props([
    'botonHeader' => '',
    'botonFooter' => '',
    'contactos' => false,
    'cliente' => null, // Asegúrate de pasar esta variable
])

<x-sistema.card class="p-4 m-2 mb-2 mx-0" x-data="contactoForm({{ json_encode($contactos) }}, {{ $cliente->id ?? 'null' }})">
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

            <div class="col-md-4"><input type="text" class="form-control" placeholder="DNI*" x-model="form.dni"></div>
            <div class="col-md-4"><input type="text" class="form-control" placeholder="Nombre*" x-model="form.nombre"></div>
            <div class="col-md-4">
                <select class="form-control" x-model="form.cargo">
                    <option value="">Cargo*</option>
                    <option value="Gerente General">Gerente General</option>
                    <option value="Apoderado">Apoderado</option>
                    <option value="Administrador (a)">Administrador (a)</option>
                    <option value="Empleado (a)">Empleado (a)</option>
                </select>
            </div>
            <div class="col-md-6"><input type="email" class="form-control" placeholder="Correo*" x-model="form.correo"></div>
            <div class="col-md-6"><input type="text" class="form-control" placeholder="Celular*" x-model="form.celular"></div>
            <div class="col-12 text-end">
                <button type="button" class="btn btn-primary" @click="guardarNuevoContacto()">
                    <i class="fas fa-save me-1"></i> Guardar
                </button>
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
            <tbody class="text-sm">
                <template x-for="(c, index) in lista" :key="c.id">
                    <tr x-data="{
                        editando: false,
                        contacto: { ...c },
                        original: {},
                        editar() {
                            this.original = JSON.parse(JSON.stringify(this.contacto));
                            this.editando = true;
                        },
                        cancelar() {
                            this.contacto = JSON.parse(JSON.stringify(this.original));
                            this.editando = false;
                        },
                        guardar() {
                            lista[index] = JSON.parse(JSON.stringify(this.contacto));
                            this.editando = false;
                        }
                    }">
                        <td class="text-center align-middle">
                            <template x-if="editando">
                                <input type="text" class="form-control form-control-sm text-center" x-model="contacto.dni">
                            </template>
                            <template x-if="!editando"><span x-text="contacto.dni"></span></template>
                        </td>
                        <td class="text-center align-middle">
                            <template x-if="editando">
                                <input type="text" class="form-control form-control-sm text-center" x-model="contacto.nombre">
                            </template>
                            <template x-if="!editando"><span x-text="contacto.nombre"></span></template>
                        </td>
                        <td class="text-center align-middle">
                            <template x-if="editando">
                                <input type="text" class="form-control form-control-sm text-center" x-model="contacto.celular">
                            </template>
                            <template x-if="!editando"><span x-text="contacto.celular"></span></template>
                        </td>
                        <td class="text-center align-middle">
                            <template x-if="editando">
                                <select class="form-control form-control-sm text-center" x-model="contacto.cargo">
                                    <option value="Gerente General">Gerente General</option>
                                    <option value="Apoderado">Apoderado</option>
                                    <option value="Administrador (a)">Administrador (a)</option>
                                    <option value="Empleado (a)">Empleado (a)</option>
                                </select>
                            </template>
                            <template x-if="!editando"><span x-text="contacto.cargo"></span></template>
                        </td>
                        <td class="text-center align-middle">
                            <template x-if="editando">
                                <input type="email" class="form-control form-control-sm text-center" x-model="contacto.correo">
                            </template>
                            <template x-if="!editando"><span x-text="contacto.correo"></span></template>
                        </td>
                        <td class="text-center align-middle">
                            <template x-if="!editando">
                                <i class="fas fa-edit text-primary cursor-pointer" @click="editar()" title="Editar"></i>
                            </template>
                            <template x-if="editando">
                                <div class="d-flex gap-2 justify-content-center">
                                    <i class="fas fa-save text-success cursor-pointer" @click="guardar()" title="Guardar"></i>
                                    <i class="fas fa-times text-danger cursor-pointer" @click="cancelar()" title="Cancelar"></i>
                                </div>
                            </template>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</x-sistema.card>

{{-- Alpine.js --}}
<script>
    function contactoForm(contactosIniciales, clienteId) {
        return {
            lista: (contactosIniciales ?? []).sort((a, b) => b.id - a.id),
            form: {
                dni: '', nombre: '', celular: '', cargo: '', correo: ''
            },
            guardarNuevoContacto() {
                if (!this.form.dni || !this.form.nombre || !this.form.cargo || !this.form.correo || !this.form.celular) {
                    alert('Todos los campos son obligatorios');
                    return;
                }

                const payload = {
                    cliente_id: clienteId,
                    ...this.form
                };

                fetch('{{ route('contactos.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error al guardar contacto');
                    return response.json();
                })
                .then(data => {
                    this.lista.unshift(data); // respuesta real
                    this.form = { dni: '', nombre: '', celular: '', cargo: '', correo: '' };
                    setTimeout(() => {
                        document.querySelector('input[placeholder="DNI*"]').focus();
                    }, 100);
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
            }
        }
    }
</script>
