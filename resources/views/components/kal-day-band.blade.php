@props(['periodKey', 'rows', 'plain' => false])
@php
    $periodDate = \Illuminate\Support\Carbon::parse($periodKey)->locale('nl');
    $landmark = $periodDate->isToday() ? 'Vandaag'
        : ($periodDate->isTomorrow() ? 'Morgen'
        : (($periodDate->isCurrentWeek() && $periodDate->isWeekend()) ? 'Dit weekend' : null));
@endphp
<section class="kal-day">
    <h3 class="kal-day__date">
        <time datetime="{{ $periodDate->toDateString() }}" class="kal-day__tile">
            <span class="kal-day__eyebrow @if ($landmark) kal-day__eyebrow--landmark @endif">{{ $landmark ?? \Illuminate\Support\Str::ucfirst($periodDate->isoFormat('dddd')) }}</span>
            <span class="kal-day__num">{{ $periodDate->isoFormat('D') }}</span>
            <span class="kal-day__month">{{ $periodDate->isoFormat('MMMM') }}</span>
        </time>
    </h3>
    <div class="kal-day__cards">
        @foreach ($rows as $row)
            @php($activity = $plain ? $row : $row['item'])
            @php($distance = $plain ? null : ($row['distance_km'] ?? null))
            <x-event-card :activity="$activity" :show-date="false" :distance="$distance" />
        @endforeach
    </div>
</section>
