@props([
    'activity',          // App\Models\Activity — a non-ride activity (workshop/meeting/other)
    'commune' => null,   // gemeente name, stripped from the venue (redundant on its own page)
])

{{--
    One entry in the chapter page's "Ook in {gemeente}" rail — the dedicated, neutral
    alternative to <x-ride-row> (the rides keep their own accent look). A quiet two-line
    link: a sans-serif bold blue title over a muted "date · venue" meta line, where the
    date is simply the day + full month (no weekday). No type label — the title already
    says what it is. Tuned in the design playground (Frederik 2026-06-24). Appearance in
    resources/css/components/other-activity.css; the parent .chapter-aside__list supplies
    the hairline dividers.
--}}
@php
    // Drop the commune everywhere it's redundant on its own chapter page — from the
    // title (e.g. "Fietscheck & sleutelworkshop Schaarbeek" → "Fietscheck &
    // sleutelworkshop") and from the venue, mirroring <x-ride-row>. If stripping would
    // empty the title, keep the original.
    $title = (string) $activity->title_nl;
    $venue = $activity->location;
    if ($commune) {
        $bare = trim((string) preg_replace('/\s{2,}/', ' ', (string) preg_replace('/\b'.preg_quote($commune, '/').'\b/iu', '', $title)), " \t\n\r,–-");
        $title = $bare !== '' ? $bare : $title;
        if ($venue !== null && $venue !== '') {
            $venue = trim((string) preg_replace('/,\s*'.preg_quote($commune, '/').'\s*$/iu', '', $venue));
        }
    }
@endphp
<a
    href="{{ route('activities.show', $activity) }}"
    {{ $attributes->class('other-activity link-plain') }}
>
    <h3 class="other-activity__title">{{ $title }}</h3>
    <p class="other-activity__meta">
        <time datetime="{{ $activity->begin_date->toDateString() }}">{{ $activity->begin_date->isoFormat('D MMMM') }}</time>@if ($venue)<span class="other-activity__sep" aria-hidden="true"> · </span>{{ $venue }}@endif
    </p>
</a>
