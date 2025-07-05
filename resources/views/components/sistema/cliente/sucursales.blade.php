@props([
    'botonHeader' => '',
    'botonFooter' => '',
    'sucursales' => [],
    'cliente' => null,
])

@php
    $departamentos = \App\Models\Departamento::orderBy('nombre')->get();
@endphp

<input type="hidden" id="cliente_id" value="{{ $cliente->id ?? '' }}">

<x-sistema.card class="p-4 m-2 mb-2 mx-0" x-data="sucursalForm()" x-init="init()">
    {{-- Encabezado --}}
    <div class="d-flex flex-row flex-wrap justify-between items-center mb-3">
        <x-sistema.titulo title="Sucursales" />
        @role('ejecutivo')
            <div class="flex flex-row gap-2">
                <button class="btn btn-primary" @click="nuevaSucursal()">+ Nueva Sucursal</button>
            </div>
        @endrole
    </div>

    {{-- Formulario de sucursal --}}
    <div class="row g-3 bg-gray-100 px-3 py-4 rounded mb-4" x-show="modoEdicion">
        <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Nombre de Sucursal *" x-model="form.nombre">
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Dirección *" x-model="form.direccion">
        </div>

        <div class="col-md-4">
            <select class="form-control" x-model="form.departamento_codigo" @change="fetchProvincias">
                <option value="">Departamento *</option>
                @foreach ($departamentos as $item)
                    <option value="{{ $item->codigo }}">{{ $item->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select class="form-control" x-model="form.provincia_codigo" @change="fetchDistritos">
                <option value="">Provincia *</option>
                <template x-for="prov in provincias" :key="prov.codigo">
                    <option :value="prov.codigo" x-text="prov.nombre"></option>
                </template>
            </select>
        </div>
        <div class="col-md-4">
            <select class="form-control" x-model="form.distrito_codigo">
                <option value="">Distrito *</option>
                <template x-for="dist in distritos" :key="dist.codigo">
                    <option :value="dist.codigo" x-text="dist.nombre"></option>
                </template>
            </select>
        </div>

        <div class="col-12 form-check form-switch ms-3">
            <input class="form-check-input" type="checkbox" x-model="form.facilidad_tecnica" id="fac_tecnica">
            <label class="form-check-label ms-2" for="fac_tecnica">Facilidades Técnicas</label>
        </div>

        <div class="col-12 text-end">
            <button class="btn btn-primary" @click="guardarSucursal()">Guardar</button>
            <button class="btn btn-secondary" @click="cancelarSucursal()">Cancelar</button>
        </div>
    </div>

    {{-- Tabla de sucursales --}}
    <div class="table-responsive">
        <table class="table table-sm table-hover bg-white border rounded text-sm text-center">
            <thead class="bg-light">
                <tr>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Facilidad Técnica</th>
                    <th>Ubicación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sucursales as $sucursal)
                    <tr>
                        <td>{{ $sucursal['nombre'] }}</td>
                        <td>{{ $sucursal['direccion'] }}</td>
                        <td>{{ $sucursal['facilidad_tecnica'] ? 'Sí' : 'No' }}</td>
                        <td>{{ $sucursal['departamento_nombre'] ?? '' }} - {{ $sucursal['provincia_nombre'] ?? '' }} - {{ $sucursal['distrito_nombre'] ?? '' }}</td>
                        <td>
                            <i class="fas fa-edit text-primary cursor-pointer"
                                @click="editarSucursal({{ json_encode($sucursal) }})" title="Editar"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-sistema.card>

{{-- Alpine JS --}}
<script>
    function sucursalForm() {
        return {
            modoEdicion: false,
            provincias: [],
            distritos: [],
            form: {
                id: null,
                nombre: '',
                direccion: '',
                facilidad_tecnica: false,
                departamento_codigo: '',
                provincia_codigo: '',
                distrito_codigo: ''
            },
            init() {},
            nuevaSucursal() {
                this.resetForm();
                this.modoEdicion = true;
            },
            editarSucursal(sucursal) {
                this.form = {
                    id: sucursal.id,
                    nombre: sucursal.nombre,
                    direccion: sucursal.direccion,
                    facilidad_tecnica: sucursal.facilidad_tecnica,
                    departamento_codigo: sucursal.departamento_codigo,
                    provincia_codigo: sucursal.provincia_codigo,
                    distrito_codigo: sucursal.distrito_codigo
                };
                this.modoEdicion = true;
                this.fetchProvincias().then(() => this.fetchDistritos());
            },
            cancelarSucursal() {
                this.modoEdicion = false;
                this.resetForm();
            },
            resetForm() {
                this.form = {
                    id: null,
                    nombre: '',
                    direccion: '',
                    facilidad_tecnica: false,
                    departamento_codigo: '',
                    provincia_codigo: '',
                    distrito_codigo: ''
                };
                this.provincias = [];
                this.distritos = [];
            },
            async fetchProvincias() {
                this.form.provincia_codigo = '';
                this.form.distrito_codigo = '';
                this.distritos = [];
                if (this.form.departamento_codigo) {
                    const res = await fetch(`/api/provincias/${this.form.departamento_codigo}`);
                    this.provincias = await res.json();
                }
            },
            async fetchDistritos() {
                this.form.distrito_codigo = '';
                if (this.form.departamento_codigo && this.form.provincia_codigo) {
                    const res = await fetch(`/api/distritos/${this.form.departamento_codigo}/${this.form.provincia_codigo}`);
                    this.distritos = await res.json();
                }
            },
            guardarSucursal() {
                const cliente_id = document.getElementById('cliente_id').value;

                if (!this.form.nombre || !this.form.direccion || !this.form.departamento_codigo || !this.form.provincia_codigo || !this.form.distrito_codigo) {
                    alert('Todos los campos son obligatorios');
                    return;
                }

                fetch(`/cliente-gestion/${cliente_id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        view: 'update-sucursal',
                        ...this.form
                    })
                })
                .then(res => res.json())
                .then(() => location.reload())
                .catch(() => alert('Error al guardar sucursal'));
            }
        };
    }
</script>
