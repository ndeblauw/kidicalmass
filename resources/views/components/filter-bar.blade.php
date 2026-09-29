{{-- Full-bleed filter bar pinned to the top of a page panel. Hosts the compact
     location picker; the slot stays open for extra page controls, none today. --}}
<div class="filter-bar">
    <div class="filter-bar__loc">
        <livewire:location-picker />
    </div>

    {{ $slot }}
</div>
