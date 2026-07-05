{{--
    404 — pagina niet gevonden. Warmste en meest gebruikte errorpagina: vangt ook
    dode links van de oude Wix-site op, dus de nav-cards wijzen naar de plekken
    waar bezoekers meestal heen willen.
--}}
<x-layouts::site title="Pagina niet gevonden">

    <x-error-page code="404" title="Oeps, je bent verkeerd gereden" illustration="img/illustrations/heart-30-sign.svg">
        <p>Deze pagina bestaat niet meer, of heeft nooit bestaan. Geen zorgen: zo sta je weer op de route.</p>

        <x-slot:actions>
            <x-cta-button :href="route('home')" variant="secondary" icon="back">Naar de startpagina</x-cta-button>
        </x-slot:actions>
    </x-error-page>

    {{-- De nuttige helft: rechtstreeks naar de populairste bestemmingen. --}}
    <section class="mx-auto mt-16 max-w-4xl">
        <x-section-heading class="text-center">Waar wil je naartoe?</x-section-heading>
        <ul class="mt-8 grid gap-6 sm:grid-cols-3" role="list">
            <li><x-nav-card :href="route('activities.index')" icon="calendar-days" title="Kalender">Vind een rit in jouw buurt.</x-nav-card></li>
            <li><x-nav-card :href="route('groups.index')" icon="map-pin" title="Lokale groepen">Ontdek wie er bij jou in de buurt fietst.</x-nav-card></li>
            <li><x-nav-card :href="route('getting-started')" icon="face-smile" title="Voor het eerst mee">Wat je mag verwachten op een rit.</x-nav-card></li>
        </ul>
    </section>

</x-layouts::site>
