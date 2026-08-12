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
        <div class="flex flex-col gap-2 text-white w-full md:rounded-xl md:bg-black md:shadow-[0_0_8px_rgba(255,255,255,0.35)] md:p-4">
            {{ $slot }}
        </div>
    </div>
</div>
