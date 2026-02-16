@props([
    'project' => null,
])
@php
    $images = $project?->images ?? collect();
@endphp
@if($images->isNotEmpty())
<a {{ $attributes->merge(['class' => '']) }} href="/projects/{{ $project->slug ?? null }}">
    <x-bubble class="max-w-full flex flex-col xs:flex-row h-full xs:aspect-[2/1] [&]:p-0 xs:[&:has(.the-image:hover)_.the-text]:w-0 xs:[&:has(.the-image:hover)_.the-text]:opacity-0 min-w-0">
        <div class="the-text order-2 xs:order-first w-full xs:w-1/2  flex flex-col min-h-0 transition-[width,_opacity] duration-600 shrink-0">
            <h3 class="mx-6 mt-4 text-white mb-1">{{ $project->name ?? null }}</h3>
            <p class="mx-6 mb-4 text-sm max-h-[280px] text-white hyphens-auto overflow-y-auto min-h-0 h-full flex-1 scrollbar-thumb-rounded-full scrollbar-track-rounded-full scrollbar scrollbar-w-[10px] scrollbar-thumb-slate-700 scrollbar-track-transparent">
                {{ $project->description ?? null }}</p>
        </div>

        <div class="the-image aspect-square w-full min-w-0 overflow-hidden">
            <div class="project-image-swiper swiper h-full w-full">
                <div class="swiper-wrapper">
                    @foreach($images as $image)
                    <div class="swiper-slide">
                        <img src="{{ asset('storage/projects/'.$project->slug.'/'.$image->filename) }}"
                            alt="{{ $project->name ?? null }}"
                            class="w-full h-full object-cover rounded-t-lg xs:rounded-t-none sm:rounded-r-lg"
                            loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                    </div>
                    @endforeach
                </div>
                @if($images->count() > 1)
                <div class="swiper-pagination"></div>
                @endif
            </div>
        </div>
    </x-bubble>
</a>
@endif