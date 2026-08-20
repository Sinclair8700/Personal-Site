<x-page :title="$title" :description="$description ?? null">
    @php
        // Tell search engines this is a list page and enumerate the projects it
        // links to (helps rich results / carousels). Raw names avoid the HTML
        // escaping the name accessor applies for display.
        $projectItems = $projects->values();
        $itemListLd = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Projects',
            'url' => url()->current(),
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $projectItems->count(),
                'itemListElement' => $projectItems->map(fn ($p, $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'url' => url('/projects/' . $p->slug),
                    'name' => $p->getRawOriginal('name'),
                ])->all(),
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($itemListLd, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    <x-content type="wide" class="py-6">

        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($projects as $project)
                <x-project :project="$project" :eager="$loop->first"/>
            @endforeach
        </div>
    </x-content>
</x-page>
