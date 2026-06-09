@props(['activity', 'showDate' => false])

{{-- One row for every ride. Commune = anchor; chip only when it's not a ride. --}}
@php
    $headline = preg_replace('/^\s*kidical\s+mass\s+/i', '', $activity->title);
    $headline = trim((string) $headline) !== '' ? $headline : $activity->title;

    // The Grande's star marker lives on the calendar lockup (<x-ride-day>), not in the
    // title. Here it only drives the (optional) featured row class.
    $isFeatured = $activity->isGrande();

    // Strip a trailing ", <commune>" from the venue when it duplicates the headline.
    $venueDisplay = $activity->location;
    if ($venueDisplay !== null && $venueDisplay !== '') {
        $venueDisplay = trim((string) preg_replace(
            '/,\s*'.preg_quote($headline, '/').'\s*$/iu', '', $venueDisplay,
        ));
    }

    $chip = match ($activity->activity_type) {
        \App\Enums\ActivityType::WORKSHOP => ['label' => 'Workshop', 'variant' => 'workshop'],
        \App\Enums\ActivityType::MEETING => ['label' => 'Vergadering', 'variant' => 'meeting'],
        \App\Enums\ActivityType::OTHER => ['label' => 'Activiteit', 'variant' => 'other'],
        default => null,
    };
@endphp
<a
    href="{{ route('activities.show', $activity) }}"
    {{ $attributes->merge(['class' => 'ride-row link-plain'.($isFeatured ? ' ride-row--featured' : '')]) }}
>
    <span class="ride-row__place">
        @if ($chip)<span class="ride-row__chip ride-row__chip--{{ $chip['variant'] }}">{{ $chip['label'] }}</span>@endif{{ $headline }}
    </span>

    <span class="ride-row__meta">
        @if ($showDate)
            <span class="ride-row__date">{{ $activity->dateShort }} ·</span>
        @endif
        <time class="ride-row__time" datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ $activity->timeLabel }}</time>
        @if ($venueDisplay)
            <span class="ride-row__at" aria-hidden="true">@</span><span class="ride-row__venue">{{ $venueDisplay }}</span>
        @endif
    </span>
</a>
