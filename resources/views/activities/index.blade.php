{{--
    Kalender (P-02) — rides-only calendar.
    Slim header + <livewire:ride-calendar> (location-first searchable filter + agenda) +
    honest "binnenkort" opt-in band. Structure only; appearance in app.css.
    Plan: docs/wiki/design/30-skeleton/events-overview.md
--}}
<x-layouts::site title="Kalender">

    {{-- HEADER — slim blue band (scanner page; the list is the point) --}}
    <section class="index-hero">
        <img src="{{ asset('img/logo-icon.png') }}" alt="" aria-hidden="true" class="index-hero__daisy">

        <div class="container mx-auto px-4 index-hero__inner">
            <span class="kal-hero__badge">
                <flux:icon.map-pin variant="solid" aria-hidden="true" />
                In heel België
            </span>
            <h1>Kalender</h1>
            <p class="kal-hero__lead">Vind een fietstocht bij jou in de buurt.</p>
        </div>
    </section>

    <div class="kal-body">
        <livewire:ride-calendar />
    </div>

    {{-- CLOSING — "Mis geen fietstocht" opt-in, honestly flagged "binnenkort" (no backend yet) --}}
    <section class="kal-optin">
        <div class="container mx-auto px-4 kal-optin__inner">
            <span class="kal-optin__badge">Binnenkort</span>
            <h2>Mis geen fietstocht</h2>
            <p class="kal-optin__sub">Straks kan je je inschrijven voor één seintje per maand met de fietstochten bij jou in de buurt. Geen spam, altijd uitschrijfbaar.</p>
        </div>
    </section>

</x-layouts::site>
