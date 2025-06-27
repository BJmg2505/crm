<section {{ $attributes->merge(['class' => 'w-[40px] h-[40px] bg-white text-orange-400 text-xl font-extrabold border-2 border-orange-500 flex justify-center items-center rounded-full']) }} data-bs-toggle="tooltip" data-bs-original-title="{{ $toggle }}">
    {{ $slot }}
</section>