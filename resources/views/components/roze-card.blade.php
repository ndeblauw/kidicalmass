@props([
    'icon',           // Flux (Heroicons) icon name, e.g. "users"
    'title',          // rendered as the card's <h3>
    'color' => 'red', // chip colour: red | blue | orange | ink | green | violet | coral
])

{{-- Compact content card for the roze-hesjes hub. Thin alias over <x-feature-card>
     at the `md` size, kept so the hub markup still reads <x-roze-card>. --}}

<x-feature-card :icon="$icon" :title="$title" :color="$color" size="md" {{ $attributes }}>{{ $slot }}</x-feature-card>
