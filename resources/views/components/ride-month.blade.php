@props(['periodKey', 'rides'])

@php($date = \Illuminate\Support\Carbon::parse($periodKey))
<section class="ride-month">
    <h3 class="ride-month__head">
        <time datetime="{{ $date->format('Y-m') }}">{{ \Illuminate\Support\Str::ucfirst(\App\Support\RideDate::monthYear($date)) }}</time>
    </h3>
    <div class="ride-month__rides">
        @foreach ($rides as $activity)
            <x-ride-row :activity="$activity" />
        @endforeach
    </div>
</section>
