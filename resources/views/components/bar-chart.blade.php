@props([
    'trend' => [],
    'type' => 'visitors',
    'unit' => 'visitors',
    'empty' => 'No data yet.',
])

@php
    $trend = collect($trend);
    $max = max(1, (int) $trend->max('count'));
@endphp

@if ($trend->isNotEmpty())
    <div data-bar-chart data-chart-type="{{ $type }}">
        <div class="flex items-end gap-1 h-40" data-bars>
            @foreach ($trend as $day)
                <button type="button"
                    class="chart-bar group relative flex-1 flex flex-col justify-end h-full cursor-pointer rounded-t focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50"
                    data-date="{{ $day['date'] }}"
                    data-label="{{ $day['label'] }}"
                    data-count="{{ $day['count'] }}"
                    @if ($type === 'messages') data-items="{{ json_encode($day['items'] ?? []) }}" @endif
                    aria-pressed="false"
                    aria-label="{{ $day['label'] }}: {{ $day['count'] }} {{ $unit }}">
                    <span class="chart-bar__fill w-full rounded-t bg-white/80 transition-colors group-hover:bg-purple"
                        style="height: {{ (int) $day['count'] === 0 ? '2px' : round(($day['count'] / $max) * 100, 2) . '%' }}"></span>
                </button>
            @endforeach
        </div>
        <div class="flex justify-between text-white/40 text-xs mt-2">
            <span>{{ $trend->first()['label'] }}</span>
            <span>{{ $trend->last()['label'] }}</span>
        </div>

        <div class="mt-4 border-t border-white/10 pt-4 min-h-[4.5rem]" data-readout>
            {{ $slot }}
        </div>
    </div>
@else
    <p class="text-white/50">{{ $empty }}</p>
@endif
