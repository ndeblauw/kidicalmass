@props([
    'href',
    'illustration',   // filename in img/illustrations, e.g. "waving-rider.svg"
    'title',
    'tint' => 'sky',  // art-panel tint: sky | yellow | red
])

{{-- Route card: a navigational "crossroads" link on the home dispatcher. Shares the
     site's card surface (rounded-card + shadow-card + white) with feature-card, but is
     a LINK, not a feature — so it leads with the illustration and carries a directional
     arrow affordance instead of an icon chip. Appearance lives here as token-backed
     utilities (no app.css entry); the page owns placement (the 3-up grid + signposts).
     `route-card` is an identity hook only and carries NO CSS. --}}
@php
    // Literal class strings (NOT interpolated) so Tailwind's scanner generates them.
    $tintClass = match ($tint) {
        'yellow' => 'bg-kidical-yellow/30',
        'red' => 'bg-kidical-red/15',
        default => 'bg-kidical-sky/25',
    };
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'route-card link-plain group flex flex-col gap-3 bg-white rounded-card p-8 shadow-card transition duration-150 hover:shadow-xl hover:ring-2 hover:ring-kidical-blue motion-safe:hover:-translate-y-1']) }}>
    <span class="flex items-center justify-center size-24 rounded-chip {{ $tintClass }}">
        <img src="{{ asset('img/illustrations/'.$illustration) }}" alt="" aria-hidden="true" loading="lazy"
            class="size-full object-contain motion-safe:transition-transform motion-safe:duration-150 motion-safe:group-hover:scale-105 motion-safe:group-hover:-rotate-2">
    </span>
    <strong class="flex items-center gap-2 font-heading text-[1.375rem] font-normal leading-[1.2] text-kidical-blue">
        {{ $title }}
        <span aria-hidden="true" class="motion-safe:transition-transform motion-safe:duration-150 motion-safe:group-hover:translate-x-1">&rarr;</span>
    </strong>
    <p class="text-[1.0625rem] leading-[1.5] text-kidical-ink/70">{{ $slot }}</p>
</a>
