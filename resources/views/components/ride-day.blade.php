@props(['periodKey', 'rows'])

@php
    $date = \Illuminate\Support\Carbon::parse($periodKey);
    $rail = \App\Support\RideDate::rail($date);
@endphp
<section class="ride-day">
    <time class="ride-day__rail" datetime="{{ $date->toDateString() }}">
        <span class="ride-day__num">{{ $rail['num'] }}</span>
        <span class="ride-day__mon">{{ $rail['month'] }}</span>
        <span class="ride-day__dow">{{ $rail['dow'] }}</span>
    </time>
    <div class="ride-day__rides">
        @foreach ($rows as $row)
            <x-ride-row :activity="$row['item']" />
        @endforeach
    </div>
</section>
