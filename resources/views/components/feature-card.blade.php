@props([
    'icon',           // Flux (Heroicons) icon name, e.g. "clock"
    'title',          // rendered as the card's heading
    'color' => 'red', // chip colour: red | blue | orange | ink | green | violet | coral
    'size' => 'lg',   // lg = full feature card (default) · md = compact (roze-hesjes hub)
])

{{-- Feature card: an icon chip + title + body. The single source of truth for the
     "icon chip card" look used across the site (getting-started deck, about/mission,
     and — at the compact `md` size, via <x-roze-card> — the roze-hesjes hub).
     Appearance lives here as token-backed utilities — there is no app.css entry.
     Placement (grid vs deck, tilt, scroll behaviour) is owned by the page that uses it.
     `feature-card` is an identity hook only; it carries NO CSS. --}}

@php
    $isCompact = $size === 'md';
    $wrapperClass = $isCompact
        ? 'flex flex-col gap-4 bg-white rounded-card p-6 shadow-card'
        : 'feature-card flex flex-col gap-[1.125rem] bg-white rounded-card p-10 shadow-card [&_a]:text-kidical-blue [&_a]:font-bold [&_a]:bg-none [&_a:hover]:underline';
    $iconSizeClass = $isCompact ? 'size-6' : 'size-[2.4rem]';
    // Compact uses the shared .roze-card-title face (Nunito Sans 800); the full
    // card lets the <h3> inherit the Caprasimo display face from @layer base.
    $titleClass = $isCompact ? 'roze-card-title' : 'text-kidical-ink';
    $bodyClass = $isCompact ? 'text-kidical-ink/75' : 'text-[1.3125rem] leading-[1.6] text-kidical-ink/75';
@endphp

<div {{ $attributes->merge(['class' => $wrapperClass]) }}>
    <x-icon-chip :color="$color" :size="$isCompact ? 'md' : 'lg'">
        <flux:icon name="{{ $icon }}" variant="solid" class="{{ $iconSizeClass }} text-white" aria-hidden="true" />
    </x-icon-chip>
    <h3 class="{{ $titleClass }}">{{ $title }}</h3>
    <p class="{{ $bodyClass }}">{{ $slot }}</p>
</div>
