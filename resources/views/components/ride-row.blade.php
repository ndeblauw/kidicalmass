@props(['activity', 'showDate' => false, 'commune' => null, 'distanceKm' => null])

{{-- One row for every ride. The calendar lockup colour carries the type; the
     title carries the name. Inside a chapter the commune is already established
     by the page, so pass :commune to drop it from the title and venue — a plain
     ride collapses to nothing that way (its title *is* the commune), so there
     the activity becomes "Fietsparade". --}}
@php
    $headline = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $activity->title));
    if ($headline === '') {
        $headline = $activity->title;
    }

    if ($commune !== null && $commune !== '') {
        $bare = trim((string) preg_replace('/\b'.preg_quote($commune, '/').'\b/iu', '', $headline), " \t\n\r,–-");
        $headline = $bare !== '' ? $bare : $activity->activity_type->labelLocalized();
    }

    // The Grande's star marker lives on the calendar lockup (<x-ride-day>), not in the
    // title. Here it only drives the (optional) featured row class.
    $isFeatured = $activity->isGrande();

    // Drop a trailing ", <commune>" from the venue: the chapter's commune when we
    // have it, otherwise a commune that merely repeats the ride's own headline.
    $venueDisplay = $activity->location;
    if ($venueDisplay !== null && $venueDisplay !== '') {
        $venueDisplay = trim((string) preg_replace(
            '/,\s*'.preg_quote($commune ?? $headline, '/').'\s*$/iu', '', $venueDisplay,
        ));
    }
@endphp
<a
    href="{{ localized_route('activities.show', ['activity' => $activity]) }}"
    {{ $attributes->merge(['class' => 'ride-row link-plain'.($isFeatured ? ' ride-row--featured' : '')]) }}
>
    <span class="ride-row__place">{{ $headline }}</span>

    <span class="ride-row__meta">
        @if ($showDate)
            <span class="ride-row__date">{{ $activity->dateShort }} ·</span>
        @else
            {{-- The lockup carries the date number; the meta line carries the weekday. --}}
            <span class="ride-row__weekday">{{ $activity->weekdayLabel }}</span>
        @endif
        <time class="ride-row__time" datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ $activity->timeLabel }}</time>
        @if ($distanceKm !== null)
            {{-- Distance rides with the time on the short first line, behind a dot, so it
                 never reads as part of the time ("15u 50 km") and every row keeps one shape.
                 Below 10 km always one decimal, above it whole km. --}}
            <span class="ride-row__distance" data-distance-km="{{ $distanceKm }}"><span class="ride-row__sep" aria-hidden="true">·</span> {{ $distanceKm < 1
                ? __('common.distance_under_km')
                : __('common.distance_km', ['km' => \Illuminate\Support\Number::format($distanceKm, precision: $distanceKm < 10 ? 1 : 0, locale: app()->getLocale())]) }}</span>
        @endif
        @if ($venueDisplay)
            <span class="ride-row__where"><span class="ride-row__at" aria-hidden="true">@</span> <span class="ride-row__venue">{{ $venueDisplay }}</span></span>
        @endif
    </span>
</a>
