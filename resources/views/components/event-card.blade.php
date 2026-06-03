@props(['activity', 'showDate' => true])

{{-- PAT-1 · Event card. Hierarchy = WHERE first (a parent scans for a ride near them):
     ride title (municipality) is the anchor, meeting point second, the time is a small
     right-side detail. Date lives in the day header on the Kalender, so pass
     :show-date="false" there; standalone uses (home/chapter) keep the date. --}}
<a
    href="{{ route('activities.show', $activity) }}"
    {{ $attributes->merge(['class' => 'event-card link-plain']) }}
>
    @php
        // A parent scans for the TOWN. Every ride is "Kidical Mass <town>", so lead with the
        // town and drop the repeated brand prefix. One-offs (e.g. "Grande Kidical Mass 2026")
        // don't start with the prefix, so they keep their full name.
        $headline = preg_replace('/^\s*kidical\s+mass\s+/i', '', $activity->title_nl);
        $headline = trim((string) $headline) !== '' ? $headline : $activity->title_nl;
    @endphp

    <div class="event-card__body">
        <h3 class="event-card__title">{{ $headline }}</h3>
        @if ($activity->location)
            <p class="event-card__loc">
                <flux:icon.map-pin variant="solid" class="event-card__loc-icon" aria-hidden="true" />
                <span class="event-card__loc-text">{{ $activity->location }}</span>
            </p>
        @endif
    </div>

    <div class="event-card__when">
        @if ($showDate)
            <span class="event-card__date">{{ $activity->begin_date->locale('nl')->isoFormat('dd D MMM') }}</span>
        @endif
        <time class="event-card__time" datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ $activity->begin_date->format('H:i') }}</time>
    </div>
</a>
