<x-page title={{$title}}>
    <x-content type="wide">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($projects as $project)
                <a href="/projects/{{ $project->slug }}">
                <div class="bg-black ring-1 ring-white rounded-lg flex flex-col xs:flex-row h-full">
                    <div class="order-2 xs:order-first w-full xs:w-1/2 p-4 grow-0">
                        <h2 class="text-lg font-bold">{{ $project->name }}</h2>




                        <p class="inline-block text-sm text-gray-500 hyphens-auto whitespace-normal overflow-y-auto text-ellipsis flex-[1_2_auto]">{{ $project->description }}</p>
                    </div>




                    <div class="aspect-square w-full xs:w-1/2  ">
                        <img src="{{ file_exists(base_path('resources/views/projects/projects/'.$project->slug.'/main.png')) ? Vite::asset('resources/views/projects/projects/'.$project->slug.'/main.png') : '' }}" alt="{{ $project->name }}" class="w-full h-full object-cover rounded-t-lg xs:rounded-t-none sm:rounded-r-lg">
                    </div>
                </div>


            @endforeach


        </div>
    </x-content>
</x-page>