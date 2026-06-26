@props([
    'group',
    'active',
    'isCaptain' => false,
    'showWelcome' => false,
    'beheerUrl' => null,
])

@php
    $tabs = \App\Support\RozeHub\HubTabs::for($group, $active, (bool) $isCaptain, (bool) $showWelcome);
    $place = \Illuminate\Support\Str::of($group->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim();
@endphp

<x-layouts::site title="Kidical Mass {{ $group->name }}">
    {{-- App-shell chrome in the header's place: one slim bar + the hub tabs,
         sticky together. The red marketing nav is gone here (member workspace). --}}
    <x-slot:navbar>
        <div class="roze-chrome">
            <x-roze-shell-bar :group="$group" />
            <x-roze-subnav :tabs="$tabs" :group="$group" :beheer-url="$beheerUrl" />
        </div>
    </x-slot:navbar>

    <div class="roze-hub-body">
        <h1 class="sr-only">Roze hesjes van {{ $place }}</h1>
        {{ $slot }}
    </div>

    {{-- Slim member footer in place of the public marketing footer: a calm way back
         to the public site, the chapter context, and a help hand-off. --}}
    <x-slot:footer>
        <footer class="roze-foot">
            <div class="roze-foot__inner">
                <a href="{{ route('home') }}" class="roze-foot__home">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <span>Terug naar de website</span>
                </a>
                <span class="roze-foot__meta">
                    <span class="roze-foot__place">Roze hesjes {{ $place }}</span>
                    <a href="{{ route('contact') }}" class="roze-foot__help">Hulp nodig?</a>
                </span>
            </div>
        </footer>
    </x-slot:footer>
</x-layouts::site>
