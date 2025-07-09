@props([
    'botonHeader' => '',
    'botonFooter' => '',
    'sucursales' => false,
])
@php
    $departamentos =  \App\Models\Departamento::orderBy('nombre')->get();
@endphp
<x-sistema.card class="m-2" x-data="sucursalForm()" x-ref="sucursalComponente" x-init="init()">
    <div class="d-flex flex-row flex-wrap justify-content-between">
        <x-sistema.titulo title="Sucursales" />
        <div class="flex flex-row gap-2">
            {{ $botonHeader }}
        </div>
    </div>
    <div class="row">
        @role('ejecutivo')
        <div class="col-12">
            <div class="row">
                <div class="col-6">
                    <input class="form-control"
                        type="hidden"
                        id="sucursal_id"
                        name="sucursal_id"
                        x-model="form.sucursal_id">
                    <div class="form-group">
                        <input class="form-control"
                            type="text"
                            id="sucursal_nombre"
                            name="sucursal_nombre"
                            placeholder="Nombre de Sucursal *"
                            x-model="form.sucursal_nombre">
                    </div>
                    <div class="form-group">
                        <input class="form-control"
                            type="text"
                            id="sucursal_direccion"
                            name="sucursal_direccion"
                            placeholder="Dirección *"
                            x-model="form.sucursal_direccion">
                    </div>
                    <div class="form-check form-switch">
                        <label class="form-check-label" for="sucursal_facilidad_tecnica">Facilidades técnicas</label>
                        <input class="form-check-input"
                            type="checkbox"
                            id="sucursal_facilidad_tecnica"
                            name="sucursal_facilidad_tecnica"
                            x-model="form.sucursal_facilidad_tecnica"
                            :checked="form.sucursal_facilidad_tecnica == true">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <select class="form-control"
                            id="sucursal_departamento_codigo"
                            x-model="form.sucursal_departamento_codigo"
                            @change="fetchProvinciasSucursal">
                            <option>Departamento *</option>
                            @foreach ($departamentos as $item)
                                <option value="{{ $item->codigo }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control"
                            id="sucursal_provincia_codigo"
                            x-model="form.sucursal_provincia_codigo"
                            @change="fetchDistritosSucursal">
                            <option>Provincia *</option>
                            <template x-for="prov in sucursal_provincias" :key="prov.codigo">
                                <option :value="prov.codigo" x-text="prov.nombre"></option>
                            </template>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control"
                            id="sucursal_distrito_codigo"
                            x-model="form.sucursal_distrito_codigo">
                            <option>Distrito *</option>
                            <template x-for="dist in sucursal_distritos" :key="dist.codigo">
                                <option :value="dist.codigo" x-text="dist.nombre"></option>
                            </template>
                        </select>
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
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nombre</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Direccion</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Facilidad Técnica</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ubicación</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="sucursales">
                        @if ($sucursales)
                            @foreach ($sucursales as $value)
                            <tr id="{{ $value['id'] }}">
                                <td class="align-middle text-uppercase text-sm">
                                    <span class="text-secondary text-xs font-weight-normal">{{ $value['nombre'] }}</span>
                                </td>
                                <td class="align-middle text-uppercase text-sm">
                                    <span class="text-secondary text-xs font-weight-normal">{{ $value['direccion'] }}</span>
                                </td>
                                <td class="align-middle text-uppercase text-sm">
                                    <span class="text-secondary text-xs font-weight-normal">{{ $value['facilidad_tecnica'] == true ? 'SI' : 'NO' }}</span>
                                </td>
                                <td class="align-middle text-uppercase text-sm">
                                    <span class="text-secondary text-xs font-weight-normal">{{ $value['ubigeo'] }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <button class="btn btn-sm btn-primary" type="button"
                                        @click="editarSucursal({ 
                                            sucursal_id: '{{ $value['id'] }}',
                                            sucursal_nombre: '{{ $value['nombre'] }}',
                                            sucursal_direccion: '{{ $value['direccion'] }}',
                                            sucursal_facilidad_tecnica: '{{ $value['facilidad_tecnica'] }}',
                                            sucursal_departamento_codigo: '{{ $value['departamento_codigo'] }}',
                                            sucursal_provincia_codigo: '{{ $value['provincia_codigo'] }}',
                                            sucursal_distrito_codigo: '{{ $value['distrito_codigo'] }}',
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
    function sucursalForm() {
        return {
            form: {
                sucursal_id: null,
                sucursal_nombre: '',
                sucursal_direccion: '',
                sucursal_facilidad_tecnica: false,
                sucursal_departamento_codigo: '',
                sucursal_provincia_codigo: '',
                sucursal_distrito_codigo: '',
            },
            sucursal_provincias: [],
            sucursal_distritos: [],
            resetForm() {
                this.form = {
                    sucursal_id: null,
                    sucursal_nombre: '',
                    sucursal_direccion: '',
                    sucursal_departamento_codigo: '',
                    sucursal_provincia_codigo: '',
                    sucursal_distrito_codigo: '',
                    sucursal_facilidad_tecnica: ''
                };
                this.sucursal_provincias = [];
                this.sucursal_distritos = [];
            },
            async fetchProvinciasSucursal() {
                this.form.sucursal_provincia_codigo = '';
                this.form.sucursal_distrito_codigo = '';
                this.sucursal_distritos = [];
                if (this.form.sucursal_departamento_codigo) {
                    const res = await fetch(`/api/provincias/${this.form.sucursal_departamento_codigo}`);
                    this.sucursal_provincias = await res.json();
                } else {
                    this.sucursal_provincias = [];
                }
            },
            async fetchDistritosSucursal() {
                this.form.sucursal_distrito_codigo = '';
                if (this.form.sucursal_departamento_codigo && this.form.sucursal_provincia_codigo) {
                    const res = await fetch(`/api/distritos/${this.form.sucursal_departamento_codigo}/${this.form.sucursal_provincia_codigo}`);
                    this.sucursal_distritos = await res.json();
                } else {
                    this.sucursal_distritos = [];
                }
            },
            async editarSucursal(sucursal) {
                this.form.sucursal_departamento_codigo = sucursal.sucursal_departamento_codigo;
                this.form.sucursal_provincia_codigo = '';
                this.form.sucursal_distrito_codigo = '';

                await this.fetchProvinciasSucursal();

                this.form.sucursal_provincia_codigo = sucursal.sucursal_provincia_codigo;

                await this.fetchDistritosSucursal();

                // Ahora completamos los demás campos del formulario
                this.form.sucursal_id = sucursal.sucursal_id;
                this.form.sucursal_nombre = sucursal.sucursal_nombre;
                this.form.sucursal_direccion = sucursal.sucursal_direccion;
                this.form.sucursal_distrito_codigo = sucursal.sucursal_distrito_codigo;
                this.form.sucursal_facilidad_tecnica = sucursal.sucursal_facilidad_tecnica;
            },
            async saveSucursal() {
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
                        view: 'update-sucursal',
                        sucursal_id: $('#sucursal_id').val(),
                        sucursal_nombre: $('#sucursal_nombre').val(),
                        sucursal_direccion: $('#sucursal_direccion').val(),
                        sucursal_facilidad_tecnica: $('#sucursal_facilidad_tecnica').is(':checked'),
                        sucursal_departamento_codigo: $('#sucursal_departamento_codigo').val(),
                        sucursal_provincia_codigo: $('#sucursal_provincia_codigo').val(),
                        sucursal_distrito_codigo: $('#sucursal_distrito_codigo').val(),
                    },
                    success: function(result) {
                        let alpineComponent = Alpine.$data(document.querySelector('[x-ref="sucursalComponente"]'));
                        alpineComponent.resetForm();
                        alpineComponent.listSucursales(result);
                    },
                    error: function(response) {
                        mostrarError(response);
                    }
                });
            },
            listSucursales(sucursales) {
                let html = "";
                sucursales.forEach(function(sucursal) {
                    html += `<tr id="${sucursal.id}">
                                <td class="align-middle text-uppercase text-sm">
                                    <span class="text-secondary text-xs font-weight-normal">${sucursal.nombre}</span>
                                </td>
                                <td class="align-middle text-uppercase text-sm">
                                    <span class="text-secondary text-xs font-weight-normal">${sucursal.direccion}</span>
                                </td>
                                <td class="align-middle text-uppercase text-sm">
                                    <span class="text-secondary text-xs font-weight-normal">${sucursal.facilidad_tecnica ? 'SI' : 'NO'}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <button class="btn btn-sm btn-primary" type="button"
                                        @click="editarSucursal({
                                            sucursal_id: '${sucursal.id}',
                                            sucursal_nombre: '${sucursal.nombre}',
                                            sucursal_direccion: '${sucursal.direccion}',
                                            sucursal_facilidad_tecnica: '${sucursal.facilidad_tecnica}',
                                            sucursal_departamento_codigo: '${sucursal.departamento_codigo}',
                                            sucursal_provincia_codigo: '${sucursal.provincia_codigo}',
                                            sucursal_distrito_codigo: '${sucursal.distrito_codigo}',
                                        })">
                                        Editar
                                    </button>
                                </td>
                            </tr>`;
                })
                $('#sucursales').html(html);
            },
            async init() {
                if (this.form.sucursal_departamento_codigo) {
                    await this.fetchProvinciasSucursal();
                    if (this.form.sucursal_provincia_codigo) {
                        await this.fetchDistritosSucursal();
                    }
                }
            }
        };
    }
</script>