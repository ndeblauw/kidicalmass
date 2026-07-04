@props(['heading'])

{{-- Centred empty state for archives/feeds awaiting content: a heading plus a
     short explanation in the slot. Composition-only utilities; no CSS partial. --}}
<div {{ $attributes->merge(['class' => 'empty-state mx-auto max-w-3xl py-4 text-center']) }}>
    <h2 class="mb-3">{{ $heading }}</h2>
    <p>{{ $slot }}</p>
</div>
