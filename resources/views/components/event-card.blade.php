@props(['activity', 'showDate' => true, 'featured' => null, 'distance' => null])

{{-- PAT-1 · Event row. A flat agenda line, not a card: time first (the eye scans the
     day's schedule straight down), then the municipality as the anchor, then the meeting
     point as a quiet detail. The day grouping + dividers carry the structure, so rows stay
     light. Date lives in the day header on the Kalender, so pass :show-date="false" there;
     standalone uses (home / chapter) keep the date.
     Featured = the Grande Kidical Mass flagship (PAT-13/D-3): inline ★ + orange anchor,
     never a separate block. Auto-detected by name until a real `is_featured` field exists. --}}
@php
    // A parent scans for the TOWN. Every ride is "Kidical Mass <town>", so lead with the
    // town and drop the repeated brand prefix. One-offs (e.g. "Grande Kidical Mass 2026")
    // don't start with the prefix, so they keep their full name.
    $headline = preg_replace('/^\s*kidical\s+mass\s+/i', '', $activity->title_nl);
    $headline = trim((string) $headline) !== '' ? $headline : $activity->title_nl;

    $isFeatured = $featured ?? \Illuminate\Support\Str::contains(
        \Illuminate\Support\Str::lower($activity->title_nl), ['grande', 'grote kidical']
    );
@endphp
<a
    href="{{ route('activities.show', $activity) }}"
    {{ $attributes->merge(['class' => 'event-card link-plain'.($isFeatured ? ' event-card--featured' : '')]) }}
>
    <span class="event-card__when">
        @if ($showDate)
            <span class="event-card__date">{{ $activity->begin_date->locale('nl')->isoFormat('dd D MMM') }}</span>
        @endif
        <time class="event-card__time" datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ $activity->begin_date->format('H:i') }}</time>
    </span>

    <span class="event-card__place">
        @if ($isFeatured)<span class="event-card__star" aria-hidden="true">★</span>@endif{{ $headline }}
    </span>

    @if ($isFeatured)
        <span class="event-card__featured-badge">Uitgelicht</span>
    @endif

    @if ($activity->location)
        <span class="event-card__loc">
            <flux:icon.map-pin variant="solid" class="event-card__loc-icon" aria-hidden="true" />
            <span class="event-card__loc-text">{{ $activity->location }}</span>
        </span>
    @endif

    @if (! is_null($distance))
        <span class="event-card__distance">{{ $distance == 0 ? 'in jouw buurt' : $distance.' km' }}</span>
    @endif
</a>
