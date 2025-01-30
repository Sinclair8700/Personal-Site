@props([
    'name' => null,
])

<div>
    <label for="salary" class="block text-sm/6 font-medium text-gray-900">{{ $slot }}</label>

    <div
        class="flex items-center rounded-md bg-white pl-3 outline outline-1 -outline-offset-1 outline-gray-300 focus-within:outline focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600">
        <input name="{{ $name }}"
            {{ $attributes->merge(['class' => 'block min-w-0 grow py-1.5 pl-1 pr-3 text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6']) }}>

    </div>
    @error($name)
        <p class="text-red-500">{{ $message }}</p>
    @enderror
</div>
