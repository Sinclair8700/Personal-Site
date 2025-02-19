<x-page title={{$title}}>
    <x-content type="wide">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($education as $item)
                <a href="/education/{{ $item->slug }}" class="text-white">
                    {{ $item->name }}
                </a>
            @endforeach
        </div>
    </x-content>
</x-page>

