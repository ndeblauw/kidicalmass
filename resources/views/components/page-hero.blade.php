@props([
    'eyebrow',
    'title',
    'illustration' => null,
])

{{-- Fixed brand-blue hero. Pinned at the lowest z-layer; .page-panel scrolls over it.
     The floating nav pill (site header) sits above this. --}}
<header class="page-hero">
    <div class="page-hero__inner container mx-auto px-4">
        <div class="page-hero__copy">
            <p class="page-hero__eyebrow">{{ $eyebrow }}</p>
            <h1 class="page-hero__title">{{ $title }}</h1>
            @isset($controls)
                <div class="page-hero__controls">{{ $controls }}</div>
            @endisset
        </div>

        @if ($illustration)
            <div class="page-hero__visual">
                <img src="{{ asset($illustration) }}" alt="" aria-hidden="true" class="page-hero__illustration">
            </div>
        @endif
    </div>
</header>

{{-- Holds the hero's place in normal flow (the hero itself is position:fixed). --}}
<div class="page-hero__spacer" aria-hidden="true"></div>

{{-- White rounded-top panel; scrolls up over the pinned hero. --}}
<div class="page-panel">
    <div class="page-panel__inner container mx-auto px-4">
        {{ $slot }}
    </div>
</div>
