<x-roze-hub :group="$group" active="fotos" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    @endphp

    {{-- LIVING SLOT B · FOTO'S — shared chapter album + upload. FAUX shell: Group is not yet
         HasMedia, there is no group gallery. Backend dep: Nico #37 (Group media library). --}}
    <section id="fotos" class="roze-gallery scroll-mt-24">
        <div class="roze-gallery__head">
            <h2 class="roze-hub-title">Foto's van het chapter</h2>
            <button type="button" class="roze-gallery__upload" disabled aria-disabled="true">
                <flux:icon name="arrow-up-tray" variant="micro" class="size-4" /> Foto's toevoegen (binnenkort)
            </button>
        </div>
        <p class="roze-gallery__lead">Het gedeelde album van {{ $gemeente }}. Hier komen de foto's van onze ritten samen.</p>
        <ul role="list" class="roze-gallery__grid">
            @for ($i = 0; $i < 6; $i++)
                <li class="roze-gallery__cell" aria-hidden="true"></li>
            @endfor
        </ul>
    </section>
</x-roze-hub>
