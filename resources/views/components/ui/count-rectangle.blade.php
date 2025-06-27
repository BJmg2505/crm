<section {{ $attributes->merge(['class' => 'w-[35px] h-[25px] bg-orange-200 text-orange-600 text-base font-extrabold border-2 border-orange-200 flex justify-center items-center rounded-lg']) }} data-bs-toggle="tooltip" data-bs-original-title="{{ $toggle }}">
    {{ $slot }}
</section>
