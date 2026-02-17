<x-page :title="$title">
    <x-content type="wide" class="py-6">
        
        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>
        
        <x-form method="POST" action="{{ route('contact.store') }}">
            <x-input>Email</x-input>
            <x-input>Message</x-input>
            <x-button>Send</x-button>
        </x-form>
    </x-content>
</x-page>
