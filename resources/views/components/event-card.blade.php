@props(['activity', 'showDate' => true, 'featured' => null])

{{-- PAT-1 · Event row. PIN + CITY | VENUE | TIME.
     Pin moves to immediately before the municipality (the anchor a parent scans for).
     Venue strips trailing ", <municipality>" when it matches the display name. --}}
@php
    $headline = preg_replace('/^\s*kidical\s+mass\s+/i', '', $activity->title_nl);
    $headline = trim((string) $headline) !== '' ? $headline : $activity->title_nl;

    $isFeatured = $featured ?? \Illuminate\Support\Str::contains(
        \Illuminate\Support\Str::lower($activity->title_nl), ['grande', 'grote kidical']
    );

    // Strip trailing ", <municipality>" from venue when it duplicates the display city.
    $venueDisplay = $activity->location;
    if ($venueDisplay !== null && $venueDisplay !== '') {
        $venueDisplay = trim((string) preg_replace(
            '/,\s*' . preg_quote($headline, '/') . '\s*$/iu',
            '',
            $venueDisplay,
        ));
    }
@endphp
<a
    href="{{ route('activities.show', $activity) }}"
    {{ $attributes->merge(['class' => 'event-card link-plain'.($isFeatured ? ' event-card--featured' : '')]) }}
>
    <span class="event-card__place">
        <flux:icon.map-pin variant="solid" class="event-card__place-pin" aria-hidden="true" />
        @if ($isFeatured)<span class="event-card__star" aria-hidden="true">★</span>@endif{{ $headline }}
    </span>

    @if ($isFeatured)
        <span class="event-card__featured-badge">Uitgelicht</span>
    @endif

    @if ($venueDisplay)
        <span class="event-card__loc">
            <span class="event-card__loc-text">{{ $venueDisplay }}</span>
        </span>
    @endif

    <span class="event-card__when">
        @if ($showDate)
            <span class="event-card__date">{{ $activity->begin_date->locale('nl')->isoFormat('dd D MMM') }}</span>
        @endif
        <time class="event-card__time" datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ $activity->begin_date->format('H:i') }}</time>
    </span>
</a>
