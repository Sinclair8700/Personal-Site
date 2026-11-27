<x-page :title="$title" :description="$description ?? null" :image="$image ?? null" :noindex="$noindex ?? false">
    @php
        $description = $description ?? null;
        $image = $image ?? null;
        $projectName = $project->getRawOriginal('name');
        // A code project with a repo link gets the more specific SoftwareSourceCode
        // type + codeRepository; anything else (CAD, electronics, demos) stays a
        // generic CreativeWork with the link recorded as sameAs.
        $projectLink = $project->link ?: null;
        $isRepo = $projectLink && \Illuminate\Support\Str::contains($projectLink, ['github.com', 'gitlab.com', 'bitbucket.org']);
        $projectLd = [
            '@context' => 'https://schema.org',
            '@type' => $isRepo ? 'SoftwareSourceCode' : 'CreativeWork',
            'name' => $projectName,
            'url' => url('/projects/'.$project->slug),
            'author' => ['@id' => rtrim(config('app.url'), '/').'/#person'],
        ];
        if ($project->updated_at) { $projectLd['dateModified'] = $project->updated_at->toAtomString(); }
        if ($description) { $projectLd['abstract'] = $description; }
        if ($image) { $projectLd['image'] = asset($image); }
        if ($isRepo) {
            $projectLd['codeRepository'] = $projectLink;
        } elseif ($projectLink) {
            $projectLd['sameAs'] = [$projectLink];
        }
        $breadcrumbLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Projects', 'item' => url('/projects')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $projectName, 'item' => url('/projects/'.$project->slug)],
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
                        @php
                            $img = \App\Support\ResponsiveImage::attributes('projects/'.$project->slug.'/'.$image->filename);
                        @endphp
                        <img src="{{ $img['src'] }}"
                            @if($img['srcset']) srcset="{{ $img['srcset'] }}" sizes="(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw" @endif
                            alt="{{ $project->name }} screenshot {{ $loop->iteration }}"
                            {{-- The first screenshot is above the fold: load it straight away. --}}
                            loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                            @if($loop->first) fetchpriority="high" @endif
                            decoding="async"
                            class="w-full h-auto object-cover rounded-lg">
                    @endforeach
                </div>
            @endif
            <p class="text-justify">{{ $project->description }}</p>
            @if($projectLink)
                <x-button type="a" href="{{ $projectLink }}" target="_blank" rel="noopener" class="inline-block mt-4">
                    {{ $isRepo ? 'View the source code' : 'Try it live' }}
                </x-button>
            @endif
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
