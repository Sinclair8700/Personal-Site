{{-- Admin editor for the CV at /cv. Saves to the cvs table via CvController@update. --}}
@php
    $field = fn ($key, $default = '') => old($key, $cv->field($key, $default));
    $input = 'block w-full rounded-md border border-white/15 bg-white/5 px-3 py-2 text-sm text-white placeholder:text-white/30 focus:border-indigo-400 focus:outline-none';
    $area = $input.' font-mono leading-6';
    $label = 'text-sm font-medium text-white';
    $help = 'text-xs text-white/50';
    $sections = [
        'profile' => ['Profile', 8, 'Paragraphs separated by a blank line. Markdown links work: [text](https://…).'],
        'skills' => ['Skills', 9, 'One group per line: "Area: item, item, item". Commas inside brackets stay with their item.'],
        'experience' => ['Experience', 24, 'Each job starts with "### Role — Company", then a line with the dates, then a blank line and the text or "- " bullets. **bold** and links work.'],
        'projects' => ['Selected projects', 18, 'Optional intro line, then each project as "### [Name](link)" followed by a short description.'],
        'education' => ['Education', 12, 'Same as experience: "### Course — Place", a dates line, then bullets.'],
        'interests' => ['Interests', 4, 'A short paragraph.'],
    ];
@endphp
<x-page :title="$title" :noindex="true">
    <x-content type="wide" class="py-6 text-white">
        <div class="mb-6 flex flex-wrap items-center gap-3">
            <a href="{{ route('cv.show') }}" target="_blank" rel="noopener" class="rounded-full border border-white/20 px-4 py-1.5 text-sm hover:border-indigo-400">View CV</a>
            <a href="{{ route('cv.markdown') }}" class="rounded-full border border-white/20 px-4 py-1.5 text-sm hover:border-indigo-400">Download Markdown</a>
            <span class="text-sm text-white/50">To export a PDF, open the CV and press "Save as PDF".</span>
        </div>

        <form method="POST" action="{{ route('cv.update') }}" class="flex max-w-5xl flex-col gap-8">
            @csrf
            @method('PUT')

            <fieldset class="grid gap-4 rounded-2xl border border-white/10 p-5 md:grid-cols-2">
                <legend class="px-2 text-sm font-semibold uppercase tracking-wider text-indigo-300">Header</legend>
                @foreach([
                    'name' => 'Name', 'headline' => 'Headline', 'location' => 'Location', 'email' => 'Email',
                    'phone' => 'Phone', 'website' => 'Website', 'github' => 'GitHub', 'extra' => 'Extra (e.g. driving licence)',
                ] as $key => $text)
                    <div class="flex flex-col gap-1">
                        <label for="cv-{{ $key }}" class="{{ $label }}">{{ $text }}</label>
                        <input id="cv-{{ $key }}" name="{{ $key }}" value="{{ $field($key) }}" class="{{ $input }}"
                            @if($key === 'name') required @endif
                            @if($key === 'email') type="email" @elseif(in_array($key, ['website', 'github'])) type="url" @else type="text" @endif
                            @error($key) aria-invalid="true" @enderror>
                        @error($key)<p class="text-sm text-red-400">{{ $message }}</p>@enderror
                    </div>
                @endforeach
                <label class="flex items-center gap-2 text-sm md:col-span-2">
                    <input type="checkbox" name="show_phone" value="1" class="size-4 rounded" @checked(old('show_phone', $cv->field('show_phone', false)))>
                    Show the phone number to visitors. When it's off, only you see it, so a PDF you save while signed in still includes it.
                </label>
            </fieldset>

            @foreach($sections as $key => [$text, $rows, $hint])
                <div class="flex flex-col gap-1.5">
                    <label for="cv-{{ $key }}" class="{{ $label }}">{{ $text }}</label>
                    <p id="cv-{{ $key }}-help" class="{{ $help }}">{{ $hint }}</p>
                    <textarea id="cv-{{ $key }}" name="{{ $key }}" rows="{{ $rows }}" aria-describedby="cv-{{ $key }}-help" class="{{ $area }}" spellcheck="true">{{ $field($key) }}</textarea>
                    @error($key)<p class="text-sm text-red-400">{{ $message }}</p>@enderror
                </div>
            @endforeach

            <div class="sticky bottom-0 -mx-2 flex items-center gap-3 border-t border-white/10 bg-black/90 px-2 py-3 backdrop-blur">
                <x-button type="submit">Save CV</x-button>
                <span class="text-sm text-white/50">Changes show on /cv straight away.</span>
            </div>
        </form>
    </x-content>
</x-page>
