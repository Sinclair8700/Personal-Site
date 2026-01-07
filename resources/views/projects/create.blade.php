<x-page :title="$title">
    <x-content type="wide" class="py-6">
        <x-form action="{{ route('projects.store') }}" method="POST">

            <x-input name="name" value="{{ old('name') }}" >
                Name
            </x-input>

            <x-input name="description" type="textarea" value="{{ old('description') }}" >
                Description
            </x-input>

            <x-input type="file" name="main_image" value="1" :file="old('main_image')" >
                Image
            </x-input>

            <x-input type="file" name="images[]" :files="old('images')" multiple>
                Images
            </x-input>

            <x-input name="link" value="{{ old('link') }}" >
                Link
            </x-input>

            <x-button type="submit">Create</x-button>
        </x-form>
    </x-content>
</x-page>
