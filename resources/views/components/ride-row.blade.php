@props(['activity', 'showDate' => false, 'commune' => null])

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
        $headline = $bare !== '' ? $bare : match ($activity->activity_type) {
            \App\Enums\ActivityType::WORKSHOP => 'Workshop',
            \App\Enums\ActivityType::MEETING => 'Vergadering',
            \App\Enums\ActivityType::OTHER => 'Activiteit',
            default => 'Fietsparade',
        };
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
    href="{{ route('activities.show', $activity) }}"
    {{ $attributes->merge(['class' => 'ride-row link-plain'.($isFeatured ? ' ride-row--featured' : '')]) }}
>
    <span class="ride-row__place">{{ $headline }}</span>

    <span class="ride-row__meta">
        @if ($showDate)
            <span class="ride-row__date">{{ $activity->dateShort }} ·</span>
        @endif
        <time class="ride-row__time" datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ $activity->timeLabel }}</time>
        @if ($venueDisplay)
            <span class="ride-row__where"><span class="ride-row__at" aria-hidden="true">@</span> <span class="ride-row__venue">{{ $venueDisplay }}</span></span>
        @endif
    </span>
</a>
