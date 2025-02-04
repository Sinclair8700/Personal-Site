<x-page title={{$title}}>
    <x-content class="h-full">
        <h1>Welcome!</h1>
        <h2>I'm Alex, a software engineer based in The United Kingdom.</h2>
        <h2>This place is my personal space on the internet where you can find my projects, thoughts and other stuff.</h2>

        <h1 class="mt-4">Projects</h1>

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
                @foreach (\App\Models\Project::all() as $project)
                    @php
                        $image = base_path('/resources/views/projects/projects/'.$project->slug.'/main.png');
                        if(!file_exists($image)){
                            continue;
                        }
                    @endphp
                    <div class="swiper-slide border-2 border-white">
                        <a href="/projects/{{ $project->slug }}" class="relative">
                            <img src="{{ Vite::asset('resources/views/projects/projects/'.$project->slug.'/main.png') }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                            <div class="absolute top-0 left-0 w-full h-full bg-black/50 hover:bg-transparent hover:opacity-0 transition-all duration-300 flex items-center justify-center">
                                <h1 class="text-white text-2xl font-bold">{{ $project->name }}</h1>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="swiper-pagination"></div>

            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="w-full border-2 border-white px-6 rounded-md">
                <h1 class="mt-4 !mb-2">Socials</h1>
                <ul class="list-disc list-inside underline">
                    <li><a href="https://discord.com/invite/w3MqeUnY" target="_blank">Discord</a></li>
                    <li><a href="https://steamcommunity.com/id/a-l-ex" target="_blank">Steam</a></li>
                    <li><a href="https://github.com/Sinclair8700" target="_blank">Github</a></li>
                    <li><a href="https://www.linkedin.com/in/alex-davies-aa10a7215/" target="_blank">LinkedIn</a></li>
                </ul>
            </div>
            <div class="w-full border-2 border-white px-6 rounded-md">
                <h1 class="mt-4 !mb-2">Hobbies</h1>
                <ul class="list-disc list-inside">
                    <li>Gaming</li>
                    <li>Coding</li>
                    <li>Pool</li>
                    <li>Driving fast</li>
                </ul>
            </div>
            <div class="w-full border-2 border-white px-6 rounded-md">
                <h1 class="mt-4 !mb-2">Languages</h1>
                <ul class="list-disc list-inside grid grid-cols-2 md:grid-cols-3">
                    <li>C / C++</li>
                    <li>Java</li>
                    <li>C#</li>
                    <li>Python</li>
                    <li>JS</li>
                    <li>PHP</li>
                    <li>SQL</li>
                    <li>HTML</li>
                    <li>CSS</li>
                </ul>
            </div>
        </div>

    </x-content>
    @vite('resources/js/home.js')
</x-page>
