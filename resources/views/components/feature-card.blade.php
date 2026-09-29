@props([
    'icon',            // Flux (Heroicons) icon name, e.g. "clock"
    'title',           // rendered as the card's heading
    'color' => 'red',  // chip colour: red | blue | orange | ink | green | violet | coral
    'size' => 'lg',    // lg = full feature card (default) · md = compact (roze-hesjes hub)
    'heading' => 'h3', // heading level for the title (h3 default; pass h4 when nested under an h3)
    'href' => null,    // optional link target: the title's stretched link makes the whole card clickable
])

{{-- Feature card: an icon chip + title + body. The single source of truth for the
     "icon chip card" look used across the site (getting-started deck, about/mission,
     and — at the compact `md` size, via <x-roze-card> — the roze-hesjes hub).
     Appearance lives here as token-backed utilities — there is no app.css entry.
     Placement (grid vs deck, tilt, scroll behaviour) is owned by the page that uses it.
     `feature-card` is an identity hook only; it carries NO CSS.
     With an `href` the card becomes a link card (about hub exits): the title's
     stretched link covers the card, which lifts on hover like x-article-card. --}}

@php
    $isCompact = $size === 'md';
    $wrapperClass = $isCompact
        ? 'flex flex-col gap-4 bg-white rounded-tile p-5 shadow-card'
        : 'feature-card flex flex-col gap-[1.125rem] bg-white rounded-card p-10 shadow-card [&_a]:text-kidical-blue [&_a]:font-bold [&_a]:bg-none [&_a:hover]:underline';
    $iconSizeClass = $isCompact ? 'size-6' : 'size-[2.4rem]';
    // Compact pairs the shared .roze-card-title face (Nunito Sans 800, text-xl) over the
    // hub's Small body copy (var(--text-sm)) — the same description size the material tiles,
    // roster, stepper and WhatsApp card use, so card copy reads at one calm size across the
    // hub. The full card lets the <h3> inherit the Caprasimo display face from @layer base,
    // over a larger lead body.
    $titleClass = $isCompact ? 'roze-card-title' : 'text-kidical-ink';
    $bodyClass = $isCompact ? 'text-sm leading-relaxed text-kidical-ink/75' : 'text-[1.3125rem] leading-[1.6] text-kidical-ink/75';
    if ($href) {
        $wrapperClass .= ' group relative transition-[transform,box-shadow] duration-200 hover:-translate-y-1 hover:shadow-hover';
    }
@endphp

<div data-size="{{ $size }}" @if ($href) data-link-card @endif {{ $attributes->merge(['class' => $wrapperClass]) }}>
    <x-icon-chip :color="$color" :size="$isCompact ? 'md' : 'lg'">
        <flux:icon name="{{ $icon }}" variant="solid" class="{{ $iconSizeClass }} text-white" aria-hidden="true" />
    </x-icon-chip>
    <{{ $heading }} class="{{ $titleClass }}">
        @if ($href)
            <a href="{{ $href }}" class="link-plain transition-colors group-hover:text-kidical-blue after:absolute after:inset-0">{{ $title }}<span class="ml-1.5 inline-block transition-transform duration-200 group-hover:translate-x-1" aria-hidden="true">→</span></a>
        @else
            {{ $title }}
        @endif
    </{{ $heading }}>
    <p class="{{ $bodyClass }}">{{ $slot }}</p>
</div>
