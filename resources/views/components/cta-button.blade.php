@props([
    'href' => null,
    'variant' => 'yellow', // yellow (dark/blue grounds) | blue (yellow bands) | pink (roze-hesje actions) | secondary (outlined, quiet) | ghost (text-only)
    'icon' => 'arrow',     // arrow (a "go" action) | back (a "return" action) | heart (support / membership)
    'size' => 'md',        // md | sm (nav + footer)
    'disc' => null,        // disc colour: red (default) | green | orange | blue
    'disabled' => false,   // inert, dimmed — no navigation, no hover animation
    'loading' => false,    // inert, shows a spinner (state set server-side)
    'block' => false,      // full-width
])

{{-- The site's signature action: a pill with an animated disc that slides from
     right to left on hover. Pure CSS, no JS (the public layout ships no Alpine).
     Styling lives in .cta-button* in resources/css/components/cta-button.css.
     `disabled`/`loading` render an inert <a> (no href) since there is no JS to
     intercept clicks. With no `href` (and not inert) it renders a <button> so it
     can drive form submits or Livewire `wire:click` actions. --}}
@php
    $isInert = $disabled || $loading;
    $isButton = $href === null && ! $isInert;

    $classes = collect([
        'cta-button',
        'cta-button--'.$variant,
        $size === 'sm' ? 'cta-button--sm' : null,
        $block ? 'cta-button--block' : null,
        $disabled ? 'cta-button--disabled' : null,
        $loading ? 'cta-button--loading' : null,
    ])->filter()->implode(' ');

    // The disc colour is a CSS custom property the stylesheet reads, defaulting
    // to red when unset. Whitelisted to brand tokens so it can't inject styles.
    $discStyle = match ($disc) {
        'green' => '--cta-disc-color: var(--color-kidical-green);',
        'orange' => '--cta-disc-color: var(--color-kidical-orange);',
        'blue' => '--cta-disc-color: var(--color-kidical-blue);',
        'red' => '--cta-disc-color: var(--color-kidical-red);',
        default => null,
    };

    // Icons use currentColor so the disc can recolour per variant (white on the
    // primary red disc, muted ink on the quiet secondary/ghost disc).
    $iconSvg = match ($icon) {
        'heart' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0z"/></svg>',
        'back' => '<svg viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M12 7H2M6 3 2 7l4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        default => '<svg viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    };
@endphp

@if ($isButton)
<button type="button" {{ $attributes->merge(['class' => $classes, 'style' => $discStyle]) }}>
@else
<a
    @unless ($isInert) href="{{ $href }}" @endunless
    @if ($isInert) aria-disabled="true" tabindex="-1" @endif
    @if ($loading) aria-busy="true" @endif
    {{ $attributes->merge(['class' => $classes, 'style' => $discStyle]) }}
>
@endif
    @if ($loading)
        <span class="cta-button__spinner" aria-hidden="true"></span>
    @endif
    <span class="cta-button__slot cta-button__slot--left" aria-hidden="true">
        <span class="cta-button__disc">{!! $iconSvg !!}</span>
    </span>
    <span class="cta-button__label">{{ $slot }}</span>
    <span class="cta-button__slot cta-button__slot--right" aria-hidden="true">
        <span class="cta-button__disc">{!! $iconSvg !!}</span>
    </span>
@if ($isButton)</button>@else</a>@endif
