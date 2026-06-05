@props(['periodKey', 'rows', 'plain' => false])
@php
    $periodDate = \Illuminate\Support\Carbon::parse($periodKey)->locale('nl');
    $landmark = $periodDate->isToday() ? 'Vandaag'
        : ($periodDate->isTomorrow() ? 'Morgen'
        : (($periodDate->isCurrentWeek() && $periodDate->isWeekend()) ? 'Dit weekend' : null));
@endphp
<section class="kal-day">
    <h3 class="kal-day__date">
        <time datetime="{{ $periodDate->toDateString() }}">{{ \Illuminate\Support\Str::ucfirst($periodDate->isoFormat('dddd D MMMM')) }}</time>
        @if ($landmark)<span class="kal-day__landmark">{{ $landmark }}</span>@endif
    </h3>
    <div class="kal-day__rides">
        @foreach ($rows as $row)
            @php($activity = $plain ? $row : $row['item'])
            @php($distance = $plain ? null : ($row['distance_km'] ?? null))
            <x-event-card :activity="$activity" :show-date="false" :distance="$distance" />
        @endforeach
    </div>
</section>
