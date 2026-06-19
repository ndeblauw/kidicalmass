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
</x-layouts::site>
