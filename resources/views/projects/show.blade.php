<x-page title={{$title}}>
    <x-content>
        @php
            try {
                echo view('projects.projects.' . $project->slug . '.index');
            } catch (\Exception $e) {
                if(auth()->check() && auth()->user()->is_admin){
                    echo "<h1>Project does not have a view in the projects/projects folder with the slug: " . $project->slug . "</h1>";
                }else{
                    echo "<h1>Project details not found</h1>";
                }
            }
        @endphp
    </x-content>
</x-page>
