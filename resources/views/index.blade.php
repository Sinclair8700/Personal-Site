<x-page title={{$title}}>
    <x-content class="h-full flex flex-col gap-6">
        <div class="w-full">
            <h1>Welcome!</h1>
            <h2>I'm Alex, a software engineer based in The United Kingdom.</h2>
            <h2 class="[&&]:mb-1">This place is my personal space on the internet where you can find my projects, thoughts and other stuff.</h2>
        </div>
        <div class="w-full">
            <h1 class="">Projects</h1>
            <div class="swiper text-white w-full rounded-md">
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
                        <div class="swiper-slide border-2 border-white h-full w-full">
                            <a href="/projects/{{ $project->slug }}" class="relative w-full h-full block">
                                <img src="{{ Vite::asset('resources/views/projects/projects/'.$project->slug.'/main.png') }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                                <div class="absolute top-0 bottom-0 left-0 right-0 bg-black/50 hover:bg-transparent hover:opacity-0 transition-all duration-300">
                                    <h1 class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white text-2xl font-bold text-center">{{ $project->name }}</h1>
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
        </div>
        <!--<div class="w-full border-2 border-white px-6 rounded-md">
            <h1 class="mt-4 mb-2!">Bio</h1>
            <p>
                I'm a software engineer based in the UK. I'm currently studying Computer Science at the University of York.
            </p>
        </div>-->
        <div class="grid grid-cols-12 gap-6">
            <div class="w-full border-2 border-white px-6 rounded-md col-span-12 sm:col-span-6 lg:col-span-4">
                <h1 class="mt-4 [&&]:mb-2">Socials</h1>
                <ul class="list-disc list-inside underline">
                    <li><a href="https://discord.com/invite/w3MqeUnY" target="_blank">Discord</a></li>
                    <li><a href="https://steamcommunity.com/id/a-l-ex" target="_blank">Steam</a></li>
                    <li><a href="https://github.com/Sinclair8700" target="_blank">Github</a></li>
                    <li><a href="https://www.linkedin.com/in/alex-davies-aa10a7215/" target="_blank">LinkedIn</a></li>
                </ul>
            </div>
            <div class="w-full border-2 border-white px-6 rounded-md col-span-12 sm:col-span-6 lg:col-span-4">
                <h1 class="mt-4 [&&]:mb-2">Hobbies</h1>
                <ul class="list-disc list-inside">
                    <li>Gaming</li>
                    <li>Coding</li>
                    <li>Pool</li>
                    <li>Driving fast</li>
                </ul>
            </div>
            <div class="w-full border-2 border-white px-6 rounded-md col-span-12 sm:col-span-6 lg:col-span-4">
                <h1 class="mt-4 [&&]:mb-2">Languages</h1>
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
            <div class="w-full border-2 border-white px-6 rounded-md col-span-12 sm:col-span-6">
                <h1 class="mt-4 [&&]:mb-2">Operating Systems</h1>
                <ul class="list-disc list-inside grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-2">
                    <li>Windows</li>
                    <li>MacOS</li>
                    <li>Ubuntu</li>
                    <li>CentOS</li>
                    <li>Pop!_OS</li>
                    <li>Arch Linux</li>
                    <li>Debian</li>
                    <li>Kali Linux</li>
                    <li>Linux Mint</li>
                    <li>kUbuntu</li>
                    <li>Android</li>
                    <li>iOS</li>
                    
                </ul>
            </div>
            <div class="w-full border-2 border-white px-6 rounded-md col-span-12 sm:col-span-6">
                <h1 class="mt-4 [&&]:mb-2">Dev Tools</h1>
                <ul class="list-disc list-inside grid grid-cols-2 lg:grid-cols-3">
                    <li>VS Code</li>
                    <li>Visual Studio</li>
                    <li>VI</li>
                    <li>Eclipse</li>
                    <li>NetBeans</li>
                    <li>Git</li>
                    <li>Docker</li>
                    <li>XAMPP</li>
                    <li>Local</li>
                    <li>Make</li>
                    <li>MingW</li>
                    <li>Webpack</li>
                    <li>Vite</li>
                    <li>TMUX</li>
                    <li>NPM</li>
                    <li>Composer</li>
                </ul>
            </div>
            
            <div class="w-full border-2 border-white px-6 rounded-md col-span-12 sm:col-span-6 lg:col-span-4">
                <h1 class="mt-4 [&&]:mb-2">Web Development</h1>
                <ul class="list-disc list-inside grid grid-cols-2">
                    <li>React</li>
                    <li>AngularJS</li>
                    <li>Laravel</li>
                    <li>Tailwind</li>
                    <li>Bootstrap</li>
                    <li>SCSS/SASS</li>
                    <li>jQuery</li>
                    <li>Gulp</li>
                    <li>OpenAI API</li>
                </ul>
            </div>

            
            <div class="w-full border-2 border-white px-6 rounded-md col-span-12 sm:col-span-6 lg:col-span-4">
                <h1 class="mt-4 [&&]:mb-2">Game & Graphics</h1>
                <ul class="list-disc list-inside grid 2xl:grid-cols-2">
                    <li>Blender</li>
                    <li>Fusion 360</li>
                    <li>Gamemaker 7</li>
                    <li>OpenGL</li>
                    <li>DirectX 9</li>
                    <li>Open Dynamics Engine</li>
                    <li>Processing</li>
                    <li>OpenFrameworks</li>
                </ul>
            </div>
            <div class="w-full border-2 border-white px-6 rounded-md col-span-12 sm:col-span-6 lg:col-span-4">
                <h1 class="mt-4 [&&]:mb-2">Design & UI</h1>
                <ul class="list-disc list-inside grid 2xl:grid-cols-2">
                    <li>Figma</li>
                    <li>Adobe XD</li>
                    <li>Photoshop</li>
                    <li>Optimizely</li>
                    <li>WPF</li>
                    <li>HTML Email</li>
                </ul>
            </div>
            <div class="w-full border-2 border-white px-6 rounded-md col-span-12 sm:col-span-6 lg:col-span-4">
                <h1 class="mt-4 [&&]:mb-2">Networking & APIs</h1>
                <ul class="list-disc list-inside grid 2xl:grid-cols-2">
                    <li>Wireshark</li>
                    <li>Postman</li>
                    <li>SSH</li>
                    <li>Sentry</li>
                    <li>Cloudflare</li>
                    <li>DWP</li>
                    <li>Google Maps API</li>
                </ul>
            </div>
            <div class="w-full border-2 border-white px-6 rounded-md col-span-12 sm:col-span-6 lg:col-span-4">
                <h1 class="mt-4 [&&]:mb-2">Infrastructure</h1>
                <ul class="list-disc list-inside grid 2xl:grid-cols-2">
                    <li>Apache</li>
                    <li>VMWare</li>
                    <li>VirtualBox</li>
                    <li>Remote Desktop</li>
                    <li>Hosting</li>
                    <li>Mail Servers</li>
                    <li>DNS Servers</li>
                    <li>Zapier</li>
                </ul>
            </div>
            <div class="w-full border-2 border-white px-6 rounded-md col-span-12 sm:col-span-6 lg:col-span-4">
                <h1 class="mt-4 [&&]:mb-2">Embedded Systems</h1>
                <ul class="list-disc list-inside grid 2xl:grid-cols-2">
                    <li>Raspberry Pi</li>
                    <li>PICAXE Editor</li>
                    <li>Breadboarding</li>
                    <li>Soldering</li>
                    <li>555 Timer</li>
                    <li>Assembly</li>
                </ul>
            </div>
            <div class="w-full border-2 border-white px-6 rounded-md col-span-12 sm:col-span-6 lg:col-span-4">
                <h1 class="mt-4 [&&]:mb-2">CMS & Forms</h1>
                <ul class="list-disc list-inside">
                    <li>WordPress</li>
                    <li>ACF</li>
                    <li>Formidable Forms</li>
                    <li>Ninja Forms</li>
                </ul>
            </div>
           

        </div>
        
            
        

    </x-content>
    @vite('resources/js/home.js')
</x-page>
