@props(['periodKey', 'rides'])

@php($date = \Illuminate\Support\Carbon::parse($periodKey))
<section class="ride-month">
    <h2 class="ride-month__head">
        <time datetime="{{ $date->format('Y-m') }}">{{ \Illuminate\Support\Str::ucfirst(\App\Support\RideDate::monthYear($date)) }}</time>
    </h2>
    <div class="ride-month__days">
        @foreach (collect($rides)->groupBy(fn ($activity) => $activity->begin_date->toDateString()) as $dayKey => $dayRides)
            <x-ride-day :period-key="$dayKey" :rows="$dayRides->map(fn ($activity) => ['item' => $activity])->values()->all()" />
        @endforeach
    </div>
</section>
