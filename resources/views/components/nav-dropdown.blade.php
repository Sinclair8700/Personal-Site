@props([
    'text' => 'Dropdown',
    'type' => 'button',
    'href' => 'javascript:void(0)',
    'active' => false
])
<div class="header-dropdown relative">
    <x-nav-link class="activator" :active="$active" :type="$type" :href="$href">
        {{ $text }}
    </x-nav-link>
    <div class="dropdown hidden absolute top-[100%] pt-4 w-fit z-30">
        <div class="rounded-xl bg-black shadow-[0_0_8px_rgba(255,255,255,0.35)] p-4 flex flex-col gap-2 text-white w-full">
            {{ $slot }}
        </div>
    </div>
</div>