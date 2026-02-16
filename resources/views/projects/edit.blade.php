<x-page :title="$title">
    <x-content type="wide" class="py-6">
        @if(session('warning'))
            <x-bubble class="mb-4 border-l-4 border-amber-500">
                <p class="text-amber-200">{{ session('warning') }}</p>
            </x-bubble>
        @endif
        <x-form action="{{ route('projects.update', $project) }}" method="PUT" enctype="multipart/form-data">

            <x-input name="name" value="{{ old('name', $project->name) }}" >
                Name
            </x-input>
            <x-input name="description" type="textarea" value="{{ old('description', $project->description) }}" >
                Description
            </x-input>

            <x-input type="file" name="images[]" multiple
                :files="$project->images->map(fn($img) => asset('storage/projects/'.$project->slug.'/'.$img->filename))->values()->all()">
                Images (upload order = display priority)
            </x-input>

            @if($project->images->isNotEmpty())
                <div class="flex flex-col gap-2">
                    <label class="text-sm/6 font-medium text-white">Remove images</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($project->images as $image)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="rounded">
                                <img src="{{ asset('storage/projects/'.$project->slug.'/'.$image->filename) }}" alt="" class="w-16 h-16 object-cover rounded">
                                <span class="text-white text-sm">Remove</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <x-input name="link" value="{{ old('link', $project->link) }}" >
                Link
            </x-input>
            <x-button type="submit">Update</x-button>
        </x-form>
    </x-content>
</x-page>
