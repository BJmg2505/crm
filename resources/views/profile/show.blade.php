@extends('layouts.app')

@section('content')
    <x-sistema.card-contenedor>
        <div class="p-4">
            <div class="d-flex flex-row justify-content-between">
                <div>
                    <x-sistema.titulo title="Perfil"/>
                </div>
            </div>
        </div>
        <div class="p-4">
            <div class=" bg-slate-400 bg-opacity-5 p-4 rounded-md" id="dialog">
                <div class="form-group">
                    <label for="name" class="form-control-label">Nombre:</label>
                    <input class="form-control" type="text" value="{{ auth()->user()->name }}" id="name" name="name" disabled>
                </div>
                <div class="form-group">
                    <label for="email" class="form-control-label">Correo:</label>
                    <input class="form-control" type="email" value="{{ auth()->user()->email }}" id="email" name="email" disabled>
                </div>
                <div class="form-group">
                    <label for="password" class="form-control-label">Nueva Contraseña</label>
                    <input class="form-control" type="password" value="" id="password" name="password" placeholder="******">
                </div>
                <div class="flex justify-end">
                    <button type="button" class="btn bg-gradient-primary m-0" onclick="updateUsuario({{ auth()->user()->id }})">Actualizar</button>
                </div>
            </div>
        </div>
    </x-sistema.card-contenedor>
    <script>
        function updateUsuario(user_id) {
            const dialog = document.querySelector("#dialog");
            dialog.querySelectorAll('.is-invalid, .invalid-feedback').forEach(element => {
                element.classList.contains('is-invalid') ? element.classList.remove('is-invalid') : element.remove();
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: `{{ url('lista_usuario/${user_id}') }}`,
                method: "PUT",
                data: {
                    view: 'update-user',
                    password: $('#password').val(),
                },
                success: function( result ) {
                    location.reload();
                },
                error: function( data ) {
                    let errors = data.responseJSON;
                    if(errors) {
                        $.each(errors.errors, function(key, value){
                            $('#dialog #'+key).addClass('is-invalid');
                            $('#dialog #'+key).after('<span class="invalid-feedback" role="alert"><strong>'+ value +'</strong></span>');
                        });
                    }
                }
            });
        }
    </script>
@endsection
