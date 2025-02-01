@props([
    'type' => 'wide'
])

@php
    $types = [
        'wide' => 'max-w-[1400px]',
        'medium' => 'max-w-[1000px]',
        'narrow' => 'max-w-[600px]',
    ];
@endphp

<div {{ 
    $attributes->merge([
        'class' => 'w-full ' . $types[$type] . ' mx-auto px-6 flex flex-col'
        ]) 
}}>
    {{ $slot }}
</div>