<x-page :title="$title">
    <x-content class="h-full flex flex-col gap-6 py-6">

        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>

        <x-form  action="{{ route('register') }}" method="POST">
            @csrf
            <x-input type="text" name="name" >
                Name
            </x-input>
            <x-input type="email" name="email" >
                Email
            </x-input>
            <x-input type="password" name="password" >
                Password
            </x-input>
            <x-input type="password" name="password_confirmation" >
                Confirm Password
            </x-input>
            <x-button type="submit">Sign Up</x-button>
        </x-form>
    </x-content>
</x-page>