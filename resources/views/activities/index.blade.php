{{--
    Kalender (P-02) — rides-only calendar.
    The <livewire:ride-calendar> owns the blue header band so the location-first
    "Waar fiets je?" picker can sit inside it as the hero control, plus the grouped
    agenda. Structure only; appearance in app.css.
    Plan: docs/wiki/design/30-skeleton/events-overview.md
--}}
<x-layouts::site title="Kalender" :description="__('meta.calendar')">

    <livewire:ride-calendar />

</x-layouts::site>
