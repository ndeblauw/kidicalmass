@props(['variant' => 'desktop'])

@php
    // FR/EN/NL in the order the client asked. A locale is a live link only when
    // the current page has a route in it; otherwise it reads as disabled (EN
    // until its routes exist, FR on pages still without a counterpart).
    $current = app()->getLocale();

    $items = collect(['fr', 'en', 'nl'])->map(fn (string $locale): array => [
        'locale' => $locale,
        'label' => strtoupper($locale),
        'url' => alternate_locale_url($locale),
        'active' => $locale === $current,
    ]);
@endphp

<nav class="lang-switch lang-switch--{{ $variant }}" aria-label="{{ __('nav.language') }}">
    @foreach ($items as $item)
        @if ($item['active'])
            <span class="lang-switch__item lang-switch__item--active" aria-current="true">{{ $item['label'] }}</span>
        @elseif ($item['url'] === null)
            <span class="lang-switch__item lang-switch__item--disabled"
                  aria-disabled="true"
                  title="{{ __('nav.coming_soon') }}">{{ $item['label'] }}</span>
        @else
            <a href="{{ $item['url'] }}"
               class="lang-switch__item"
               hreflang="{{ $item['locale'] }}"
               lang="{{ $item['locale'] }}">{{ $item['label'] }}</a>
        @endif
    @endforeach
</nav>
