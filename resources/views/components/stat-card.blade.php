@props([
    'value',          // the big number, e.g. "5.500"
    'label',          // the caption beneath it
    'icon' => null,   // optional Flux (Heroicons) icon name, e.g. "users"; omit for a clean number
    'color' => 'blue', // card colour: blue | green | red
])

{{-- Stat card: a solid-colour card with a big number, an optional white icon chip,
     and a caption. The single source of truth for the proof-of-impact stat look
     (used in the steun-ons proof deck). Appearance lives here as token-backed
     utilities - there is no app.css entry. Placement (deck overlap, tilt, reveal
     animation) is owned by the page that uses it. `stat-card` is an identity hook only. --}}
@php
    // Literal class strings (NOT interpolated) so Tailwind's scanner generates them.
    $bg = match ($color) {
        'green' => 'bg-kidical-green',
        'red' => 'bg-kidical-red',
        default => 'bg-kidical-blue',
    };
    $iconColor = match ($color) {
        'green' => 'text-kidical-green',
        'red' => 'text-kidical-red',
        default => 'text-kidical-blue',
    };
@endphp

<div {{ $attributes->merge(['class' => "stat-card flex flex-col gap-3 {$bg} rounded-card p-7 shadow-card"]) }}>
    <div class="flex items-start justify-between gap-4">
        <span class="font-heading font-normal leading-none text-white text-[clamp(2.75rem,5vw,4rem)]">{{ $value }}</span>
        @if ($icon)
            <span class="flex items-center justify-center shrink-0 size-12 rounded-full bg-white">
                <flux:icon name="{{ $icon }}" variant="solid" class="size-6 {{ $iconColor }}" aria-hidden="true" />
            </span>
        @endif
    </div>
    <p class="text-lg font-bold leading-snug text-white">{{ $label }}</p>
</div>
