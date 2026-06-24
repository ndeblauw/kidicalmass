@props([
    'activity',          // App\Models\Activity — the soonest upcoming kidicalmass ride
    'commune' => null,   // gemeente name, for context (not printed by default)
])

{{--
    The chapter page's "next ride" feature card (§2) — a custom, richer alternative to
    the list <x-ride-row>. The WHOLE card is a single link to the ride's detail page; a
    "Bekijk deze rit" cue + hover lift make that one affordance explicit. Deliberately
    nothing else is clickable inside it — the "stay informed" CTA is decoupled and lives
    as a quiet line BENEATH the card (in groups/show.blade.php), so the two destinations
    never compete. Appearance in resources/css/components/next-ride.css.

    The route map renders the ride's real GPX track (via <x-route-map>, shared with the
    ride detail page) as a static, non-interactive preview — clicks fall through to the
    card link. Rides without a GPX file fall back to the stylised brand placeholder.
    The km figure reads the real, optional `distance` string and is dropped when empty.
--}}
@php
    $href = route('activities.show', $activity);
    $routeCoords = $activity->route_coordinates;
    $dateHeadline = \Illuminate\Support\Str::ucfirst($activity->dateFull); // "Zondag 28 juni"

    // Drop a trailing ", {gemeente}" — redundant on the commune's own chapter page
    // (mirrors <x-ride-row>'s venue cleaning).
    $location = $activity->location;
    if ($commune) {
        $location = (string) preg_replace('/,\s*'.preg_quote($commune, '/').'\s*$/i', '', $location);
    }

    $distance = filled($activity->distance) ? trim($activity->distance) : null;
    $desc = filled($activity->content_nl)
        ? \Illuminate\Support\Str::limit(strip_tags($activity->content_nl), 120)
        : null;
@endphp

<article {{ $attributes->class('next-ride') }}>
    <a href="{{ $href }}" class="next-ride__body link-plain">
        <div class="next-ride__main">
            <p class="next-ride__eyebrow">Volgende rit</p>
            <h2 class="next-ride__date">
                <time datetime="{{ $activity->begin_date->toDateString() }}">{{ $dateHeadline }}</time>
            </h2>

            <dl class="next-ride__meta">
                <div class="next-ride__meta-item">
                    <x-icon-chip color="blue" size="sm" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    </x-icon-chip>
                    <dt class="sr-only">Vertrek</dt>
                    <dd><time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ $activity->timeLabel }}</time></dd>
                </div>

                <div class="next-ride__meta-item">
                    <x-icon-chip color="red" size="sm" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    </x-icon-chip>
                    <dt class="sr-only">Vertrekplaats</dt>
                    <dd>{{ $location }}</dd>
                </div>

                @if ($distance)
                    <div class="next-ride__meta-item">
                        <x-icon-chip color="green" size="sm" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18.5" cy="17.5" r="3.5"/><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="15" cy="5" r="1"/><path d="M12 17.5V14l-3-3 4-3 2 3h2"/></svg>
                        </x-icon-chip>
                        <dt class="sr-only">Afstand</dt>
                        <dd>{{ $distance }}</dd>
                    </div>
                @endif
            </dl>

            @if ($desc)
                <p class="next-ride__desc">{{ $desc }}</p>
            @endif

            <span class="next-ride__more">
                Bekijk deze rit
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </div>

        @if (count($routeCoords) > 0)
            {{-- Real GPX route — a static preview; the whole card still links to detail. --}}
            <x-route-map :coordinates="$routeCoords" :interactive="false" class="next-ride__map" aria-hidden="true" />
        @else
            {{-- FAUX stylised route map — fallback when the ride has no GPX file yet. --}}
            <div class="next-ride__map" aria-hidden="true">
                <svg class="next-ride__map-svg" viewBox="0 0 440 320" preserveAspectRatio="xMidYMid slice">
                    <path class="next-ride__route" d="M50 270 C 120 260, 150 210, 200 200 S 300 180, 330 120 400 75 405 45" fill="none"/>
                    <circle class="next-ride__route-wp" cx="200" cy="200" r="5"/>
                    <circle class="next-ride__route-wp" cx="330" cy="120" r="5"/>
                    <circle class="next-ride__route-start" cx="50" cy="270" r="10"/>
                    <g transform="translate(396 28)">
                        <path class="next-ride__route-pin" d="M9 0C4 0 0 4 0 9c0 6.5 9 17 9 17s9-10.5 9-17c0-5-4-9-9-9Z"/>
                        <circle class="next-ride__route-pin-dot" cx="9" cy="9" r="3.4"/>
                    </g>
                </svg>
            </div>
        @endif
    </a>
</article>
