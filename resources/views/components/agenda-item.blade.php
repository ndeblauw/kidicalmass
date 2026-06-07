@props([
    'badge',
    'badgeVariant' => 'other', // ride | workshop | meeting | other
    'datetime',
    'when',
    'title',
    'location'  => null,
    'ctaHref',
    'ctaLabel',
    'quiet'     => false,
])

<li {{ $attributes->merge(['class' => 'agenda-item']) }}>
    <span class="agenda-item__badge agenda-item__badge--{{ $badgeVariant }}">{{ $badge }}</span>
    <span class="agenda-item__when">
        <time datetime="{{ $datetime }}">{{ $when }}</time>
    </span>
    <span class="agenda-item__what">
        {{ $title }}
        @if ($location)
            <span class="agenda-item__loc">· {{ $location }}</span>
        @endif
    </span>
    <a href="{{ $ctaHref }}" class="agenda-item__cta{{ $quiet ? ' agenda-item__cta--quiet' : '' }} link-plain">{{ $ctaLabel }}</a>
</li>
