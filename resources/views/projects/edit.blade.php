<x-page :title="$title">
    <x-content type="wide" class="py-6">
        <x-form action="{{ route('projects.update', $project->slug) }}" method="PUT">

            <x-input name="name" value="{{ old('name', $project->name) }}" >
                Name
            </x-input>
            <x-input name="description" type="textarea" value="{{ old('description', $project->escapedDescription()) }}" >
                Description
            </x-input>
            <x-input type="file" name="main_image" file="{{ asset('storage/projects/'.$project->slug.'/main.png') }}" value="1" >
                Image
            </x-input>

            <x-input type="file" name="images[]" :files="[asset('storage/projects/'.$project->slug.'/main.png'), asset('storage/projects/'.$project->slug.'/main.png')]" multiple>
                Images
            </x-input>

            <x-input name="link" value="{{ old('link', $project->link) }}" >
                Link
            </x-input>
            <x-button type="submit">Update</x-button>
        </x-form>
    </x-content>
</x-page>
