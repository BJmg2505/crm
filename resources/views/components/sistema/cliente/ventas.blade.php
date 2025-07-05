@props([
    'botonHeader' => '',
    'botonFooter' => '',
    'ventas' => false,
])
<x-sistema.card class="p-4 m-2 mx-0">
    <div class="d-flex flex-row flex-wrap justify-content-between mb-3">
        <x-sistema.titulo title="Productos en Negociación" />
        @role('ejecutivo')
            <div class="flex flex-row gap-2" id="cont-venta-header"></div>
        @endrole
    </div>
    @role('ejecutivo')
        <div class="form-group mb-3">
            <label for="producto_id" class="form-label fw-bold">Selecciona un Producto</label>
            <select class="form-control uppercase" name="producto_id" id="producto_id"></select>
        </div>
    @endrole
    <div class="card border shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center table-striped small"
                id="producto_table">
                <thead class="table-light">
                    <tr class="text-center align-middle">
                        <th class="text-uppercase fw-bold small" style="color: #ff5100;">Producto</th>
                        <th class="text-uppercase fw-bold small" style="color: #ff5100;">Detalle</th>
                        <th class="text-uppercase fw-bold small" style="color: #ff5100;">Sucursal</th>
                        <th class="text-uppercase fw-bold small" style="color: #ff5100;">Cant</th>
                        <th class="text-uppercase fw-bold small" style="color: #ff5100;">Precio</th>
                        <th class="text-uppercase fw-bold small" style="color: #ff5100;">Cargo Fijo</th>
                        <th class="text-uppercase fw-bold small" style="color: #ff5100;">Acciones</th>
                    </tr>
                </thead>
                <tbody num="0"></tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-end fw-bold ">TOTAL</td>
                        <td>
                            <input class="form-control form-control-lg text-end fw-bold" type="number" id="total"
                                value="0.00" disabled style="width: 90px;">
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    {{ $botonFooter }}
</x-sistema.card>

