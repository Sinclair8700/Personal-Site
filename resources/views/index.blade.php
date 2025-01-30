<x-page title={{$title}}>
    <x-content class="h-full">
        <h1>Welcome!</h1>
        <h2>I'm Alex, a software engineer based in The United Kingdom.</h2>
        <h2>This place is my personal space on the internet where you can find my projects, thoughts and other stuff.</h2>

        <h1 class="mt-6">Projects</h1>

        <div class="swiper text-white w-full rounded-md mb-6">
            <div class="
            rounded-md
            swiper-wrapper 
            [&_.swiper-slide]:w-full 
            [&_.swiper-slide]:aspect-video
            [&_.swiper-slide]:bg-white
            [&_.swiper-slide]:rounded-md
            [&_.swiper-slide]:overflow-hidden
            [&_.swiper-slide]:shadow-md
            [&_.swiper-slide]:transition-all
            [&_.swiper-slide]:duration-300
            [&_.swiper-slide]:ease-in-out
            ">
                <div class="swiper-slide">Slide 1</div>
                <div class="swiper-slide">Slide 2</div>
                <div class="swiper-slide">Slide 3</div>
            </div>

            <div class="swiper-pagination"></div>

            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>

        <div class="flex flex-row gap-6">
            <div class="w-full border-2 border-white px-6 rounded-md">
                <h1 class="mt-6">Socials</h1>
                <ul class="list-disc list-inside">
                    <li>Discord</li>
                    <li>Steam</li>
                    <li>Github</li>
                    <li>LinkedIn</li>
                </ul>
            </div>
            <div class="w-full border-2 border-white px-6 rounded-md">
                <h1 class="mt-6">Hobbies</h1>
                <ul class="list-disc list-inside">
                    <li>Gaming</li>
                    <li>Coding</li>
                    <li>Pool</li>
                    <li>Driving fast</li>
                </ul>
            </div>
            <div class="w-full border-2 border-white px-6 rounded-md">
                <h1 class="mt-6">Skills</h1>
                <ul class="list-disc list-inside">
                    <li>C++</li>
                    <li>Python</li>
                    <li>JavaScript</li>
                </ul>
            </div>
        </div>

    </x-content>
    @vite('resources/js/home.js')
</x-page>
