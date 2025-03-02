@props([
    'type' => 'button',
    'tier' => 'primary'
])
@php
    $classes = [
        'primary' => 'rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600',
        'secondary' => 'px-3 py-2 text-sm/6 font-semibold text-gray-900',
        'danger' => 'rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600',
    ];
@endphp


@if ($type === 'a')
<a {{ $attributes->merge(['class' => $classes[$tier] ]) }}>
    {{ $slot }}
</a>
@else
<button {{ $type === 'submit' ? 'type=submit' : '' }} {{ $attributes->merge(['class' => $classes[$tier] ]) }}>
    {{ $slot }}
</button>
@endif