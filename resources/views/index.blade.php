<x-page :title="$title" :heading="$heading ?? null" :description="$description ?? null" og-type="profile">
    <x-content class="h-full flex flex-col gap-6 py-6">

        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>

        <div class="w-full flex flex-col gap-3 text-white/70 max-w-2xl [&_a]:underline [&_a:hover]:text-white">
            <h2 class="text-white mb-1">Hi, I'm Alex Davies :)</h2>
            <p>I'm a full-stack software developer in Stoke-on-Trent. I have a First in Computer Science from Keele University, and since 2022 I've worked at Designamite, where I lead development for FibreNest, Persimmon Homes' fibre broadband business. On the side I build websites and online shops for local businesses through <a href="https://21cs.co.uk" target="_blank" rel="noopener">21cs</a>.</p>
            <p>This is my corner of the internet, where I keep track of and show off my projects: web apps, game engines, simulators, embedded electronics and automation tools in PHP, JavaScript, Python and C++. You can also <a href="/cv">read my CV</a> or <a href="/contact">get in touch</a>.</p>
        </div>
        <div class="w-full">
            <h2 class="text-white mb-4">Projects</h2>
            <div class="projects-carousel swiper text-white w-full rounded-md aspect-[704/396] md:aspect-[2704/747] ">
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
                    @foreach (\App\Models\Project::with('images')->get()->filter(fn ($p) => $p->images->isNotEmpty())->shuffle()->values() as $project)
                        @php
                            $primaryImage = $project->images->first();
                            $img = \App\Support\ResponsiveImage::attributes('projects/'.$project->slug.'/'.$primaryImage->filename);
                            // The first slide is the LCP image: fetch it straight away. Both layouts
                            // below use the same sizes, so they resolve to one download.
                            $first = $loop->first;
                        @endphp
                        {{-- The first two slides start out marked active/next (Swiper keeps these classes up to date
                             once it starts), so they show before the JavaScript runs. app.css sizes them the way Swiper will. --}}
                        <div class="swiper-slide {{ $loop->first ? 'swiper-slide-active' : '' }} {{ $loop->index === 1 ? 'swiper-slide-next' : '' }} h-full w-full [&:not(.swiper-slide-active)]:hidden md:[&:not(.swiper-slide-active,.swiper-slide-next)]:hidden overflow-visible">
                            <x-project :project="$project" :eager="$first" sizes="(min-width: 768px) 50vw, calc(100vw - 3rem)" class="xs:inline hidden"/>

                            <a href="/projects/{{ $project->slug }}" class="relative w-full h-full block xs:hidden border-2 border-white transition-colors duration-300 focus-within:border-purple hover:border-purple rounded-md overflow-hidden">
                                <img
                                    src="{{ $img['src'] }}"
                                    @if($img['srcset']) srcset="{{ $img['srcset'] }}" sizes="(min-width: 768px) 50vw, calc(100vw - 3rem)" @endif
                                    alt="{{ $project->name }}"
                                    @if($first) loading="eager" fetchpriority="high" @else loading="lazy" @endif
                                    decoding="async"
                                    class="w-full h-full object-cover">
                                <div class="absolute top-0 bottom-0 left-0 right-0 bg-black/50 hover:bg-transparent hover:opacity-0 transition-all duration-300">
                                    <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-white text-center text-xl font-semibold">{{ $project->name }}</span>
                                </div>
                                {{-- No spinner on the eager first slide: it can finish loading before Swiper
                                     starts listening, and the spinner would never be cleared. --}}
                                @unless($first)
                                    <div class="swiper-lazy-preloader"></div>
                                @endunless
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <h2 class="sr-only">About me &amp; skills</h2>
        <div class="grid grid-cols-12 gap-6">
            <x-bubble class="col-span-12 sm:col-span-6 lg:col-span-4">
                <h3>
                    Socials
                </h3>
                {{-- inline-block + padding gives each link a 24px+ tap target (PageSpeed: touch targets). --}}
                <ul class="list-disc list-inside [&_a]:inline-block [&_a]:py-1 [&_button]:inline-block [&_button]:py-1">
                    <li><button type="button" class="underline cursor-pointer copy-value text-left" data-value="alex_8700" aria-label="Copy Discord username alex_8700">Discord
                            <x-popover position="bottom">
                                Click to copy alex_8700
                            </x-popover>
                        </button>
                    </li>
                    <li><a class="underline" href="https://steamcommunity.com/id/a-l-ex" target="_blank" rel="noopener noreferrer">Steam</a></li>
                    <li><a class="underline" href="https://github.com/Sinclair8700" target="_blank" rel="noopener noreferrer">Github</a></li>
                    <li><a class="underline" href="https://www.linkedin.com/in/alex-davies-aa10a7215/" target="_blank" rel="noopener noreferrer">LinkedIn</a></li>
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
