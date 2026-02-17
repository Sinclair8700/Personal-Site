<x-page :title="$title">
    <x-content type="wide" class="py-6">
        @if(session('success'))
            <x-bubble class="mb-4 border-l-4 border-emerald-500">
                <p class="text-emerald-200">{{ session('success') }}</p>
            </x-bubble>
        @endif
        
        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>
        
        <x-form method="POST" action="{{ route('contact.store') }}">
            <x-input name="email_address" type="text">Email</x-input>
            <x-input name="message" type="textarea">Message</x-input>
            <x-button>Send</x-button>
        </x-form>
    </x-content>
</x-page>
