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
        <div class="flex flex-row gap-2">
            {{ $botonHeader }}
        </div>
    </div>

    <div class="row g-3" id="form-datos-cliente">
        <div class="col-md-3">
            <select id="tipo_documento" name="tipo_documento" class="form-select" onchange="mostrarCampos()" disabled>
                <option value="">-- Tipo de documento --</option>
                <option value="dni" {{ old('tipo_documento', $cliente->tipo_documento ?? '') == 'dni' ? 'selected' : '' }}>DNI</option>
                <option value="ruc" {{ old('tipo_documento', $cliente->tipo_documento ?? '') == 'ruc' ? 'selected' : '' }}>RUC</option>
            </select>
        </div>
        {{-- RUC y Razón Social --}}
        <div class="col-12 row g-3" id="campos-ruc" style="display: none;">
            <div class="col-md-4">
                <input type="text"
                    id="ruc"
                    name="ruc"
                    maxlength="11"
                    class="form-control"
                    placeholder="RUC *"
                    value="{{ $cliente->ruc ?? '' }}"
                    onchange="validarRuc(this)"
                    disabled>
            </div>
            <div class="col-md-8">
                <input type="text"
                    id="razon_social"
                    name="razon_social"
                    class="form-control"
                    placeholder="Razón Social *"
                    value="{{ $cliente->razon_social ?? '' }}"
                    disabled>
            </div>
        </div>
        {{-- DNI --}}
        <div class="col-12 row g-3" id="campos-dni" style="display: none;">
            <div class="col-md-3">
                <input type="text"
                    id="dni_cliente"
                    name="dni_cliente"
                    maxlength="8"
                    class="form-control"
                    placeholder="DNI *"
                    value="{{ $cliente->dni_cliente ?? '' }}"
                    disabled>
            </div>
            <div class="col-md-3">
                <input type="text"
                    id="nombre_cliente"
                    name="nombre_cliente"
                    class="form-control"
                    placeholder="Nombres *"
                    value="{{ $cliente->nombre_cliente ?? '' }}"
                    disabled>
            </div>
            <div class="col-md-3">
                <input type="text"
                    id="apellido_paterno_cliente"
                    name="apellido_paterno_cliente"
                    class="form-control"
                    placeholder="Apellido Paterno *"
                    value="{{ $cliente->apellido_paterno_cliente ?? '' }}"
                    disabled>
            </div>
            <div class="col-md-3">
                <input type="text"
                    id="apellido_materno_cliente"
                    name="apellido_materno_cliente"
                    class="form-control"
                    placeholder="Apellido Materno"
                    value="{{ $cliente->apellido_materno_cliente ?? '' }}"
                    disabled>
            </div>
        </div>

        <div x-data="telefonoManager({{ $cliente->celular_cliente ?? '[]' }})">
            <div class="col-12 row g-3">
                <div class="col-md-8">
                    <input type="text"
                        id="correo_cliente"
                        name="correo_cliente"
                        class="form-control"
                        placeholder="Correo Electrónico *"
                        value="{{ $cliente->correo_cliente ?? '' }}"
                        disabled>
                </div>
                <div class="col-md-3">
                    <input type="text"
                        maxlength="9"
                        x-model="nuevo"
                        class="form-control"
                        placeholder="Celular *">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-warning" @click="agregar">+</button>
                </div>
            </div>
            <div class="col-12 row g-3 mt-3">
                <template x-for="(cel, index) in celulares" :key="index">
                    <div class="col-12 row g-2 celular-item">
                        <div class="col-md-3">
                            <input type="text"
                                class="form-control"
                                x-model="celulares[index]"
                                readonly>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger" @click="eliminar(index)">x</button>
                        </div>
                    </div>
                </template>
            </div>
            <input type="hidden" id="celular_cliente" name="celular_cliente" :value="JSON.stringify(celulares)">
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
            tipo_documento: $('#tipo_documento').val(),
            ruc: $('#ruc').val(),
            razon_social: $('#razon_social').val(),
            dni_cliente: $('#dni_cliente').val(),
            nombre_cliente: $('#nombre_cliente').val(),
            apellido_paterno_cliente: $('#apellido_paterno_cliente').val(),
            apellido_materno_cliente: $('#apellido_materno_cliente').val(),
            correo_cliente: $('#correo_cliente').val(),
            celular_cliente: $('#celular_cliente').val(),
        };
    }

    function establecerValoresCliente(data) {
        $('#tipo_documento').val(data.tipo_documento).prop('disabled', true);
        mostrarCampos();

        $('#ruc').val(data.ruc);
        $('#razon_social').val(data.razon_social);
        $('#dni_cliente').val(data.dni_cliente);
        $('#nombre_cliente').val(data.nombre_cliente);
        $('#apellido_paterno_cliente').val(data.apellido_paterno_cliente);
        $('#apellido_materno_cliente').val(data.apellido_materno_cliente);
        $('#correo_cliente').val(data.correo_cliente);
        $('#celular_cliente').val(data.celular_cliente);
    }

    function editarCliente() {
        datosClienteOriginales = obtenerValoresCliente();
        $('#form-datos-cliente :input').prop('disabled', false);
        $('#tipo_documento').prop('disabled', true);
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
        const tipo = data.tipo_documento;

        // Validación
        if (!tipo) {
            alert('Por favor, seleccione el tipo de documento (DNI o RUC).');
            return;
        }

        if (tipo === 'ruc') {
            if (
                !data.ruc ||
                !data.razon_social
            ) {
                alert('Por favor, complete todos los campos obligatorios para RUC.');
                return;
            }
        }

        if (tipo === 'dni') {
            if (
                !data.dni_cliente ||
                !data.nombre_cliente ||
                !data.apellido_paterno_cliente ||
                !data.apellido_materno_cliente
            ) {
                alert('Por favor, complete todos los campos obligatorios para DNI.');
                return;
            }
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
                view: 'update-cliente',
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

    function mostrarCampos() {
        const tipo = document.getElementById('tipo_documento').value;
        const camposRuc = document.getElementById('campos-ruc');
        const camposDni = document.getElementById('campos-dni');

        if (tipo === 'ruc') {
            camposRuc.style.display = 'flex';
            camposDni.style.display = 'none';
        } else if (tipo === 'dni') {
            camposRuc.style.display = 'none';
            camposDni.style.display = 'flex';
        } else {
            camposRuc.style.display = 'none';
            camposDni.style.display = 'none';
        }
    }
    $(document).ready(function () {
        if (!$('#tipo_documento').val()) {
            $('#tipo_documento').prop('disabled', false);
        }
        mostrarCampos();
    });

    function telefonoManager(celularesIniciales) {
        return {
            nuevo: "",
            celulares: celularesIniciales || [],
            agregar() {
                if (this.nuevo.length === 9 && /^[0-9]+$/.test(this.nuevo)) {
                    this.celulares.push(this.nuevo);
                    this.nuevo = "";
                } else {
                    alert("Debe ingresar un número de 9 dígitos");
                }
            },
            eliminar(index) {
                this.celulares.splice(index, 1);
            }
        }
    }
</script>
