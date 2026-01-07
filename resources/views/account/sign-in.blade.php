<x-page :title="$title">
    <x-content class="h-full flex flex-col gap-6 py-6">

        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <x-input type="email" name="email" >
                Email
            </x-input>
            <x-input type="password" name="password" >
                Password
            </x-input>
            <x-button type="submit">Sign Up</x-button>
        </form>
    </x-content>
</x-page>