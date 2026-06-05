@props([
    'href',
    'variant' => 'yellow', // yellow (dark/blue grounds) | blue (yellow CTA bands)
    'icon' => 'arrow',     // arrow (a "go" action) | heart (support / membership)
    'size' => 'md',        // md | sm (nav + footer)
])

{{-- The site's signature primary action: a pill with an animated disc that
     slides from right to left on hover. Pure CSS, no JS (the public layout
     ships no Alpine). Styling lives in .cta-button* in app.css. --}}
@php
    $classes = collect([
        'cta-button',
        'cta-button--'.$variant,
        $size === 'sm' ? 'cta-button--sm' : null,
    ])->filter()->implode(' ');

    $iconSvg = match ($icon) {
        'heart' => '<svg viewBox="0 0 24 24" fill="white" aria-hidden="true"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0z"/></svg>',
        default => '<svg viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M2 7h10M8 3l4 4-4 4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    };
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    <span class="cta-button__slot cta-button__slot--left" aria-hidden="true">
        <span class="cta-button__disc">{!! $iconSvg !!}</span>
    </span>
    <span class="cta-button__label">{{ $slot }}</span>
    <span class="cta-button__slot cta-button__slot--right" aria-hidden="true">
        <span class="cta-button__disc">{!! $iconSvg !!}</span>
    </span>
</a>