<script>
    selectProducto();

    function selectProducto() {
        $('#producto_id').html('');
        $.ajax({
            url: `{{ url('producto/0') }}`,
            method: "GET",
            data: {
                view: 'show-producto-select',
            },
            success: function(productos) {
                let option = `<option value="0">Seleccionar...</option>`;
                productos.forEach(function(producto) {
                    option += `<option value="${producto.id}">${producto.nombre}</option>`;
                });
                $('#producto_id').append(option);
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    $('#producto_id').on("change", function(event) {
        let cliente_id = $('#cliente_id').val();
        $.ajax({
            url: `{{ url('producto/0') }}`,
            method: "GET",
            data: {
                view: 'show-producto-table',
                producto_id: event.target.value,
            },
            success: function(producto) {
                let tbodyRef = $('#producto_table tbody');
                let num = tbodyRef.attr('num');
                let tr = `<tr id="${num}">
                    <td class="align-middle">
                        <input type="hidden" value="${producto.id}" id="producto_id${num}">
                        <input type="hidden" value="${producto.nombre}" id="producto_nombre${num}">
                        ${producto.nombre}
                    </td>
                    <td>
                        <select class="form-control" id="detalle${num}">
                            <option value="200 Mbps">200 Mbps</option>
                            <option value="300 Mbps">300 Mbps</option>
                            <option value="400 Mbps">400 Mbps</option>
                            <option value="600 Mbps">600 Mbps</option>
                            <option value="1000 Mbps">1000 Mbps</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control" id="sucursal_id${num}"></select>
                    </td>
                    <td>
                        <input class="form-control form-control-sm" type="number" value="1" id="cantidad${num}" onkeyup="cargoFijoProducto(${num})" style="width: 80px;">
                    </td>
                    <td>
                        <input class="form-control" type="number" value="0" id="precio${num}" onkeyup="cargoFijoProducto(${num})" style="width: 80px;">
                    </td>
                    <td>
                        <input class="form-control" type="number" value="0" id="cargofijo${num}" disabled style="width: 90px;">
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-2 justify-content-center" id="acciones${num}">
                            <i class="fa-solid fa-save text-success cursor-pointer" onclick="guardarProducto(${num})"></i>
                            <i class="fa-solid fa-trash text-danger cursor-pointer" onclick="eliminarProducto(${num})"></i>
                        </div>
                    </td>
                </tr>`;
                tbodyRef.append(tr);
                getSucursalesCliente(cliente_id, num);
                cargoFijoProducto(num);
                num++;
                tbodyRef.attr('num', num);
            }
        });
        selectProducto();
    });

    function cargoFijoProducto(num) {
        let cantidad = parseFloat($('#cantidad' + num).val());
        let precio = parseFloat($('#precio' + num).val());
        let cargofijo = cantidad * precio;
        $('#cargofijo' + num).val(cargofijo.toFixed(2));
        totalVenta();
    }

    function totalVenta() {
        let trRef = $('#producto_table tbody tr');
        let total = 0;
        $.each(trRef, function(index, tr) {
            let cargofijo = parseFloat($('#cargofijo' + tr.id).val());
            total += cargofijo;
        });
        $('#total').val(total.toFixed(2));
    }

    function eliminarProducto(num) {
        // Eliminar del localStorage
        let ventasGuardadas = JSON.parse(localStorage.getItem('ventas_guardadas') || '[]');
        ventasGuardadas = ventasGuardadas.filter(item => item.num != num);
        localStorage.setItem('ventas_guardadas', JSON.stringify(ventasGuardadas));

        // Eliminar de la tabla
        $('#producto_table tbody tr#' + num).remove();
        totalVenta();
    }

    function toggleEditMode(num, isEditing) {
        // Habilitar/deshabilitar campos según el modo
        $(`#detalle${num}`).prop('disabled', !isEditing);
        $(`#sucursal_id${num}`).prop('disabled', !isEditing);
        $(`#cantidad${num}`).prop('disabled', !isEditing);
        $(`#precio${num}`).prop('disabled', !isEditing);

        // Cambiar el ícono de editar/guardar
        if (isEditing) {
            $(`#acciones${num}`).html(`
            <i class="fa-solid fa-save text-success cursor-pointer" onclick="guardarProducto(${num})"></i>
            <i class="fa-solid fa-trash text-danger cursor-pointer" onclick="eliminarProducto(${num})"></i>
        `);
        } else {
            $(`#acciones${num}`).html(`
            <i class="fa-solid fa-pen text-primary cursor-pointer" onclick="editarProducto(${num})"></i>
            <i class="fa-solid fa-trash text-danger cursor-pointer" onclick="eliminarProducto(${num})"></i>
        `);
        }
    }

    function guardarProducto(num) {
        const producto = {
            num: num,
            producto_id: $('#producto_id' + num).val(),
            producto_nombre: $('#producto_nombre' + num).val(),
            detalle: $('#detalle' + num).val(),
            cantidad: $('#cantidad' + num).val(),
            precio: $('#precio' + num).val(),
            total: $('#cargofijo' + num).val(),
            sucursal_id: $('#sucursal_id' + num).val(),
            sucursal_nombre: $('#sucursal_id' + num + ' option:selected').text()
        };

        if (!producto.producto_id || producto.producto_id == 0) {
            alert('Debe seleccionar un producto');
            return;
        }

        // Guardar en localStorage
        let ventasGuardadas = JSON.parse(localStorage.getItem('ventas_guardadas') || '[]');

        // Eliminar si ya existe
        ventasGuardadas = ventasGuardadas.filter(item => item.num != num);
        ventasGuardadas.push(producto);
        localStorage.setItem('ventas_guardadas', JSON.stringify(ventasGuardadas));

        // Deshabilitar campos y cambiar a modo visualización
        toggleEditMode(num, false);

        // Efecto visual
        $('#producto_table tr#' + num).addClass('table-success');
        setTimeout(() => {
            $('#producto_table tr#' + num).removeClass('table-success');
        }, 1000);
    }

    function editarProducto(num) {
        // Habilitar campos y cambiar a modo edición
        toggleEditMode(num, true);

        // Efecto visual
        $('#producto_table tr#' + num).addClass('table-warning');
        setTimeout(() => {
            $('#producto_table tr#' + num).removeClass('table-warning');
        }, 1000);
    }

    function getSucursalesCliente(cliente_id, num) {
        $.ajax({
            url: `{{ url('cliente-gestion/${cliente_id}') }}`,
            method: "GET",
            data: {
                view: 'show-sucursal-select',
            },
            success: function(sucursales) {
                let select = $(`#sucursal_id${num}`);
                select.html('');
                let option = ``;
                sucursales.forEach(function(sucursal) {
                    option += `<option value="${sucursal.id}">${sucursal.nombre}</option>`;
                });
                select.append(option);
            }
        });
    }

    // Mostrar los productos guardados desde localStorage al recargar
    $(document).ready(function() {
        let ventasGuardadas = JSON.parse(localStorage.getItem('ventas_guardadas') || '[]');
        let tbodyRef = $('#producto_table tbody');
        let num = parseInt(tbodyRef.attr('num') || 0);

        ventasGuardadas.forEach(producto => {
            let tr = `<tr id="${producto.num}">
                <td class="align-middle">
                    <input type="hidden" value="${producto.producto_id}" id="producto_id${producto.num}">
                    <input type="hidden" value="${producto.producto_nombre}" id="producto_nombre${producto.num}">
                    ${producto.producto_nombre}
                </td>
                <td>
                    <select class="form-control" id="detalle${producto.num}" disabled>
                        <option value="200 Mbps" ${producto.detalle === '200 Mbps' ? 'selected' : ''}>200 Mbps</option>
                        <option value="300 Mbps" ${producto.detalle === '300 Mbps' ? 'selected' : ''}>300 Mbps</option>
                        <option value="400 Mbps" ${producto.detalle === '400 Mbps' ? 'selected' : ''}>400 Mbps</option>
                        <option value="600 Mbps" ${producto.detalle === '600 Mbps' ? 'selected' : ''}>600 Mbps</option>
                        <option value="1000 Mbps" ${producto.detalle === '1000 Mbps' ? 'selected' : ''}>1000 Mbps</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" id="sucursal_id${producto.num}" disabled></select>
                </td>
                <td>
                    <input class="form-control" type="number" value="${producto.cantidad}" id="cantidad${producto.num}" onkeyup="cargoFijoProducto(${producto.num})" disabled style="width: 80px;">
                </td>
                <td>
                    <input class="form-control" type="number" value="${producto.precio}" id="precio${producto.num}" onkeyup="cargoFijoProducto(${producto.num})" disabled style="width: 80px;">
                </td>
                <td>
                    <input class="form-control" type="number" value="${producto.total}" id="cargofijo${producto.num}" disabled style="width: 90px;">
                </td>
                <td class="text-center">
                    <div class="d-flex gap-2 justify-content-center" id="acciones${producto.num}">
                        <i class="fa-solid fa-pen text-primary cursor-pointer" onclick="editarProducto(${producto.num})"></i>
                        <i class="fa-solid fa-trash text-danger cursor-pointer" onclick="eliminarProducto(${producto.num})"></i>
                    </div>
                </td>
            </tr>`;
            tbodyRef.append(tr);
            getSucursalesCliente($('#cliente_id').val(), producto.num);

            // Establecer el valor seleccionado de sucursal después de cargar las opciones
            setTimeout(() => {
                $(`#sucursal_id${producto.num}`).val(producto.sucursal_id);
            }, 300);

            cargoFijoProducto(producto.num);

            // Actualizar el contador num si es necesario
            if (producto.num >= num) {
                num = producto.num + 1;
            }
        });
        tbodyRef.attr('num', num);
    });
</script>
