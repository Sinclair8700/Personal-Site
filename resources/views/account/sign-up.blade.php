<x-page :title="$title">
    <x-content class="h-full flex flex-col gap-6 py-6">

        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <x-input type="text" name="name" placeholder="Name"/>
            <x-input type="email" name="email" placeholder="Email"/>
            <x-input type="password" name="password" placeholder="Password"/>
            <x-input type="password" name="password_confirmation" placeholder="Confirm Password"/>
            <button type="submit">Sign Up</button>
        </form>
    </x-content>
</x-page>