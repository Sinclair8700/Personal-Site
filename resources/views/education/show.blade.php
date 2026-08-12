<x-page :title="$title" :description="$education->description">
    @php
        $breadcrumbLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Education', 'item' => url('/education')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $education->name, 'item' => url()->current()],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($breadcrumbLd, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    <x-content type="wide" class="py-6 text-white">
        <div class="max-w-3xl mx-auto flex flex-col gap-4">
            <img src="{{ asset('storage/education/'.$education->image) }}"
                alt="{{ $education->name }}"
                class="w-full max-w-2xl h-auto rounded-lg"
                onerror="this.onerror=null;this.src='{{ asset('images/education-placeholder.png') }}';">
            <div class="flex flex-col gap-1">
                <p class="text-white/70">{{ $education->location }}</p>
                <p class="text-white/70">{{ $education->start_date->format('M Y') }} &ndash; {{ $education->end_date->format('M Y') }}</p>
            </div>
            <p>{{ $education->description }}</p>
            @if($education->link)
                <a href="{{ $education->link }}" target="_blank" rel="noopener noreferrer"
                    class="underline w-fit hover:text-purple transition-colors duration-300">Visit website</a>
            @endif
        </div>
    </x-content>
</x-page>
