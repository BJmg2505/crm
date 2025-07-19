@props([
    'botonHeader' => '',
    'botonFooter' => '',
    'movistar' => '',
])
<x-sistema.card class="p-4 m-2 mb-2 mx-0">
    <div class="d-flex flex-row flex-wrap justify-between items-center mb-2">
        <div></div>
        <div class="flex flex-row gap-2">
            {{ $botonHeader }}
        </div>
    </div>

    <div class="row" id="form-datos-adicionales">
        @if ($config['datosAdicionales']['estadoWick'])
            <div class="col-md-4 mb-3">
                <label for="estadowick_id">Estado Winforce</label>
                <select class="form-control" id="estadowick_id" disabled>
                    <option value="">Seleccione...</option>
                    @foreach ($estadowicks as $value)
                        <option value="{{ $value->id }}"
                            {{ $movistar && $movistar->estadowick_id == $value->id ? 'selected' : '' }}>
                            {{ $value->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        @if ($config['datosAdicionales']['lineaClaro'])
            <div class="col-md-4 mb-3">
                <label for="linea_claro">Score</label>
                <input class="form-control" type="number" id="linea_claro" name="linea_claro"
                    value="{{ $movistar->linea_claro ?? '' }}" disabled>
            </div>
        @endif

        @if ($config['datosAdicionales']['lineaEntel'])
            <div class="col-md-4 mb-3">
                <label for="linea_entel">Cant. Trabajadores</label>
                <input class="form-control" type="number" id="linea_entel" name="linea_entel"
                    value="{{ $movistar->linea_entel ?? '' }}" disabled>
            </div>
        @endif

        @if ($config['datosAdicionales']['lineaBitel'])
            <div class="col-md-4 mb-3">
                <label for="linea_bitel">Cant. Sucursales</label>
                <input class="form-control" type="number" id="linea_bitel" name="linea_bitel"
                    value="{{ $movistar->linea_bitel ?? '' }}" disabled>
            </div>
        @endif

        @if ($config['datosAdicionales']['tipoCliente'])
            <div class="col-md-4 mb-3">
                <label for="clientetipo_id">Tipo de Cliente</label>
                <select class="form-control" id="clientetipo_id" disabled>
                    <option value="">Seleccione...</option>
                    @foreach ($clientetipos as $value)
                        <option value="{{ $value->id }}"
                            {{ $movistar && $movistar->clientetipo_id == $value->id ? 'selected' : '' }}>
                            {{ $value->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="col-md-4 mb-3">
            <label for="agencia_id">Estado Cliente</label>
            <select class="form-control" id="agencia_id" disabled>
                <option value="">Seleccione...</option>
                @foreach ($agencias as $value)
                    <option value="{{ $value->id }}"
                        {{ $movistar && $movistar->agencia_id == $value->id ? 'selected' : '' }}>
                        {{ $value->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{ $botonFooter }}
</x-sistema.card>

<script>
    let datosOriginales = {};

    function editarDatosAdicionales() {
        datosOriginales = obtenerValoresFormulario();
        $('#form-datos-adicionales :input').not('#linea_claro').prop('disabled', false);
        $('#btn-editar-datos').addClass('d-none');
        $('#btn-guardar-datos, #btn-cancelar-datos').removeClass('d-none');
    }

    function cancelarDatosAdicionales() {
        establecerValoresFormulario(datosOriginales);
        $('#form-datos-adicionales :input').prop('disabled', true);
        $('#btn-editar-datos').removeClass('d-none');
        $('#btn-guardar-datos, #btn-cancelar-datos').addClass('d-none');
    }

    function obtenerValoresFormulario() {
        return {
            estadowick_id: $('#estadowick_id').val() ?? 1,
            estadodito_id: $('#estadodito_id').val() ?? 1,
            linea_claro: $('#linea_claro').val() ?? '0',
            linea_entel: $('#linea_entel').val() ?? '0',
            linea_bitel: $('#linea_bitel').val() ?? '0',
            linea_movistar: $('#linea_movistar').val() ?? '0',
            clientetipo_id: $('#clientetipo_id').val() ?? 1,
            ejecutivo_salesforce: $('#ejecutivo_salesforce').val() ?? '',
            agencia_id: $('#agencia_id').val() ?? 1,
        };
    }

    function establecerValoresFormulario(data) {
        $('#estadowick_id').val(data.estadowick_id);
        $('#linea_claro').val(data.linea_claro);
        $('#linea_entel').val(data.linea_entel);
        $('#linea_bitel').val(data.linea_bitel);
        $('#clientetipo_id').val(data.clientetipo_id);
        $('#ejecutivo_salesforce').val(data.ejecutivo_salesforce);
    }

    function guardarDatosAdicionales() {
        const data = obtenerValoresFormulario();

        // Validaciones básicas
        if (data.clientetipo_id === '') {
            alert('Debe seleccionar un tipo de cliente.');
            return;
        }

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
                view: 'update-movistar',
                ...data
            },
            success: function() {
                $('#form-datos-adicionales :input').prop('disabled', true);
                $('#btn-editar-datos').removeClass('d-none');
                $('#btn-guardar-datos, #btn-cancelar-datos').addClass('d-none');
            },
            error: function() {
                mostrarError(response);
            }
        });
    }

    // Lógica extra si estadowick_id == 3
    $('#estadowick_id').on('change', function() {
        if ($(this).val() == 3) {
            $('#estadodito_id').val(3).prop('disabled', true);
        } else {
            $('#estadodito_id').val(1).prop('disabled', false);
        }
    });
    $(document).ready(function () {
        function toggleLineaClaro() {
            const selected = $('#estadowick_id').val();
            if (selected) {
                $('#linea_claro').prop('disabled', false);
            } else {
                $('#linea_claro').prop('disabled', true);
            }
        }

        // Llamamos al cargar la página por si ya viene preseleccionado
        toggleLineaClaro();

        // Escuchar cambios en el select
        $('#estadowick_id').on('change', function () {
            toggleLineaClaro();
        });

        
        $('#clientetipo_id').on('change', function() {
            const clientetipoId = $(this).val();
    
            let agenciaId = ''; // Default: libre (vacío)
    
            if (clientetipoId == 1) {
                agenciaId = 1;
            } else if (clientetipoId == 3 || clientetipoId == 4) {
                agenciaId = 2;
            }
    
            $('#agencia_id').val(agenciaId);
        });
    
        // Opcional: ejecutar una vez al cargar la página si ya hay un valor seleccionado
        $('#clientetipo_id').trigger('change');
    });
</script>
