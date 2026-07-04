@props([
    'ride', // past Activity with a non-empty gallery collection
    'href', // the chapter's Foto's page (its picker already defaults to this newest album)
])

@php
    $photo = $ride->getFirstMedia('gallery');
    $count = $ride->getMedia('gallery')->count();
    $weekday = \App\Support\RideDate::weekday($ride->begin_date);
@endphp

<a href="{{ $href }}" class="roze-recap">
    <span class="roze-recap__frame">
        {{ $photo->img('card', ['class' => 'roze-recap__img', 'alt' => "Sfeerbeeld van de rit van {$weekday}", 'loading' => 'eager']) }}
    </span>
    <span class="roze-recap__body">
        <h2 class="roze-recap__title">{{ "Dat was 'm." }}</h2>
        <span class="roze-recap__meta">{{ "{$count} foto's van de rit van {$weekday} staan in het album." }}</span>
        <span class="roze-recap__cta">Bekijk het album
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
        </span>
    </span>
</a>
