<x-page title={{$title}}>
    <x-content type="wide" class="py-6">
        
        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($projects as $project)
                <a href="/projects/{{ $project->slug }}">
                    {{-- <x-bubble class="max-w-full bg-black ring-2 ring-white transition duration-300 focus-within:ring-purple hover:ring-purple rounded-lg flex flex-col xs:flex-row h-full xs:aspect-[2/1]"> --}}
                    <x-bubble class="max-w-full flex flex-col xs:flex-row h-full xs:aspect-[2/1] p-0">
                        <div class="order-2 xs:order-first w-full xs:w-1/2 px-4 py-3 flex flex-col min-h-0">
                            <h3 class="text-white mb-1">{{ $project->name }}</h3>
                            <p class="text-sm max-h-[280px] text-white hyphens-auto overflow-y-auto min-h-0 h-full flex-1 scrollbar-thumb-rounded-full scrollbar-track-rounded-full scrollbar scrollbar-w-[10px] scrollbar-thumb-slate-700 scrollbar-track-transparent">
                                {{ $project->description }}</p>
                        </div>

                        <div class="aspect-square w-full xs:w-1/2  ">
                            <img src="{{ asset('storage/projects/' . $project->slug . '/main.png') }}"
                                alt="{{ $project->name }}"
                                class="w-full h-full object-cover rounded-t-lg xs:rounded-t-none sm:rounded-r-lg">
                        </div>
                    </x-bubble>
                </a>
            @endforeach
        </div>
    </x-content>
</x-page>
