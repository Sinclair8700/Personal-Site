{{-- The CV at /cv. Content comes from the cvs table (edit at /cv/edit). Print styles in app.css
     (.cv-page) turn this into a clean A4 layout for "Save as PDF". --}}
@use('App\Models\Cv')
@php
    $host = fn ($url) => preg_replace('#^https?://(www\.)?#', '', rtrim((string) $url, '/'));
    $icons = [
        'pin' => 'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z',
        'mail' => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75',
        'phone' => 'M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z',
        'globe' => 'M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418',
        'code' => 'M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5',
        'check' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    ];
    $contact = array_values(array_filter([
        $cv->field('location') ? ['icon' => 'pin', 'text' => $cv->field('location')] : null,
        $cv->field('email') ? ['icon' => 'mail', 'text' => $cv->field('email'), 'href' => 'mailto:'.$cv->field('email')] : null,
        $showPhone ? ['icon' => 'phone', 'text' => $cv->field('phone'), 'href' => 'tel:'.preg_replace('/[^\d+]/', '', $cv->field('phone'))] : null,
        $cv->field('website') ? ['icon' => 'globe', 'text' => $host($cv->field('website')), 'href' => $cv->field('website')] : null,
        $cv->field('github') ? ['icon' => 'code', 'text' => $host($cv->field('github')), 'href' => $cv->field('github')] : null,
        $cv->field('extra') ? ['icon' => 'check', 'text' => $cv->field('extra')] : null,
    ]));
    $experience = $cv->entries('experience');
    $projects = $cv->entries('projects', false);
    $education = $cv->entries('education');
    $skills = $cv->skillGroups();
