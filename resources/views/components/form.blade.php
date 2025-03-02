<form {{ !isset($method) || $method == 'GET' ? 'method=GET': 'method=POST' }} {{ $attributes->merge(['class' => 'flex flex-col gap-4 max-w-4xl ']) }}>
    @csrf
    @method($method ?? 'GET')
    {{ $slot }}
</form>


