@props([
    'icon',           // Flux (Heroicons) icon name, e.g. "clock"
    'title',          // rendered as the card's <strong>
    'color' => 'red', // chip colour: red | blue | orange | ink | green | violet | coral
])

{{-- Feature card: an icon chip + title + body. The single source of truth for the
     "icon chip card" look used across the site (getting-started deck, about/mission, …).
     Appearance lives here as token-backed utilities — there is no app.css entry.
     Placement (grid vs deck, tilt, scroll behaviour) is owned by the page that uses it.
     `feature-card` is an identity hook only; it carries NO CSS. --}}

<div {{ $attributes->merge(['class' => 'feature-card flex flex-col gap-[1.125rem] bg-white rounded-card p-10 shadow-card [&_a]:text-kidical-blue [&_a]:font-bold [&_a]:bg-none [&_a:hover]:underline']) }}>
    <x-icon-chip :color="$color" size="lg">
        <flux:icon name="{{ $icon }}" variant="solid" class="size-[2.4rem] text-white" aria-hidden="true" />
    </x-icon-chip>
    <h3 class="text-kidical-ink">{{ $title }}</h3>
    <p class="text-[1.3125rem] leading-[1.6] text-kidical-ink/75">{{ $slot }}</p>
</div>
