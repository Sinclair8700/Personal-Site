<x-page :title="$title" :noindex="true">
    <x-content class="h-full flex flex-col gap-6 py-6">

        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>

        <x-form  action="{{ route('register') }}" method="POST">
            @csrf
            <x-input type="text" name="name" autocomplete="name" >
                Name
            </x-input>
            <x-input type="email" name="email" autocomplete="email" >
                Email
            </x-input>
            <x-input type="password" name="password" autocomplete="new-password" >
                Password
            </x-input>
            <x-input type="password" name="password_confirmation" autocomplete="new-password" >
                Confirm Password
            </x-input>
            <x-button type="submit">Sign Up</x-button>
        </x-form>
    </x-content>
</x-page>