@endphp
<x-page :title="$title" :heading="$heading ?? null" :description="$description ?? null" og-type="profile">
    <div class="cv-page text-white">
        <x-content type="medium" class="py-10 print:py-0">

            {{-- Header --}}
            <section class="cv-hero relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-950 via-black to-black p-7 md:p-10">
                <div aria-hidden="true" class="cv-blob pointer-events-none absolute -top-28 -right-20 size-80 rounded-full bg-indigo-600/30 blur-3xl"></div>
                <div aria-hidden="true" class="cv-blob pointer-events-none absolute -bottom-32 left-1/3 size-72 rounded-full bg-fuchsia-600/10 blur-3xl"></div>

                <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="cv-accent text-xs font-semibold uppercase tracking-[0.25em] text-indigo-300">Curriculum vitae</p>
                        <p class="cv-name mt-2 bg-gradient-to-r from-white via-white to-indigo-300 bg-clip-text text-4xl font-bold tracking-tight text-transparent md:text-6xl">{{ $cv->field('name') }}</p>
                        @if($cv->field('headline'))
                            <p class="mt-2 text-lg text-white/80 md:text-xl">{{ $cv->field('headline') }}</p>
                        @endif
                    </div>

                    <div class="cv-actions flex flex-wrap gap-2">
                        <button type="button" onclick="window.print()"
                            class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 transition hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Save as PDF
                        </button>
                        <a href="{{ route('cv.markdown') }}"
                            class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white/90 transition hover:border-indigo-400 hover:text-white">
                            Markdown
                        </a>
                        @if($isAdmin)
                            <a href="{{ route('cv.edit') }}"
                                class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white/90 transition hover:border-indigo-400 hover:text-white">
                                Edit
                            </a>
                        @endif
                    </div>
                </div>

                @if($contact)
                    <ul class="cv-contact relative mt-7 flex flex-wrap gap-2" aria-label="Contact details">
                        @foreach($contact as $item)
                            <li>
                                @if(!empty($item['href']))<a href="{{ $item['href'] }}" @if(str_starts_with($item['href'], 'http')) target="_blank" rel="noopener" @endif @else<span @endif
                                    class="cv-chip inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-sm text-white/85 transition hover:border-indigo-400/60 hover:text-white">
                                    <svg class="size-4 shrink-0 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}" /></svg>
                                    {{ $item['text'] }}
                                @if(!empty($item['href']))</a>@else</span>@endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <div class="cv-grid mt-10 grid gap-10 lg:grid-cols-[minmax(0,1fr)_17rem] print:grid-cols-[minmax(0,1fr)_12.5rem] print:gap-7">
                <div class="flex min-w-0 flex-col gap-12 print:gap-4">

                    @if(trim($cv->field('profile')) !== '')
                        <section aria-labelledby="cv-profile">
                            <h2 id="cv-profile" class="cv-h">Profile</h2>
                            <div class="cv-prose cv-lead mt-4">{!! Cv::markdown($cv->field('profile')) !!}</div>
                        </section>
                    @endif

                    @if($experience['entries'])
                        <section aria-labelledby="cv-experience">
                            <h2 id="cv-experience" class="cv-h">Experience</h2>
                            @if($experience['intro'])<div class="cv-prose mt-4">{!! Cv::markdown($experience['intro']) !!}</div>@endif
                            <ol class="cv-timeline relative mt-6 flex flex-col gap-9 border-l border-white/15 pl-7 print:gap-3 print:pl-5">
                                @foreach($experience['entries'] as $entry)
                                    <li class="cv-entry relative">
                                        <span aria-hidden="true" class="cv-dot absolute top-1.5 -left-[35px] size-3 rounded-full bg-indigo-500 ring-4 ring-black print:-left-[25.5px] print:size-2 print:ring-0"></span>
                                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                                            <h3 class="text-lg font-semibold leading-snug text-white">{!! Cv::inline($entry['title']) !!}</h3>
                                            @if($entry['meta'])
                                                <span class="cv-meta shrink-0 rounded-full border border-white/10 bg-white/5 px-3 py-0.5 text-xs text-white/70">{!! Cv::inline($entry['meta']) !!}</span>
                                            @endif
                                        </div>
                                        @if($entry['org'])
                                            <p class="cv-accent cv-org mt-0.5 font-medium text-indigo-300">{!! Cv::inline($entry['org']) !!}</p>
                                        @endif
                                        @if(trim($entry['body']) !== '')
                                            <div class="cv-prose mt-3">{!! Cv::markdown($entry['body']) !!}</div>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </section>
                    @endif

                </div>

                <aside class="flex flex-col gap-12 print:gap-4">
                    @if($skills)
                        <section aria-labelledby="cv-skills">
                            <h2 id="cv-skills" class="cv-h">Skills</h2>
                            <div class="mt-5 flex flex-col gap-5 print:gap-2.5">
                                @foreach($skills as $group)
                                    <div>
                                        <h3 class="cv-skill-area text-xs font-semibold uppercase tracking-wider text-white/50">{{ $group['area'] }}</h3>
                                        <ul class="cv-skill-list mt-2 flex flex-wrap gap-1.5">
                                            @foreach($group['items'] as $skill)
                                                <li class="cv-skill rounded-md border border-indigo-400/20 bg-indigo-500/10 px-2 py-0.5 text-xs text-indigo-100">{{ $skill }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if($education['entries'])
                        <section aria-labelledby="cv-education">
                            <h2 id="cv-education" class="cv-h">Education</h2>
                            <div class="mt-5 flex flex-col gap-6 print:gap-3">
                                @foreach($education['entries'] as $entry)
                                    <div class="cv-entry">
                                        <h3 class="font-semibold leading-snug text-white">{!! Cv::inline($entry['title']) !!}</h3>
                                        @if($entry['org'])<p class="cv-accent text-sm font-medium text-indigo-300">{!! Cv::inline($entry['org']) !!}</p>@endif
                                        @if($entry['meta'])<p class="cv-meta-text mt-0.5 text-xs text-white/60">{!! Cv::inline($entry['meta']) !!}</p>@endif
                                        @if(trim($entry['body']) !== '')<div class="cv-prose mt-2 text-sm">{!! Cv::markdown($entry['body']) !!}</div>@endif
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if(trim($cv->field('interests')) !== '')
                        <section aria-labelledby="cv-interests">
                            <h2 id="cv-interests" class="cv-h">Interests</h2>
                            <div class="cv-prose mt-4 text-sm">{!! Cv::markdown($cv->field('interests')) !!}</div>
                        </section>
                    @endif
                </aside>
            </div>

            @if($projects['entries'])
                <section aria-labelledby="cv-projects" class="mt-12 print:mt-4">
                    <h2 id="cv-projects" class="cv-h">Selected projects</h2>
                    @if($projects['intro'])<div class="cv-prose mt-4 text-white/70">{!! Cv::markdown($projects['intro']) !!}</div>@endif
                    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 print:grid-cols-3 print:gap-x-5 print:gap-y-2.5">
                        @foreach($projects['entries'] as $project)
                            <article class="cv-card group rounded-2xl border border-white/10 bg-white/[0.03] p-5 transition hover:-translate-y-0.5 hover:border-indigo-400/50 hover:bg-white/[0.05]">
                                <h3 class="cv-card-title text-base font-semibold text-white">{!! Cv::inline($project['title']) !!}</h3>
                                <div class="cv-prose mt-2 text-sm">{!! Cv::markdown($project['body']) !!}</div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </x-content>
    </div>
</x-page>
