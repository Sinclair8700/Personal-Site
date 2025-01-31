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
    <div class="dropdown hidden absolute top-[100%] pt-4">
        <div class="rounded-xl bg-black">
            {{ $slot }}
        </div>
    </div>
</div>