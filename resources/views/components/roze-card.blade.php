@props([
    'icon',           // Flux (Heroicons) icon name, e.g. "users"
    'title',          // rendered as the card's <h3>
    'color' => 'red', // chip colour: red | blue | orange | ink | green | violet | coral
])

{{-- Compact content card for use inside the roze-hesjes hub.
     Appearance lives here as token-backed utilities — there is no app.css entry.
     Placement (grid, spacing) is owned by the page that uses it. --}}

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 bg-white rounded-card p-6 shadow-card']) }}>
    <x-icon-chip :color="$color" size="md">
        <flux:icon name="{{ $icon }}" variant="solid" class="size-6 text-white" aria-hidden="true" />
    </x-icon-chip>
    <h3 class="roze-card-title">{{ $title }}</h3>
    <p class="text-kidical-ink/75">{{ $slot }}</p>
</div>
