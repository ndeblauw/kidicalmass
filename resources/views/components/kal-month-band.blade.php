@props(['periodKey', 'rides'])
@php($periodDate = \Illuminate\Support\Carbon::parse($periodKey)->locale('nl'))
<section class="kal-day">
    <h3 class="kal-day__date">
        <time datetime="{{ $periodDate->format('Y-m') }}">{{ \Illuminate\Support\Str::ucfirst($periodDate->isoFormat('MMMM YYYY')) }}</time>
    </h3>
    <div class="kal-day__rides">
        @foreach ($rides as $activity)
            <x-event-card :activity="$activity" :show-date="false" />
        @endforeach
    </div>
</section>
