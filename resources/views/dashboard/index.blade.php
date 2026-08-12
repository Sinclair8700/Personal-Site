<x-page :title="$title ?? 'Dashboard'" :noindex="true">
    <x-content class="py-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <x-bubble>
                <h3>Projects</h3>
                <p class="text-4xl font-semibold text-white">{{ $projectCount ?? 0 }}</p>
            </x-bubble>
            <x-bubble>
                <h3>Messages</h3>
                <p class="text-4xl font-semibold text-white">{{ $messageCount ?? 0 }}</p>
            </x-bubble>
            <x-bubble>
                <h3>Unique visitors</h3>
                <p class="text-4xl font-semibold text-white">{{ $uniqueVisitors ?? 0 }}</p>
            </x-bubble>
        </div>

        {{-- New visitors --}}
        <x-bubble class="mt-6">
            <div class="flex items-baseline justify-between mb-4">
                <h3>New visitors</h3>
                <span class="text-white/50 text-sm">last 30 days</span>
            </div>

            @php
                $visitorTrend = collect($visitorTrend ?? []);
                $visitorPeak = $visitorTrend->sortByDesc('count')->first();
            @endphp

            <x-bar-chart :trend="$visitorTrend" type="visitors" unit="new visitors" empty="No visitor data yet.">
                <dl class="grid grid-cols-[auto_1fr] gap-x-6 gap-y-1 text-sm">
                    <dt class="text-white/60">30-day total</dt>
                    <dd class="text-white text-right font-medium">{{ $visitorTrend->sum('count') }}</dd>
                    <dt class="text-white/60">Busiest day</dt>
                    <dd class="text-white text-right font-medium">
                        {{ $visitorPeak && $visitorPeak['count'] > 0 ? $visitorPeak['label'].' ('.$visitorPeak['count'].')' : '—' }}
                    </dd>
                </dl>
                <p class="text-white/40 text-xs mt-3">Hover a bar for a day&rsquo;s detail &middot; click to pin it.</p>
            </x-bar-chart>
        </x-bubble>

        {{-- Messages --}}
        <x-bubble class="mt-6" id="messages">
            <div class="flex items-baseline justify-between mb-4">
                <h3>Messages</h3>
                <span class="text-white/50 text-sm">last 30 days</span>
            </div>

            @php $messages = collect($messages ?? []); @endphp

            <x-bar-chart :trend="$messageTrend ?? []" type="messages" unit="messages" empty="No messages yet.">
                @if ($messages->isNotEmpty())
                    <div class="text-white/50 text-xs mb-2">Most recent messages &middot; last 30 days</div>
                    <div class="max-h-64 overflow-y-auto scrollbar-thin scrollbar-thumb-white/20">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="text-white/50 text-xs text-left">
                                    <th class="font-medium py-1 pr-3 sticky top-0 bg-black">When</th>
                                    <th class="font-medium py-1 pr-3 sticky top-0 bg-black">Email</th>
                                    <th class="font-medium py-1 sticky top-0 bg-black">Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($messages as $message)
                                    <tr class="border-t border-white/10 align-top">
                                        <td class="py-2 pr-3 text-white/60 whitespace-nowrap">{{ $message->created_at->format('M j, g:i a') }}</td>
                                        <td class="py-2 pr-3 text-white whitespace-nowrap">
                                            <a href="mailto:{{ $message->email_address }}" class="underline decoration-white/20 hover:text-purple">{{ $message->email_address }}</a>
                                        </td>
                                        <td class="py-2 text-white/80">{{ $message->message }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-white/50 text-sm">No messages in the last 30 days.</p>
                @endif
            </x-bar-chart>
        </x-bubble>

        <div class="mt-6">
            <a href="{{ route('projects.create') }}"
                class="inline-block rounded-md border-2 border-white text-white px-4 py-2 transition-colors duration-300 hover:border-purple">
                Create project
            </a>
        </div>
    </x-content>
</x-page>
