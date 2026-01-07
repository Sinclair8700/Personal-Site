<x-page :title="$title">
    <x-content class="h-full flex flex-col gap-6 py-6">

        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <x-input type="text" name="email" placeholder="Email"/>
            <x-input type="password" name="password" placeholder="Password"/>
            <button type="submit">Sign In</button>
        </form>
    </x-content>
</x-page>