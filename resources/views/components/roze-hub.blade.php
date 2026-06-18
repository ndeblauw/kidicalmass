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
    <header class="roze-hub-hero">
        <h1>Kidical Mass {{ $group->name }}</h1>
        <img src="{{ asset('img/logos/logo-icon.png') }}" alt="" aria-hidden="true" class="roze-hub-hero__mark">
    </header>

    <x-roze-subnav :tabs="$tabs" :group="$group" :beheer-url="$beheerUrl" />

    <div class="roze-hub-body">
        {{ $slot }}
    </div>
</x-layouts::site>
