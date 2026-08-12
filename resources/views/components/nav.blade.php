<nav class="bg-black sm:fixed sm:top-0 w-full sm:z-50" >
    <x-content >
        <div class="flex h-16 items-center justify-between w-full gap-4">
            <a href="/" class="text-white shrink-0">alexdavi.es</a>

            <!-- Desktop menu -->
            <div class="nav-desktop hidden md:block w-full ml-4">
                <div class="flex items-baseline justify-start gap-4 w-full">
                    {{ $slot }}
                </div>
            </div>

            <!-- Hamburger button (mobile) -->
            <button
                type="button"
                id="mobile-menu-button"
                aria-label="Open menu"
                aria-controls="mobile-drawer"
                aria-expanded="false"
                class="md:hidden inline-flex items-center justify-center rounded-md p-2 text-gray-300 hover:bg-gray-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-white shrink-0"
            >
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>
    </x-content>

    <!-- Mobile drawer, toggled by the hamburger button. -->
    <div id="mobile-drawer" class="md:hidden hidden fixed inset-0 z-[60]">
        <!-- Backdrop -->
        <div id="mobile-drawer-backdrop" class="absolute inset-0 bg-black/60 opacity-0 transition-opacity duration-300"></div>

        <!-- Panel -->
        <div
            id="mobile-drawer-panel"
            class="absolute top-0 right-0 flex h-full w-72 max-w-[80%] translate-x-full flex-col bg-black shadow-[0_0_20px_rgba(255,255,255,0.15)] transition-transform duration-300 ease-in-out"
        >
            <div class="flex h-16 shrink-0 items-center justify-between border-b border-gray-800 px-4">
                <span class="font-medium text-white">Menu</span>
                <button
                    type="button"
                    id="mobile-menu-close"
                    aria-label="Close menu"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-300 hover:bg-gray-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                >
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="nav-mobile flex flex-col gap-1 overflow-y-auto p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</nav>
