@props([
    'botonHeader' => '',
    'botonFooter' => '',
])
<x-sistema.card class="m-2">
    <div class="d-flex flex-row flex-wrap justify-content-between">
        <x-sistema.titulo title="Etapa *" />
        <div class="flex flex-row gap-2">
            {{ $botonHeader }}
        </div>
    </div>
    <div class="form-group">
        <select class="form-control" id="etapa_id" disabled>
            @foreach ($etapas as $value)
                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
            @endforeach
        </select>
    </div>
    {{ $botonFooter }}
</x-sistema.card>
