{{-- 403 — hier komt vooral een begeleider terecht die de roze-hesjespagina
     opent zonder ingelogd te zijn. Ga uit van goede wil: nodig uit om in te
     loggen in plaats van te blokkeren. --}}
<x-layouts::site title="Alleen voor begeleiders">

    <x-error-page code="403" title="Deze pagina is voor begeleiders" illustration="img/illustrations/heart-sign-holder.svg">
        <p>Ben jij begeleider bij een lokale groep? Log dan even in, daarna kan je meteen verder.</p>

        <x-slot:actions>
            <x-cta-button :href="route('login')">Inloggen</x-cta-button>
            <x-cta-button :href="route('home')" variant="ghost" icon="back">Naar de startpagina</x-cta-button>
        </x-slot:actions>
    </x-error-page>

</x-layouts::site>
