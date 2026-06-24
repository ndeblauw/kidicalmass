@props(['activity', 'commune' => null])

{{-- A single upcoming parade as a self-contained card-chip; the whole pill is the link.
     A short date (muted) leads, the title (blue) carries the name. Used in the chapter
     page's "Alle parades" strip, where the chips wrap in a flowing, compact row.

     Title stripping mirrors <x-ride-row>: drop a leading "kidical mass", then the
     chapter's commune, falling back to a type label when nothing distinctive remains
     (a plain ride's title *is* the commune, so it collapses to "Fietsparade"). --}}
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

    $rail = \App\Support\RideDate::rail($activity->begin_date);
    $shortDate = $rail['num'].' '.$rail['month'];
@endphp
<a
    href="{{ route('activities.show', $activity) }}"
    {{ $attributes->merge(['class' => 'ride-pill link-plain']) }}
>
    <time class="ride-pill__date" datetime="{{ $activity->begin_date->format('Y-m-d') }}">{{ $shortDate }}</time>
    <span class="ride-pill__title">{{ $headline }}</span>
    @if ($activity->isGrande())
        <span class="ride-pill__star" aria-hidden="true">★</span>
    @endif
</a>
