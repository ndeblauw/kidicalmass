@props(['band', 'radiusKm' => null, 'byDay'])

{{-- One distance band on the calendar (nearby, region or far), rides by day inside.
     The subline names the band's radius, taken from config via the caller. --}}
<section data-proximity-section="{{ $band }}" aria-labelledby="proximity-{{ $band }}">
    <header class="mb-6">
        <h2 id="proximity-{{ $band }}">{{ __("calendar.proximity.$band.title") }}</h2>
        <p class="mt-1 text-lg leading-snug text-text-body">{{ __("calendar.proximity.$band.sub", ['km' => \Illuminate\Support\Number::format((float) $radiusKm, maxPrecision: 1, locale: app()->getLocale())]) }}</p>
    </header>
    {{-- Day-to-day rhythm comes from .ride-day + .ride-day, same as the plain list. --}}
    <div>
        @foreach ($byDay as $dayKey => $rows)
            <x-ride-day :period-key="$dayKey" :rows="$rows" />
        @endforeach
    </div>
</section>
