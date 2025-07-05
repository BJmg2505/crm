<div {{ $attributes->merge(['class' => 'rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 ease-in-out p-2 m-0 mt-0 mr-0 mb-0 ml-0']) }}>
    <div class="text-gray-800">
        {{ $slot }}
    </div>
</div>