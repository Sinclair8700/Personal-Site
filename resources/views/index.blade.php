<x-page title={{$title}}>
    <x-content class="h-full flex flex-col gap-6">
        <div class="w-full">
            <h1 class="text-white text-[24px] leading-[32px] mb-4">Welcome!</h1>
            <h2 class="text-white text-[18px] leading-[24px] mb-3">I'm Alex, a software engineer based in The United Kingdom.</h2>
            <h2 class="text-white text-[18px] leading-[24px] mb-1">This place is my personal space on the internet where you can find my projects, thoughts and other stuff.</h2>
        </div>
        <div class="w-full">
            <h1 class="text-white text-[24px] leading-[32px] mb-4">Projects</h1>
            <div class="swiper text-white w-full rounded-md aspect-[704/396] md:aspect-[2704/747] ">
                <div class="
                rounded-md
                swiper-wrapper 
                [&_.swiper-slide]:w-full 
                [&_.swiper-slide]:h-full
                [&_.swiper-slide]:min-h-full
                [&_.swiper-slide]:aspect-video
                [&_.swiper-slide]:shrink-0
                [&_.swiper-slide]:bg-transparent
                
                [&_.swiper-slide]:overflow-hidden
                [&_.swiper-slide]:shadow-md
                [&_.swiper-slide]:duration-300
                [&_.swiper-slide]:ease-in-out
                ">
                    @foreach (\App\Models\Project::all() as $project)
                        @php
                            $imageExists = Storage::disk('public')->exists('projects/'.$project->slug.'/main.png');
                            if(!$imageExists){
                                continue;
                            }
                        @endphp
                        <div class="swiper-slide    h-full w-full [&:not(.swiper-slide-active)]:hidden md:[&:not(.swiper-slide-active,.swiper-slide-next)]:hidden">
                            <a href="/projects/{{ $project->slug }}" class="relative w-full h-full block border-2 border-white transition-colors duration-300 focus-within:border-purple hover:border-purple rounded-md overflow-hidden">
                                <img 
                                    src="{{ asset('storage/projects/'.$project->slug.'/main.png') }}" 
                                    alt="{{ $project->name }}" 
                                    loading="lazy"
                                    class="w-full h-full object-cover">
                                <div class="absolute top-0 bottom-0 left-0 right-0 bg-black/50 hover:bg-transparent hover:opacity-0 transition-all duration-300">
                                    <h2 class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white text-2xl font-bold text-center">{{ $project->name }}</h2>
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
        <!--<div class="">
            <h1 class="mt-4 mb-2!">Bio</h1>
            <p>
                I'm a software engineer based in the UK. I'm currently studying Computer Science at the University of York.
            </p>
        </div>-->
        <div class="grid grid-cols-12 gap-6">
            <x-bubble class="col-span-12 sm:col-span-6 lg:col-span-4">
                <h3>
                    Socials
                </h3>
                <ul class="list-disc list-inside">
                    <li><span class="underline cursor-pointer copy-value" data-value="alex_8700">Discord
                            <x-popover position="bottom">
                                Click to copy tag
                            </x-popover>
                        </span>
                    </li>
                    <li><a class="underline" href="https://steamcommunity.com/id/a-l-ex" target="_blank">Steam</a></li>
                    <li><a class="underline" href="https://github.com/Sinclair8700" target="_blank">Github</a></li>
                    <li><a class="underline" href="https://www.linkedin.com/in/alex-davies-aa10a7215/" target="_blank">LinkedIn</a></li>
                </ul>
            </x-bubble>
            <x-bubble class="col-span-12 sm:col-span-6 lg:col-span-4">
                <h3>Hobbies</h3>
                <ul class="list-disc list-inside">
                    <li>Gaming</li>
                    <li>Coding</li>
                    <li>Pool</li>
                    <li>Sim Racing</li>
                </ul>
            </x-bubble>
            <x-bubble class="col-span-12 sm:col-span-6 lg:col-span-4">
                <h3>Languages</h3>
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
            </x-bubble>
            <x-bubble class="col-span-12 sm:col-span-6">
                <h3>Operating Systems</h3>
                <ul class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-2">
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
            </x-bubble>
            <x-bubble class="col-span-12 sm:col-span-6">
                <h3>Dev Tools</h3>
                <ul class="grid grid-cols-2 lg:grid-cols-3">
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
            </x-bubble>
            
            <x-bubble class=" col-span-12 sm:col-span-6 lg:col-span-4">
                <h3>Web Development</h3>
                <ul class="grid grid-cols-2">
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
            </x-bubble>

            
            <x-bubble class=" col-span-12 sm:col-span-6 lg:col-span-4">
                <h3>Game & Graphics</h3>
                <ul class="grid 2xl:grid-cols-2">
                    <li>Blender</li>
                    <li>Fusion 360</li>
                    <li>Gamemaker 7</li>
                    <li>OpenGL</li>
                    <li>DirectX 9</li>
                    <li>Open Dynamics Engine</li>
                    <li>Processing</li>
                    <li>OpenFrameworks</li>
                </ul>
            </x-bubble>
            <x-bubble class=" col-span-12 sm:col-span-6 lg:col-span-4">
                <h3>Design & UI</h3>
                <ul class="grid 2xl:grid-cols-2">
                    <li>Figma</li>
                    <li>Adobe XD</li>
                    <li>Photoshop</li>
                    <li>Optimizely</li>
                    <li>WPF</li>
                    <li>HTML Email</li>
                </ul>
            </x-bubble>
            <x-bubble class=" col-span-12 sm:col-span-6 lg:col-span-4">
                <h3>Networking & APIs</h3>
                <ul class="grid 2xl:grid-cols-2">
                    <li>Wireshark</li>
                    <li>Postman</li>
                    <li>SSH</li>
                    <li>Sentry</li>
                    <li>Cloudflare</li>
                    <li>DWP</li>
                    <li>Google Maps API</li>
                </ul>
            </x-bubble>
            <x-bubble class=" col-span-12 sm:col-span-6 lg:col-span-4">
                <h3>Infrastructure</h3>
                <ul class="grid 2xl:grid-cols-2">
                    <li>Apache</li>
                    <li>VMWare</li>
                    <li>VirtualBox</li>
                    <li>Remote Desktop</li>
                    <li>Hosting</li>
                    <li>Mail Servers</li>
                    <li>DNS Servers</li>
                    <li>Zapier</li>
                </ul>
            </x-bubble>
            <x-bubble class=" col-span-12 sm:col-span-6 lg:col-span-4">
                <h3>Embedded Systems</h3>
                <ul class="grid 2xl:grid-cols-2">
                    <li>Raspberry Pi</li>
                    <li>PICAXE Editor</li>
                    <li>Breadboarding</li>
                    <li>Soldering</li>
                    <li>555 Timer</li>
                    <li>Assembly</li>
                </ul>
            </x-bubble>
            <x-bubble class=" col-span-12 sm:col-span-6 lg:col-span-4">
                <h3>CMS & Forms</h3>
                <ul>
                    <li>WordPress</li>
                    <li>ACF</li>
                    <li>Formidable Forms</li>
                    <li>Ninja Forms</li>
                </ul>
            </x-bubble>
           

        </div>
        
            
        

    </x-content>
    @vite('resources/js/home.js')
</x-page>
