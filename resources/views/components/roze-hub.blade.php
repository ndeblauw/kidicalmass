@props([
    'group',
    'active',
    'isCaptain' => false,
    'showWelcome' => false,
    'beheerUrl' => null,
])

@php
    $tabs = \App\Support\RozeHub\HubTabs::for($group, $active, (bool) $isCaptain, (bool) $showWelcome);
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
        {{ $slot }}
    </div>
</x-layouts::site>
