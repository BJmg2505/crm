@props([
    'botonHeader' => '',
    'botonFooter' => '',
])

<x-sistema.card class="p-4 m-1 mx-0">
    {{-- Título y botones --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <x-sistema.titulo title="Etapa" />
        <div class="d-flex gap-2">
            {{ $botonHeader }}
        </div>
    </div>

    {{-- Sel    ector compacto --}}
    <div class="row align-items-center">
        <div class="col-auto">
            <label for="etapa_id" class="form-label mb-0 me-2 fw-bold">Etapa:</label>
        </div>
        <div class="col-auto">
            <select class="form-select form-select-sm w-auto" id="etapa_id">
                @foreach ($etapas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Footer --}}
    <div class="mt-3 text-end">
        {{ $botonFooter }}
    </div>
</x-sistema.card>
