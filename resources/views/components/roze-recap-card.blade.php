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
        {{ $photo->img('card', ['class' => 'roze-recap__img', 'alt' => __('roze.recap.photo_alt', ['weekday' => $weekday]), 'loading' => 'eager']) }}
    </span>
    <span class="roze-recap__body">
        <h2 class="roze-recap__title">{{ __('roze.recap.title') }}</h2>
        <span class="roze-recap__meta">{{ trans_choice('roze.recap.meta', $count, ['count' => $count, 'weekday' => $weekday]) }}</span>
        <span class="roze-recap__cta">{{ __('roze.recap.cta') }}
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
        </span>
    </span>
</a>
