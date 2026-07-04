{{-- 419 — verlopen sessie, meestal een contactformulier dat te lang openstond.
     history.back() als inline onclick: de publieke site verscheept geen JS-framework,
     en x-cta-button zonder href rendert een <button> waar het attribuut op landt. --}}
<x-layouts::site title="Even opnieuw proberen">

    <x-error-page code="419" title="Je was er even tussenuit" illustration="img/illustrations/relaxed-rider.svg">
        <p>Deze pagina stond te lang open. Ga terug en probeer het opnieuw, meer is het niet.</p>

        <x-slot:actions>
            <x-cta-button onclick="history.back()" icon="back">Ga terug</x-cta-button>
            <x-cta-button :href="route('home')" variant="ghost">Naar de startpagina</x-cta-button>
        </x-slot:actions>
    </x-error-page>

</x-layouts::site>
