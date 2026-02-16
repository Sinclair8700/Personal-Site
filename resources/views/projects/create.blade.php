<x-page :title="$title">
    <x-content type="wide" class="py-6">
        <x-form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" data-native-submit>

            <x-input name="name" value="{{ old('name') }}" >
                Name
            </x-input>

            <x-input name="description" type="textarea" value="{{ old('description') }}" >
                Description
            </x-input>

            <x-input type="file" name="images[]" multiple :files="[]">
                Images (at least one required, order = display priority)
            </x-input>

            <x-input name="link" value="{{ old('link') }}" >
                Link
            </x-input>

            <x-button type="submit">Create</x-button>
        </x-form>
    </x-content>
</x-page>
