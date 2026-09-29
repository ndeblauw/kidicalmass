@props(['variant' => 'desktop'])

@php
    // SetLocale::DISPLAY holds the order the client asked for; only live
    // locales (SetLocale::SUPPORTED) are offered, so EN stays hidden until its
    // routes exist. A live locale reads as disabled on a page that has no
    // counterpart in it (alternate_locale_url returns null).
    $current = app()->getLocale();

    $items = collect(\App\Http\Middleware\SetLocale::DISPLAY)
        ->intersect(\App\Http\Middleware\SetLocale::SUPPORTED)
        ->map(fn (string $locale): array => [
            'locale' => $locale,
            'label' => strtoupper($locale),
            'name' => \App\Http\Middleware\SetLocale::NAMES[$locale] ?? strtoupper($locale),
            'url' => alternate_locale_url($locale),
            'active' => $locale === $current,
        ]);
@endphp

@if ($variant === 'desktop')
    {{-- Desktop: one compact tile (current code + chevron) that discloses the
         list, so the header does not grow as languages are added. --}}
    @php($active = $items->firstWhere('active', true))
    <nav class="lang-menu" aria-label="{{ __('nav.language') }}"
         x-data="{ open: false }"
         x-on:click.outside="open = false"
         x-on:keydown.escape.stop="open = false; $refs.trigger.focus()">
        <button type="button" x-ref="trigger" class="lang-menu__trigger"
                x-on:click="open = ! open"
                aria-expanded="false" x-bind:aria-expanded="open.toString()"
                aria-controls="lang-menu-list"
                aria-label="{{ __('nav.language') }}: {{ $active['name'] ?? '' }}">
            <span aria-hidden="true">{{ $active['label'] ?? '' }}</span>
            <flux:icon.chevron-down variant="micro" class="lang-menu__chevron" aria-hidden="true" x-bind:class="open && 'lang-menu__chevron--open'" />
        </button>
        <ul id="lang-menu-list" class="lang-menu__list" x-show="open" x-cloak x-transition.opacity.duration.100ms>
            @foreach ($items as $item)
                <li>
                    @if ($item['active'])
                        <span class="lang-menu__item lang-menu__item--active" aria-current="true" lang="{{ $item['locale'] }}">
                            {{ $item['name'] }} <span class="lang-menu__code" aria-hidden="true">{{ $item['label'] }}</span>
                        </span>
                    @elseif ($item['url'] === null)
                        <span class="lang-menu__item lang-menu__item--disabled" aria-disabled="true" lang="{{ $item['locale'] }}"
                              title="{{ __('nav.coming_soon') }}">
                            {{ $item['name'] }} <span class="lang-menu__code" aria-hidden="true">{{ $item['label'] }}</span>
                        </span>
                    @else
                        <a href="{{ $item['url'] }}" class="lang-menu__item" hreflang="{{ $item['locale'] }}" lang="{{ $item['locale'] }}">
                            {{ $item['name'] }} <span class="lang-menu__code" aria-hidden="true">{{ $item['label'] }}</span>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
@else
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
@endif
