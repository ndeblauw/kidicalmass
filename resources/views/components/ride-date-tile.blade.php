@props([
    'date',
    'accent' => null,
    'rotation' => null,
    'grande' => false,
    'weekday' => false,
    'size' => 'default',
])

{{--
    The illustrated tear-off date lockup: navy outline, coloured header bar, big date
    number in the accent colour. Shared by the Kalender agenda (<x-ride-day>) and the
    ride hero. Styling lives in components/ride-day.css under .ride-day__* — the
    established lockup language, also reused by the ride gallery poster.

    - accent:   CSS colour for the bar + date number (e.g. var(--color-kidical-red)).
                Null falls back to the .ride-day__cal default (red).
    - rotation: tilt in degrees. Null uses the deterministic per-date tilt.
    - weekday:  show the weekday line above the date.
    - grande:   show the orange Grande star poking past the card edge.
    - size:     'default' (agenda) or 'lg' (~50% bigger, used as the ride hero anchor).
--}}
@php
    $tileDate = \Illuminate\Support\Carbon::parse($date);
    $rail = \App\Support\RideDate::rail($tileDate);
    $tileRotation = $rotation ?? $rail['rotation'];
    $tileStyle = '--ride-day-rot: '.$tileRotation.'deg;'.($accent ? '--ride-accent: '.$accent.';' : '');
@endphp

<div {{ $attributes->class(['ride-day__cal', 'ride-day__cal--lg' => $size === 'lg'])->merge(['style' => $tileStyle]) }}>
    <time class="ride-day__rail" datetime="{{ $tileDate->toDateString() }}">
        <span class="ride-day__bar" aria-hidden="true"></span>
        <span class="ride-day__body">
            @if ($weekday)
                <span class="ride-day__day">{{ \App\Support\RideDate::weekday($tileDate) }}</span>
            @endif
            <span class="ride-day__date">{{ $rail['num'] }}</span>
            <span class="ride-day__month">{{ $rail['month'] }}</span>
        </span>
    </time>
    @if ($grande)
        <svg class="ride-day__star" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 1.6l3.09 6.26 6.91 1-5 4.87 1.18 6.88L12 17.27l-6.18 3.25L7 13.64l-5-4.87 6.91-1z"/>
        </svg>
    @endif
</div>
