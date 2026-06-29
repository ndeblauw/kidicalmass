<x-roze-hub :group="$group" active="fotos" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl" :own-heading="true">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    @endphp

    {{-- FOTO'S — one album per ride, newest shown by default, the picker pages back through
         the season. Wired to each ride's `gallery` media collection; upload is still faux
         (Nico #37). --}}
    <section
        class="roze-gallery scroll-mt-24"
        @if ($rides->isNotEmpty()) x-data="{ ride: '{{ $rides->first()->id }}' }" @endif
    >
        <div class="roze-gallery__head">
            <div>
                <h1 class="roze-hub-title">Foto's van {{ $gemeente }}</h1>
                <p class="roze-hub-lead">Het gedeelde album van {{ $gemeente }}, rit per rit. Hier komen de foto's van onze tochten samen.</p>
            </div>
        </div>
        {{-- Quiet, honest "binnenkort" affordance, below the intro so the title leads cleanly. --}}
        <button type="button" class="roze-gallery__upload" disabled aria-disabled="true">
            <flux:icon name="arrow-up-tray" variant="micro" class="size-4" /> Foto's toevoegen (binnenkort)
        </button>

        @if ($rides->isEmpty())
            <div class="roze-gallery__empty">
                <p class="roze-row-title">Nog geen foto's</p>
                <p>Na de eerste rit verschijnen hier de albums. Neem gerust je toestel mee.</p>
            </div>
        @else
            @if ($rides->count() > 1)
                <div class="roze-gallery__picker">
                    <label for="roze-gallery-ride">Kies een rit</label>
                    <div class="roze-gallery__select-wrap">
                        <select id="roze-gallery-ride" x-model="ride" class="roze-gallery__select">
                            @foreach ($rides as $ride)
                                @php $photoCount = $ride->getMedia('gallery')->count(); @endphp
                                <option value="{{ $ride->id }}">{{ ucfirst($ride->date_full) }} &middot; {{ trans_choice(':count foto|:count foto\'s', $photoCount, ['count' => $photoCount]) }}</option>
                            @endforeach
                        </select>
                        <flux:icon name="chevron-down" class="roze-gallery__select-chev size-4" aria-hidden="true" />
                    </div>
                </div>
            @endif

            @foreach ($rides as $ride)
                <div x-show="ride === '{{ $ride->id }}'" x-cloak class="roze-gallery__album">
                    <x-ride-gallery
                        :photos="$ride->getMedia('gallery')"
                        :date="$ride->begin_date"
                        :commune="$gemeente"
                    />
                </div>
            @endforeach
        @endif
    </section>
</x-roze-hub>
