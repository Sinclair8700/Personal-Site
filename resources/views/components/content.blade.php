@props([
    'type' => 'wide'
])

@php
    $types = [
        'wide' => 'max-w-[1400px]',
        'medium' => 'max-w-[1000px]',
        'narrow' => 'max-w-[600px]',
    ];
@endphp

<div class="w-full flex" >
    @isset($leftGutter)
        <div {{$leftGutter->attributes->merge([
            'class' => 'w-full shrink-1 grow-0 content-gutter'
        ])}}>

            {{$leftGutter}}
        </div>
    @else
        <div class="w-full shrink-1 grow-0 content-gutter"></div>
    @endisset

    <div {{ 
        $attributes->merge([
            'class' => "relative px-6 flex flex-col w-full shrink-0 grow " . $types[$type]
        ])
    }}>
        {{ $slot }}
    </div>
    @isset($rightGutter)
        <div {{$rightGutter->attributes->merge([
            'class' => 'w-full shrink-1 grow-0 content-gutter'
        ])}}>
            {{$rightGutter}}
        </div>
    @else
        <div class="w-full shrink-1 grow-0 content-gutter"></div>
    @endisset
</div>