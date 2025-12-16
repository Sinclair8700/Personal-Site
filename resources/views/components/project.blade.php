@props([
    'project' => null
])
<a href="/projects/{{ $project->slug ?? null }}">
    <x-bubble class="max-w-full flex flex-col xs:flex-row h-full xs:aspect-[2/1] [&]:p-0 xs:[&:has(.the-image:hover)_.the-text]:w-0 xs:[&:has(.the-image:hover)_.the-text]:opacity-0 min-w-0">
        <div class="the-text order-2 xs:order-first w-full xs:w-1/2  flex flex-col min-h-0 transition-[width,_opacity] duration-600 shrink-0">
            <h3 class="mx-6 mt-4 text-white mb-1">{{ $project->name ?? null }}</h3>
            <p class="mx-6 mb-4 text-sm max-h-[280px] text-white hyphens-auto overflow-y-auto min-h-0 h-full flex-1 scrollbar-thumb-rounded-full scrollbar-track-rounded-full scrollbar scrollbar-w-[10px] scrollbar-thumb-slate-700 scrollbar-track-transparent">
                {{ $project->description ?? null }}</p>
        </div>

        <div class="the-image aspect-square w-full">
            <img src="{{ asset('storage/projects/' . ($project->slug ?? null) . '/main.png') }}"
                alt="{{ $project->name ?? null }}"
                class="w-full h-full object-cover rounded-t-lg xs:rounded-t-none sm:rounded-r-lg">
        </div>
    </x-bubble>
</a>