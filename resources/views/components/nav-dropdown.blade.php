@props([
    'text' => 'Dropdown',
    'type' => 'button',
    'href' => 'javascript:void(0)',
    'active' => false
])
<div class="dropdown-container relative flex flex-wrap items-center justify-between md:block">
    <x-nav-link class="activator" :active="$active" :type="$type" :href="$href">
        {{ $text }}
    </x-nav-link>

    <!-- Accordion toggle, mobile drawer only. -->
    <button type="button" class="dropdown-toggle p-2 text-gray-300 hover:text-white md:hidden" aria-label="Toggle {{ $text }} submenu" aria-expanded="false">
        <svg class="dropdown-chevron size-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <div class="dropdown hidden static w-full pt-1 pl-4 z-30 md:absolute md:top-[100%] md:w-fit md:pt-4 md:pl-0">
        <div class="relative w-full md:rounded-xl md:bg-black md:shadow-[0_0_8px_rgba(255,255,255,0.35)] md:overflow-hidden">
            {{-- On desktop a long list scrolls inside the panel. The scrollbar stays visible, and
                 dropdowns.js adds .has-more while there is more below, which shows the fade and hint. --}}
            <div class="dropdown-scroll flex flex-col gap-2 text-white w-full md:p-4 md:pr-3 md:max-h-[min(70vh,30rem)] md:overflow-y-auto md:overscroll-contain md:scrollbar-thin md:scrollbar-thumb-rounded-full md:scrollbar-thumb-white/60 md:scrollbar-track-white/10">
                {{ $slot }}
            </div>
            <div class="dropdown-more pointer-events-none absolute inset-x-0 bottom-0 h-16 flex-col items-center justify-end gap-0.5 pb-2 bg-gradient-to-t from-black via-black/90 to-transparent text-xs font-medium text-white/80" aria-hidden="true">
                <span>Scroll for more</span>
                <svg class="size-4 animate-bounce" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
        </div>
    </div>
</div>
