<x-layouts::site title="Je bent erbij">
    <section class="flex flex-col items-center gap-6 text-center max-w-2xl mx-auto py-16">
        <x-envelope-chips />

        <h1>Je bent erbij!</h1>

        <p class="max-w-xl">
            Vanaf nu mis je niets meer. Eén keer per maand laten we je weten
            waar er bij jou in de buurt gefietst wordt.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <x-cta-button :href="route('activities.index')" variant="yellow" icon="arrow">Bekijk de kalender</x-cta-button>
            <x-cta-button :href="route('groups.index')" variant="secondary" icon="arrow">Vind je groep</x-cta-button>
        </div>
    </section>
</x-layouts::site>
