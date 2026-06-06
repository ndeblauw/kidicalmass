@props(['periodKey', 'rows'])
@php
    $periodDate = \Illuminate\Support\Carbon::parse($periodKey)->locale('nl');
    $landmark = $periodDate->isToday() ? 'Vandaag'
        : ($periodDate->isTomorrow() ? 'Morgen'
        : (($periodDate->isCurrentWeek() && $periodDate->isWeekend()) ? 'Dit weekend' : null));
    // Compact date: abbreviated day + "D Mon" on two lines
    $dayAbbr = \Illuminate\Support\Str::upper($periodDate->isoFormat('dd'));
    $dayNum  = $periodDate->isoFormat('D MMM');
@endphp
<section class="kal-day">
    <div class="kal-day__date-col">
        <time class="kal-day__date" datetime="{{ $periodDate->toDateString() }}">
            <span class="kal-day__date-dow">{{ $dayAbbr }}</span>
            <span class="kal-day__date-num">{{ $dayNum }}</span>
        </time>
        @if ($landmark)<span class="kal-day__landmark">{{ $landmark }}</span>@endif
    </div>
    <div class="kal-day__rides">
        @foreach ($rows as $row)
            <x-event-card :activity="$row['item']" :show-date="false" />
        @endforeach
    </div>
</section>
