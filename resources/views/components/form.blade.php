@props([
    'method' => 'POST',
])
@php
    $formMethod = in_array(strtoupper($method), ['GET', 'POST']) ? strtoupper($method) : 'POST';
@endphp
<form method="{{ $formMethod }}" {{ $attributes->except('method')->merge(['class' => 'flex flex-col gap-4 max-w-4xl']) }}>
    @csrf
    @if($formMethod !== 'GET')
        @method($method)
    @endif
    {{ $slot }}
</form>


