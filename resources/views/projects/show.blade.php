<x-page :title="$title">
    <x-content class="py-6">
        @if(session('warning'))
            <x-bubble class="mb-4 border-l-4 border-amber-500">
                <p class="text-amber-200">{{ session('warning') }}</p>
            </x-bubble>
        @endif
        <div class="text-white">
            @if($project->hasImages())
                <div class="w-full md:w-1/2 lg:w-1/3 float-left mr-6 mb-4 space-y-4">
                    @foreach($project->images as $image)
                        <img src="{{ asset('storage/projects/'.$project->slug.'/'.$image->filename) }}"
                            alt="{{ $project->name }}"
                            class="w-full h-auto object-cover rounded-lg">
                    @endforeach
                </div>
            @endif
            <p class="text-justify">{{ $project->description }}</p>
            <div class="clear-both"></div>
        </div>

        @php
            try {
                echo view('projects.projects.' . $project->slug . '.index');
            } catch (\Exception $e) {
                if(auth()->check() && auth()->user()->is_admin){
                    echo "<h1>Project does not have a view in the projects/projects folder with the slug: " . $project->slug . "</h1>";
                }
            }
        @endphp
    </x-content>
</x-page>
