@props([
    'position' => 'top',
    'gap' => 12
])

@php
    $validPositions = ['top', 'bottom', 'left', 'right'];
    $positionClasses = [];
    
    // Handle multiple positions (space-separated)
    $positionValues = explode(' ', $position);
    
    foreach ($positionValues as $pos) {
        if (in_array($pos, $validPositions)) {
            $positionClasses[] = 'popover-' . $pos;
        }
    }
    
    // If no valid positions were found, default to 'top'
    if (empty($positionClasses)) {
        $positionClasses[] = 'popover-top';
    }
    
    $positionClassString = implode(' ', $positionClasses);
@endphp

<div class="popover {{ $positionClassString }} popover-gap-{{ $gap }} z-40 hidden rounded-xl bg-black shadow-[0_0_8px_rgba(255,255,255,0.35)] px-4 py-2 text-white w-auto">
    {{ $slot }}
</div>
