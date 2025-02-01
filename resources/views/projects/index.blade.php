<x-page title={{$title}}>
    <x-content>
        <div class="flex flex-col gap-4">
            @foreach ($projects as $project)
                {{ $project->name }}
            @endforeach
        </div>
    </x-content>
</x-page>