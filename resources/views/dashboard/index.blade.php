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

        <x-bubble class="mt-6">
            <div class="flex items-baseline justify-between mb-4">
                <h3>New visitors</h3>
                <span class="text-white/50 text-sm">last 30 days</span>
            </div>

            @php $trend = collect($trend ?? []); @endphp
            @if ($trend->isNotEmpty())
                @php $max = max(1, $trend->max('count')); @endphp
                <div class="flex items-end gap-1 h-40">
                    @foreach ($trend as $day)
                        <div class="group relative flex-1 flex flex-col justify-end h-full" aria-label="{{ $day['label'] }}: {{ $day['count'] }} new visitors">
                            <div class="w-full rounded-t bg-white/80 hover:bg-purple transition-colors"
                                style="height: {{ $day['count'] === 0 ? '2px' : round(($day['count'] / $max) * 100, 2) . '%' }}"></div>
                            <div class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block whitespace-nowrap rounded bg-white text-black text-xs px-2 py-1 z-10">
                                {{ $day['label'] }}: {{ $day['count'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between text-white/40 text-xs mt-2">
                    <span>{{ $trend->first()['label'] }}</span>
                    <span>{{ $trend->last()['label'] }}</span>
                </div>
            @else
                <p class="text-white/50">No visitor data yet.</p>
            @endif
        </x-bubble>

        <div class="mt-6">
            <a href="{{ route('projects.create') }}"
                class="inline-block rounded-md border-2 border-white text-white px-4 py-2 transition-colors duration-300 hover:border-purple">
                Create project
            </a>
        </div>
    </x-content>
</x-page>
