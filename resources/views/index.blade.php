<x-page title={{$title}}>
    <x-content class="h-full flex flex-col gap-6 py-6">

        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>

        <div class="w-full">
            <h2 class="text-white mb-4">Hi!</h2>
            <h3 class="text-white mb-3">You've found my site!</h3>
            <h3 class="text-white mb-1">This place is my personal space on the internet where I keep track of and show off my projects :)</h3>
        </div>
        <div class="w-full">
            <h2 class="text-white mb-4">Projects</h2>
            <div class="swiper text-white w-full rounded-md aspect-[704/396] md:aspect-[2704/747] ">
                <div class="
                rounded-lg
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
                    @foreach (\App\Models\Project::all()->shuffle() as $project)
                        @php
                            $imageExists = Storage::disk('public')->exists('projects/'.$project->slug.'/main.png');
                            if(!$imageExists){
                                continue;
                            }
                        @endphp
                        <div class="swiper-slide    h-full w-full [&:not(.swiper-slide-active)]:hidden md:[&:not(.swiper-slide-active,.swiper-slide-next)]:hidden overflow-visible">
                            <x-project :project="$project" :swiper="true" class="xs:inline hidden"/>

                            <a href="/projects/{{ $project->slug }}" class="relative w-full h-full block xs:hidden border-2 border-white transition-colors duration-300 focus-within:border-purple hover:border-purple rounded-md overflow-hidden">
                                <img 
                                    src="{{ asset('storage/projects/'.$project->slug.'/main.png') }}" 
                                    alt="{{ $project->name }}" 
                                    loading="lazy"
                                    class="w-full h-full object-cover">
                                <div class="absolute top-0 bottom-0 left-0 right-0 bg-black/50 hover:bg-transparent hover:opacity-0 transition-all duration-300">
                                    <h2 class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white text-center">{{ $project->name }}</h2>
                                </div>
                                <div class="swiper-lazy-preloader"></div>
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
                                Click to copy alex_8700
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
                <ul class="list-disc list-inside grid grid-cols-2 ">
                    <li>
                        Gaming
                        <x-popover position="top">
                            Mostly Counter Strike, a bit of Minecraft, red alert 2 and battlefield 6. 
                        </x-popover>
                    </li>
                    <li>
                        Coding
                        <x-popover position="top">
                            <p class="max-w-[480px]">
                                My first interaction with coding was in school IT lessons with Scratch, which I didn't much like. Then I took python classes at lunch time but didn't really enjoy that either. My attachment to coding started with C++ as a hobby when I was 16. My first projects (not on this site :( ) were mostly copy and pasting together bits of other people's code until I got things working. I got quite into creating DirectX 9 GUI frameworks as feedback was very visual and rewarding. Over time I made roughly 6 different GUI frameworks in C++, each repitition broadening my development toolset. I eventually went into University to study Computer Science and now I'm a full stack developer with experience in all of these languages:
                            </p>
                            <x-coding-languages />
                        </x-popover>
                    </li>
                    <li>
                        Pool
                        <x-popover position="top">
                            <p>Love it so much I made a game :P</p>
                            <a class="underline" href="{{ route('projects.show', 'pool-snooker-simulator') }}" target="_blank">take a look</a>
                        </x-popover>
                    </li>
                    <li>
                        Electronics
                        <x-popover position="top" >
                            <p class="max-w-[480px]">
                                I have a deep love for electronics; My grandad Graham got me into it when I was very young. Almost every time I visited him as a kid he would let me play around with his electronics components and show me projects he was working on.
                            </p>
                        </x-popover>
                    </li>
                    <li>Sim Racing</li>
                    <li>Driving</li>
                    <li>Fishing</li>
                    <li>Baking</li>
                </ul>
            </x-bubble>
            <x-bubble class="col-span-12 sm:col-span-6 lg:col-span-4">
                <h3>Languages</h3>
                <x-coding-languages />
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
                    <li>Arduino</li>
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
