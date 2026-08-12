<x-page :title="$title" :description="$description ?? null">
    <x-content type="wide" class="py-6">
        
        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>
        
        <x-form method="POST" action="{{ route('contact.store') }}">
            <x-input name="email_address" type="text">Email</x-input>
            <x-input name="message" type="textarea">Message</x-input>

            {{-- Honeypot: hidden from people; bots that auto-fill every field trip it --}}
            <div style="position:absolute; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden;" aria-hidden="true">
                <label for="website">Website</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off" value="">
            </div>

            <x-button>Send</x-button>
        </x-form>
    </x-content>
</x-page>
