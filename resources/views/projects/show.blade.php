<x-page :title="$title" :description="$description" :image="$image">
    @php
        $projectName = $project->getRawOriginal('name');
        $projectLd = [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $projectName,
            'url' => url()->current(),
            'author' => ['@type' => 'Person', 'name' => 'Alex Davies'],
        ];
        if ($description) { $projectLd['abstract'] = $description; }
        if ($image) { $projectLd['image'] = asset($image); }
        $breadcrumbLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Projects', 'item' => url('/projects')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $projectName, 'item' => url()->current()],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($projectLd, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    <script type="application/ld+json">{!! json_encode($breadcrumbLd, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    <x-content class="py-6">
        @if(session('warning'))
            <x-bubble class="mb-4 border-l-4 border-amber-500">
                <p class="text-amber-200">{{ session('warning') }}</p>
            </x-bubble>
        @endif
        <div class="text-white">
            @if($project->hasImages())
                <div class="w-full md:w-1/2 lg:w-1/3 float-left mr-6 mb-4 space-y-4">
                    @foreach($project->images as $image)
                        <img src="{{ asset('storage/projects/'.$project->slug.'/'.$image->filename) }}"
                            alt="{{ $project->name }} — screenshot {{ $loop->iteration }}"
                            loading="lazy"
                            class="w-full h-auto object-cover rounded-lg">
                    @endforeach
                </div>
            @endif
            <p class="text-justify">{{ $project->description }}</p>
            <div class="clear-both"></div>
        </div>

        @php
            try {
                echo view('projects.projects.' . $project->slug . '.index');
            } catch (\Exception $e) {
                if(auth()->check() && auth()->user()->hasRole('admin')){
                    echo "<h2>Project does not have a view in the projects/projects folder with the slug: " . $project->slug . "</h2>";
                }
            }
        @endphp
    </x-content>
</x-page>
