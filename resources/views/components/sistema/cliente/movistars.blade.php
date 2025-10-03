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
        @if ($config['datosAdicionales']['lineaClaro'])
            <div class="col-md-4 mb-3">
                <label for="linea_claro">Líneas Claro</label>
                <input class="form-control"
                    type="number"
                    id="linea_claro"
                    name="linea_claro"
                    min="0"
                    step="1"
                    value="{{ $movistar->linea_claro ?? '' }}">
            </div>
        @endif
        @if ($config['datosAdicionales']['lineaMovistar'])
            <div class="col-md-4 mb-3">
                <label for="linea_movistar">Líneas Movistar</label>
                <input class="form-control"
                    type="number"
                    id="linea_movistar"
                    name="linea_movistar"
                    min="0"
                    step="1"
                    value="{{ $movistar->linea_movistar ?? '' }}">
            </div>
        @endif
        @if ($config['datosAdicionales']['lineaBitel'])
            <div class="col-md-4 mb-3">
                <label for="linea_bitel">Líneas Bitel</label>
                <input class="form-control"
                    type="number"
                    id="linea_bitel"
                    name="linea_bitel"
                    min="0"
                    step="1"
                    value="{{ $movistar->linea_bitel ?? '' }}">
            </div>
        @endif
        <div class="col-md-4 mb-3">
            <label for="cantidad_lineas">Cant. Líneas</label>
            <input class="form-control"
                type="number"
                id="cantidad_lineas"
                name="cantidad_lineas"
                min="0"
                step="1"
                value="{{ $movistar->cantidad_lineas ?? '' }}">
        </div>
        <div class="col-md-4 mb-3">
            <label for="cargo_fijo">Cargo Fijo</label>
            <input class="form-control"
                type="number"
                id="cargo_fijo"
                name="cargo_fijo"
                min="0"
                step="1"
                value="{{ $movistar->cargo_fijo ?? '' }}">
        </div>
        @if ($config['datosAdicionales']['estadoWick'])
            <div class="col-md-4 mb-3">
                <label for="estadowick_id">Tipo</label>
                <select class="form-control" id="estadowick_id">
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
    </div>
    {{ $botonFooter }}
</x-sistema.card>
<script>
    let datosOriginales = {};
    function editarDatosAdicionales() {
        datosOriginales = obtenerValoresFormulario();
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
</script>
