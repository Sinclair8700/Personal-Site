<x-page title={{$title}}>
    <x-content type="wide" class="py-6">
        <x-form action="{{ route('projects.store') }}" method="POST">
            @csrf

            <x-input name="name" value="{{ old('name') }}" >
                Name
            </x-input>
            @error('name')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror

            <x-input name="description" type="textarea" value="{{ old('description') }}" >
                Description
            </x-input>
            @error('description')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror

            <x-input type="file" name="main_image" value="1" :file="old('main_image')" >
                Image
            </x-input>
            @error('main_image')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror

            <x-input type="file" name="images[]" :files="old('images')" multiple>
                Images
            </x-input>

            <x-input name="link" value="{{ old('link') }}" >
                Link
            </x-input>
            @error('link')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror

            <x-button type="submit">Create</x-button>
        </x-form>
    </x-content>
</x-page>
