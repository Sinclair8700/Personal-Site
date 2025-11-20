<nav class="bg-black sm:fixed sm:top-0 w-full sm:z-50" >
    <x-content >
        <div class="flex h-16 items-center justify-between w-full">
            <div class="flex items-center w-full">
                <h2 class="text-white">alexdavi.es</h2>
                <div class="hidden md:block w-full ml-10">
                    <div class="flex items-baseline justify-start gap-4 w-full">
                        {{ $slot }}
                    </div>
                </div>
            </div>
            
            
        </div>
    </x-content>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="md:hidden" id="mobile-menu">
        <div class="flex gap-4 px-3 pb-3 pt-2 items-baseline">
            <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
            {{ $slot }}
        </div>
        
    </div>
</nav>