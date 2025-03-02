@props([
    'name' => null,
    'type' => 'text',
    'files' => [],
    'file' => null,
])

<div class="flex flex-col gap-1">
    <label for="{{ $name }}" class=" text-sm/6 font-medium text-white">{{ $slot }}</label>

    <x-bubble
        class="{{ $type == 'file' ? 'relative h-24 max-h-24 overflow-hidden group' : '' }} rounded-md bg-transparent [&]:px-0 [&]:py-0">

        @if($type == 'file')
            <div class="absolute top-2 left-3 w-full h-full pointer-events-none text-white text-sm/6 choose-file opacity-100 group-[:has(.file-uploads_img)]:opacity-0 transition-opacity">
                Choose file...
            </div>
            <div class="absolute w-full h-full pointer-events-none text-white text-sm/6 file-uploads flex items-center justify-center">      
                @if($file)
                    <img data-file-id="{{ $file }}" src="{{ $file }}" class="pointer-events-none w-full h-full object-cover">
                @elseif($files)
                    @foreach($files as $index => $file)  
                        <img data-file-id="{{ $index }}" src="{{ $file }}" class="pointer-events-none w-full h-full object-cover">
                    @endforeach
                @endif

            </div>
        @endif

        @if($type == 'textarea')
            <textarea name="{{ $name }}"  {{ $attributes->merge(['class' => 'block min-w-0 w-full px-3 py-2 text-white text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6 h-[92px]']) }}>{{ $attributes->get('value') }}</textarea>
        @else
            <input name="{{ $name }}" type="{{ $type }}" {{ $attributes->merge(['class' => ($type == 'file' ? 'opacity-0' : '') . ' block min-w-0 w-full h-full px-3 py-2 text-white text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6']) }}/>
        @endif
    </x-bubble>
    @error($name)
        <p class="text-red-500">{{ $message }}</p>
    @enderror
</div>
