<x-page title={{$title}}>
    <x-content class="h-full  flex flex-col items-center justify-center py-6">
        <canvas class="w-full h-full hidden" id="physics-canvas"></canvas>
        <div id="parameters" class="flex flex-col items-center justify-center gap-4">
            <h2 class="text-4xl font-bold">Physics Paint</h2>
            <p class="text-lg">A physics paint application</p>
            <x-input type="number" id="gravity" placeholder="Gravity" min="0" max="100" >
                Gravitational Constant
            </x-input>
            <x-input type="number" id="atom-size" placeholder="Atom Size" min="1" max="100" >
                Atom Size
            </x-input>
            <x-button id="start-button">Start</x-button>
        </div>
    </x-content>
</x-page>

@vite('resources/js/physics-paint.js')