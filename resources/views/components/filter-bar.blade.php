{{-- Full-bleed filter bar pinned to the top of a page panel. Hosts the compact
     location picker; pass extra controls (e.g. the agenda radius tabs) as the slot. --}}
<div class="filter-bar">
    <div class="filter-bar__loc">
        <livewire:location-picker :compact="true" />
    </div>

    {{ $slot }}
</div>
