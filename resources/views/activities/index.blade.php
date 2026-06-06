{{--
    Kalender (P-02) — rides-only calendar.
    The <livewire:ride-calendar> owns the blue header band so the location-first
    "Waar fiets je?" picker can sit inside it as the hero control, plus the grouped
    agenda. Followed by the honest "binnenkort" opt-in band. Structure only; appearance in app.css.
    Plan: docs/wiki/design/30-skeleton/events-overview.md
--}}
<x-layouts::site title="Kalender">

    <livewire:ride-calendar />

    <x-slot:closing>
        <section class="kal-optin relative z-10">
            <div class="container mx-auto px-4 kal-optin__inner">
                <span class="kal-optin__badge">Binnenkort</span>
                <h2>Mis geen fietstocht</h2>
                <p class="kal-optin__sub">Straks kan je je inschrijven voor één seintje per maand met de fietstochten bij jou in de buurt. Geen spam, altijd uitschrijfbaar.</p>
            </div>
        </section>
    </x-slot:closing>

</x-layouts::site>
