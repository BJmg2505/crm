<button {{ $attributes->merge(['class' => 'bg-[#EB5C1F] text-white hover:bg-[#d0451b] rounded-xl font-semibold px-4 py-2 transition-all duration-300 shadow-md hover:shadow-orange-500/20']) }}>
    {{ $slot }}
</button>