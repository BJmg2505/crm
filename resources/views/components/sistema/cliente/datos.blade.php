@props([
    'botonHeader' => '',
    'botonFooter' => '',
    'cliente' => '',
])

@php
    $departamentos = \App\Models\Departamento::orderBy('nombre')->get();
@endphp

<input type="hidden" id="cliente_id" value="{{ $cliente->id ?? '' }}">

<x-sistema.card class="p-4 m-2 mb-2 mx-0">
    <div class="d-flex flex-row flex-wrap justify-between items-center mb-3">
        <x-sistema.titulo title="Datos Del Cliente" />
        @role('ejecutivo')
            <div class="flex flex-row gap-2">
                <button class="btn btn-primary" id="btn-editar-cliente" onclick="editarCliente()">Editar</button>
                <button class="btn btn-primary d-none" id="btn-guardar-cliente" onclick="guardarCliente()">Guardar</button>
                <button class="btn btn-secondary d-none" id="btn-cancelar-cliente" onclick="cancelarCliente()">Cancelar</button>
            </div>
        @endrole
    </div>

    <div class="row g-3" id="form-datos-cliente">
        {{-- RUC y Razón Social --}}
        <div class="col-md-4">
            <input type="text" id="ruc" name="ruc" maxlength="11" class="form-control" placeholder="RUC *"
                value="{{ $cliente->ruc ?? '' }}" disabled onchange="validarRuc(this)">
        </div>
        <div class="col-md-8">
            <input type="text" id="razon_social" name="razon_social" class="form-control" placeholder="Razón Social *"
                value="{{ $cliente->razon_social ?? '' }}" disabled>
        </div>

        {{-- Dirección Fiscal --}}
        <div class="col-12">
            <input type="text" id="ciudad" name="ciudad" class="form-control" placeholder="Dirección Fiscal *"
                value="{{ $cliente->ciudad ?? '' }}" disabled>
        </div>

        {{-- Departamento, Provincia y Distrito --}}
        <div x-data="ubigeoSelects(
                '{{ $cliente->departamento_codigo ?? '' }}',
                '{{ $cliente->provincia_codigo ?? '' }}',
                '{{ $cliente->distrito_codigo ?? '' }}',
                true
            )"
            x-init="init()"
            class="col-12 row g-3">

            <div class="col-md-4">
                <select id="departamento_codigo" x-model="departamento_codigo" @change="fetchProvincias"
                    :disabled="isReadOnly" class="form-control">
                    <option value="">Departamento *</option>
                    @foreach ($departamentos as $item)
                        <option value="{{ $item->codigo }}">{{ $item->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <select id="provincia_codigo" x-model="provincia_codigo" @change="fetchDistritos"
                    :disabled="isReadOnly" class="form-control">
                    <option value="">Provincia *</option>
                    <template x-for="prov in provincias" :key="prov.codigo">
                        <option :value="prov.codigo" x-text="prov.nombre"></option>
                    </template>
                </select>
            </div>

            <div class="col-md-4">
                <select id="distrito_codigo" x-model="distrito_codigo" :disabled="isReadOnly" class="form-control">
                    <option value="">Distrito *</option>
                    <template x-for="dist in distritos" :key="dist.codigo">
                        <option :value="dist.codigo" x-text="dist.nombre"></option>
                    </template>
                </select>
            </div>
        </div>

        {{-- Bot opcional --}}
        @role(['sistema', 'administrador'])
            <div class="col-12 form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" id="generado_bot"
                    @if ($cliente->generado_bot ?? false) checked @endif disabled>
                <label class="form-check-label ms-2" for="generado_bot">Generado por Bot</label>
            </div>
        @endrole

        <div class="col-12 text-end">
            {{ $botonFooter }}
        </div>
    </div>
</x-sistema.card>

<script>
    let datosClienteOriginales = {};

    function obtenerValoresCliente() {
        return {
            ruc: $('#ruc').val(),
            razon_social: $('#razon_social').val(),
            ciudad: $('#ciudad').val(),
            departamento_codigo: $('#departamento_codigo').val(),
            provincia_codigo: $('#provincia_codigo').val(),
            distrito_codigo: $('#distrito_codigo').val(),
        };
    }

    function establecerValoresCliente(data) {
        $('#ruc').val(data.ruc);
        $('#razon_social').val(data.razon_social);
        $('#ciudad').val(data.ciudad);
        $('#departamento_codigo').val(data.departamento_codigo).trigger('change');

        setTimeout(() => {
            $('#provincia_codigo').val(data.provincia_codigo).trigger('change');
            setTimeout(() => {
                $('#distrito_codigo').val(data.distrito_codigo);
            }, 300);
        }, 300);
    }

    function editarCliente() {
        datosClienteOriginales = obtenerValoresCliente();
        $('#form-datos-cliente :input').prop('disabled', false);
        window.dispatchEvent(new Event('enable-ubigeo'));
        $('#btn-editar-cliente').addClass('d-none');
        $('#btn-guardar-cliente, #btn-cancelar-cliente').removeClass('d-none');
    }

    function cancelarCliente() {
        establecerValoresCliente(datosClienteOriginales);
        $('#form-datos-cliente :input').prop('disabled', true);
        window.dispatchEvent(new Event('disable-ubigeo'));
        $('#btn-editar-cliente').removeClass('d-none');
        $('#btn-guardar-cliente, #btn-cancelar-cliente').addClass('d-none');
    }

    function guardarCliente() {
        const data = obtenerValoresCliente();
        const cliente_id = $('#cliente_id').val();

        // Validación simple
        if (!data.ruc || !data.razon_social || !data.ciudad || !data.departamento_codigo || !data.provincia_codigo || !data.distrito_codigo) {
            alert('Por favor, complete todos los campos obligatorios.');
            return;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/cliente-gestion/${cliente_id}`,
            method: 'PUT',
            data: {
                view: 'update-datos-cliente',
                ...data
            },
            success: function () {
                $('#form-datos-cliente :input').prop('disabled', true);
                window.dispatchEvent(new Event('disable-ubigeo'));
                $('#btn-editar-cliente').removeClass('d-none');
                $('#btn-guardar-cliente, #btn-cancelar-cliente').addClass('d-none');
            },
            error: function () {
                alert('Ocurrió un error al guardar los datos.');
            }
        });
    }

    function validarRuc(element) {
        const ruc = element.value;
        if (ruc.length !== 11) {
            alert('El RUC debe tener exactamente 11 dígitos.');
            return;
        }

        $.ajax({
            url: '{{ url("cliente-gestion/0") }}',
            method: "GET",
            data: {
                view: 'show-validar-ruc',
                ruc: ruc
            },
            success: function (result) {
                // puedes mostrar info de validez si deseas
            },
            error: function (response) {
                alert('Error al validar RUC.');
            }
        });
    }

    function ubigeoSelects(departamentoInicial = '', provinciaInicial = '', distritoInicial = '', isReadOnly = false) {
        return {
            departamento_codigo: departamentoInicial,
            provincia_codigo: provinciaInicial,
            distrito_codigo: distritoInicial,
            provincias: [],
            distritos: [],
            isReadOnly: isReadOnly,

            async fetchProvincias() {
                this.provincia_codigo = '';
                this.distrito_codigo = '';
                this.distritos = [];
                if (this.departamento_codigo) {
                    const res = await fetch(`/api/provincias/${this.departamento_codigo}`);
                    this.provincias = await res.json();
                } else {
                    this.provincias = [];
                }
            },

            async fetchDistritos() {
                this.distrito_codigo = '';
                if (this.departamento_codigo && this.provincia_codigo) {
                    const res = await fetch(`/api/distritos/${this.departamento_codigo}/${this.provincia_codigo}`);
                    this.distritos = await res.json();
                } else {
                    this.distritos = [];
                }
            },

            async init() {
                await this.fetchProvincias();
                this.provincia_codigo = '{{ $cliente->provincia_codigo ?? '' }}';
                await this.fetchDistritos();
                this.distrito_codigo = '{{ $cliente->distrito_codigo ?? '' }}';

                window.addEventListener('enable-ubigeo', () => {
                    this.isReadOnly = false;
                });
                window.addEventListener('disable-ubigeo', () => {
                    this.isReadOnly = true;
                });
            }
        };
    }
</script>
