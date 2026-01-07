<x-page :title="$title">
    <x-content type="wide" class="py-6">
        
        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>
        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($projects as $project)
                <x-project :project="$project"/>
            @endforeach
        </div>
    </x-content>
</x-page>
