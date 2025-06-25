<section class="overflow-x-auto">
    <table {{ $attributes->merge(['id' => 'ui-table', 'class' => 'ui-table table align-items-center mb-0']) }}>
        <thead>
            {{ $thead }}
        </thead>
        <tbody num="0">
            {{ $tbody }}
        </tbody>
        <tfoot>
            {{ $tfoot }}
        </tfoot>
    </table>
</section>
<script>
    $(document).ready(function(){
        $('.ui-table th').addClass('text-center text-uppercase text-warning text-xxs font-weight-bolder opacity-7');
        $('.ui-table td').addClass('align-middle text-center text-xs uppercase');

        // Agregar clase hover a las filas
        $('.ui-table tbody tr').hover(
            function() {
                $(this).addClass('bg-gray-100');
            },
            function() {
                $(this).removeClass('bg-gray-100');
            }
        );

        // Datatable
        $('#ui-table').DataTable({
            dom: '<"flex justify-between p-4"fl>rt<"flex justify-between p-4"ip>',
            processing: true,
            language: {
                search: 'Buscar:',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ entradas',
                processing: 'Cargando',
            },
            pageLength: 50,
            order: [],
        });
    });
</script>
