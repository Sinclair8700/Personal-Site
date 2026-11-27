<x-page :title="$title" :description="$description ?? null">
    <x-content type="wide" class="py-6">

        <x-slot name="leftGutter" class="snow">
        </x-slot>
        <x-slot name="rightGutter" class="snow">
        </x-slot>

        <x-form method="POST" action="{{ route('contact.store') }}" data-contact-challenge>
            <x-input name="email_address" type="email" autocomplete="email" value="{{ old('email_address') }}">Email</x-input>
            <x-input name="message" type="textarea" value="{{ old('message') }}">Message</x-input>

            {{-- Honeypot: hidden from people; bots that auto-fill every field trip it --}}
            <div style="position:absolute; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden;" aria-hidden="true">
                <label for="website">Website</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off" value="">
            </div>

            {{-- Bot check: resources/js/contact-challenge.js solves this puzzle and then enables Send (see App\Support\ContactChallenge) --}}
            <input type="hidden" name="challenge_salt" value="{{ $challenge['salt'] }}">
            <input type="hidden" name="challenge" value="{{ $challenge['challenge'] }}">
            <input type="hidden" name="challenge_signature" value="{{ $challenge['signature'] }}">
            <input type="hidden" name="challenge_number" value="" data-challenge-max="{{ $challenge['max'] }}">

            @error('challenge')
                <p class="text-red-500" role="alert">{{ $message }}</p>
            @enderror

            <div class="flex items-center gap-4">
                <x-button type="submit" disabled data-challenge-submit class="disabled:opacity-50 disabled:cursor-not-allowed">Send</x-button>
                <p class="text-sm text-white/60" data-challenge-status role="status" aria-live="polite">
                    <noscript>JavaScript is needed to send this form.</noscript>
                </p>
            </div>
        </x-form>
    </x-content>
</x-page>
