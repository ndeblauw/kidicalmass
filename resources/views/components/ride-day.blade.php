@props(['periodKey', 'rows', 'commune' => null])

@php
    $date = \Illuminate\Support\Carbon::parse($periodKey);
    $rail = \App\Support\RideDate::rail($date);

    // A day earns the Grande star when any of its rides is the flagship edition.
    $isGrande = collect($rows)->contains(fn ($row) => $row['item']->isGrande());

    // The lockup's accent reflects what the day is for. When a day mixes types,
    // the ride wins, then workshop, then meeting, then other.
    $accentType = collect($rows)
        ->map(fn ($row) => $row['item']->activity_type)
        ->sortBy(fn ($type) => match ($type) {
            \App\Enums\ActivityType::KIDICALMASS => 0,
            \App\Enums\ActivityType::WORKSHOP => 1,
            \App\Enums\ActivityType::MEETING => 2,
            default => 3,
        })
        ->first();
@endphp
<section class="ride-day">
    {{-- Wrapper carries the tilt so the star rotates with the card yet sits outside the
         card's clipped edge (the card hides overflow to round the header bar). --}}
    <div class="ride-day__cal" style="--ride-day-rot: {{ $rail['rotation'] }}deg;@if ($accentType) --ride-accent: {{ $accentType->accentColor() }};@endif">
        <time class="ride-day__rail" datetime="{{ $date->toDateString() }}">
            <span class="ride-day__bar" aria-hidden="true"></span>
            <span class="ride-day__body">
                <span class="ride-day__date">{{ $rail['num'] }}</span>
                <span class="ride-day__month">{{ $rail['month'] }}</span>
            </span>
        </time>
        @if ($isGrande)
            <svg class="ride-day__star" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 1.6l3.09 6.26 6.91 1-5 4.87 1.18 6.88L12 17.27l-6.18 3.25L7 13.64l-5-4.87 6.91-1z"/>
            </svg>
        @endif
    </div>
    <div class="ride-day__rides">
        @foreach ($rows as $row)
            <x-ride-row :activity="$row['item']" :commune="$commune" />
        @endforeach
    </div>
</section>
