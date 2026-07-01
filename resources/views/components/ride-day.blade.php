@props(['periodKey', 'rows', 'commune' => null])

@php
    $date = \Illuminate\Support\Carbon::parse($periodKey);

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
    {{-- The tear-off carries the tilt so the Grande star rotates with the card yet sits
         outside the card's clipped edge (the card hides overflow to round the header bar). --}}
    <x-ride-date-tile
        :date="$date"
        :accent="$accentType?->accentColor()"
        :grande="$isGrande" />
    <div class="ride-day__rides">
        @foreach ($rows as $row)
            <x-ride-row :activity="$row['item']" :commune="$commune" />
        @endforeach
    </div>
</section>
