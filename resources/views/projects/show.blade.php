<x-page :title="$title">
    <x-content class="py-6">
        <div class="text-white">
            <div class="w-full md:w-1/2 lg:w-1/3 float-left mr-6 mb-4">
                <img src="{{ asset('storage/projects/' . $project->slug . '/main.png') }}"
                    alt="{{ $project->name }}"
                    class="w-full h-auto object-cover rounded-lg">
            </div>
            <p class="text-justify">{{ $project->escapedDescription }}</p>
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
