@props([
    'color' => 'red', // red | blue | orange | ink | green | violet | coral
    'size' => 'md',   // sm 2.25rem | md 2.75rem | lg 4.25rem
    'shadow' => false,
])

{{-- The icon-chip motif: a tilted, rounded-square colour tile with a white icon.
     The single source of truth for the chip used by <x-feature-card>, the roze-hub
     Voor-de-rit tiles, and the feed cards. The icon (Flux or inline SVG) is the slot. --}}
@php
    // Literal class strings (NOT interpolated) so Tailwind's scanner generates them.
    $chipBg = match ($color) {
        'blue' => 'bg-kidical-blue',
        'orange' => 'bg-kidical-orange',
        'ink' => 'bg-kidical-ink',
        'green' => 'bg-kidical-green',
        'violet' => 'bg-kidical-violet',
        'coral' => 'bg-kidical-coral',
        default => 'bg-kidical-red',
    };
    $chipSize = match ($size) {
        'sm' => 'size-[2.25rem]',
        'lg' => 'size-[4.25rem]',
        default => 'size-[2.75rem]',
    };
    $chipShadow = $shadow ? 'shadow-float' : '';
@endphp

<span {{ $attributes->merge(['class' => "flex items-center justify-center shrink-0 -rotate-3 rounded-chip text-white {$chipBg} {$chipSize} {$chipShadow}"]) }}>
    {{ $slot }}
</span>
