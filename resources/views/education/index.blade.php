<x-page :title="$title" :description="$description ?? null">
    <x-content type="wide" class="py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($education as $item)
                <a href="/education/{{ $item->slug }}"
                    class="flex flex-col rounded-lg overflow-hidden border-2 border-white bg-black text-white transition-colors duration-300 hover:border-purple focus-within:border-purple">
                    <img src="{{ asset('storage/education/'.$item->image) }}"
                        alt="{{ $item->name }}"
                        loading="lazy"
                        class="w-full h-40 object-cover"
                        onerror="this.onerror=null;this.src='{{ asset('images/education-placeholder.png') }}';">
                    <div class="p-4 flex flex-col gap-2 flex-1">
                        <h2 class="leading-tight">{{ $item->name }}</h2>
                        <p class="text-white/70">{{ $item->description }}</p>
                        <p class="text-white/50 text-sm mt-auto">{{ $item->location }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </x-content>
</x-page>
