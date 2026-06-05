@props(['periodKey', 'rides'])
@php($periodDate = \Illuminate\Support\Carbon::parse($periodKey)->locale('nl'))
<section class="kal-day">
    <h3 class="kal-day__date">
        <time datetime="{{ $periodDate->format('Y-m') }}" class="kal-day__tile kal-day__tile--month">
            <span class="kal-day__num kal-day__num--month">{{ \Illuminate\Support\Str::ucfirst($periodDate->isoFormat('MMMM')) }}</span>
            <span class="kal-day__month">{{ $periodDate->isoFormat('YYYY') }}</span>
        </time>
    </h3>
    <div class="kal-day__cards">
        @foreach ($rides as $activity)
            <x-event-card :activity="$activity" :show-date="false" />
        @endforeach
    </div>
</section>
