<div
    class="location-picker"
    x-data="{
        locating: false,
        geoError: false,
        locate() {
            this.geoError = false;
            if (! navigator.geolocation) { this.geoError = true; return; }
            this.locating = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => $wire.setFromCoords(pos.coords.latitude, pos.coords.longitude),
                () => { this.locating = false; this.geoError = true; },
            );
        }
    }"
>
    @if ($current && ! $editing)
        <p class="location-picker__current">
            <span class="location-picker__current-pin" aria-hidden="true">
            <svg viewBox="0 0 40 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 2C10.059 2 2 10.059 2 20C2 32 20 52 20 52C20 52 38 32 38 20C38 10.059 29.941 2 20 2Z" fill="var(--color-kidical-red)"/>
                <circle cx="20" cy="20" r="7.5" fill="rgba(0,0,0,0.2)"/>
                <circle cx="20" cy="20" r="4.5" fill="white"/>
            </svg>
        </span>
            <span>Je fietst rond <strong>{{ $current['name'] }}</strong></span>
            <button type="button" wire:click="$set('editing', true)" class="location-picker__change link-plain">wijzig</button>
        </p>
    @else
        {{-- Brand pin anchors the whole control (same teardrop as the ride detail hero). --}}
        <span class="location-picker__pin" aria-hidden="true">
            <svg viewBox="0 0 40 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 2C10.059 2 2 10.059 2 20C2 32 20 52 20 52C20 52 38 32 38 20C38 10.059 29.941 2 20 2Z" fill="var(--color-kidical-red)"/>
                <circle cx="20" cy="20" r="7.5" fill="rgba(0,0,0,0.25)"/>
                <circle cx="20" cy="20" r="4.5" fill="white"/>
            </svg>
        </span>

        <div class="location-picker__main">
            <label class="location-picker__label" for="location-picker-query">Waar wil je fietsen?</label>
            <div class="location-picker__field">
                <input
                    id="location-picker-query"
                    type="text"
                    wire:model.live.debounce.250ms="query"
                    placeholder="Typ postcode of gemeente"
                    autocomplete="off"
                    class="location-picker__input"
                >
                <button type="button" @click="locate()" class="location-picker__geo link-plain" :disabled="locating">
                    <span class="location-picker__geo-inner" x-show="! locating">
                        Gebruik mijn locatie
                    </span>
                    <span class="location-picker__geo-inner" x-show="locating" x-cloak>
                        <svg class="location-picker__spinner" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="42" stroke-dashoffset="14" />
                        </svg> Locatie zoeken…
                    </span>
                </button>
            </div>

            <p class="location-picker__error" x-show="geoError" x-cloak role="alert">
                We konden je locatie niet vinden. Typ je postcode of gemeente.
            </p>

            @if ($suggestions->isNotEmpty())
                <ul class="location-picker__suggestions">
                    @foreach ($suggestions as $pc)
                        <li wire:key="pc-{{ $pc->zip }}">
                            <button type="button" wire:click="choose('{{ $pc->zip }}')" class="location-picker__suggestion link-plain">
                                <strong>{{ $pc->zip }}</strong> <span>{{ $pc->name }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</div>
