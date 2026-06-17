<div
    class="location-picker {{ $compact ? 'location-picker--compact' : '' }}"
    x-data="{
        locating: false,
        geoError: false,
        init() {
            // Picking a location triggers a navigate to the same page; restore the scroll
            // position we stashed just before, so the user stays put instead of jumping to the top.
            const y = sessionStorage.getItem('lp-scroll');
            if (y !== null) {
                sessionStorage.removeItem('lp-scroll');
                requestAnimationFrame(() => requestAnimationFrame(() => window.scrollTo(0, parseInt(y, 10))));
            }
        },
        stashScroll() { sessionStorage.setItem('lp-scroll', window.scrollY); },
        options() { return [...this.$root.querySelectorAll('[data-option]')]; },
        focusFirst() { this.options()[0]?.focus(); },
        move(dir, current) {
            const opts = this.options();
            if (! opts.length) { return; }
            const i = opts.indexOf(current);
            (dir > 0 ? (opts[i + 1] ?? this.$refs.input) : (i <= 0 ? this.$refs.input : opts[i - 1])).focus();
        },
        dismiss() { this.$refs.input.focus(); this.$wire.set('query', ''); },
        locate() {
            this.geoError = false;
            if (! navigator.geolocation) { this.geoError = true; return; }
            this.locating = true;
            this.stashScroll();
            navigator.geolocation.getCurrentPosition(
                (pos) => $wire.setFromCoords(pos.coords.latitude, pos.coords.longitude),
                () => { this.locating = false; this.geoError = true; sessionStorage.removeItem('lp-scroll'); },
            );
        }
    }"
    @focus-picker.window="$wire.set('editing', true); $nextTick(() => $el.querySelector('input')?.focus())"
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
            <label class="location-picker__label" for="location-picker-query">
                {{-- Compact view only: the teardrop pin anchors the prompt where the big pin is hidden. --}}
                <span class="location-picker__label-pin" aria-hidden="true">
                    <svg viewBox="0 0 40 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 2C10.059 2 2 10.059 2 20C2 32 20 52 20 52C20 52 38 32 38 20C38 10.059 29.941 2 20 2Z" fill="var(--color-kidical-red)"/>
                        <circle cx="20" cy="20" r="7.5" fill="rgba(0,0,0,0.25)"/>
                        <circle cx="20" cy="20" r="4.5" fill="white"/>
                    </svg>
                </span>
                Waar wil je fietsen?
            </label>
            <div class="location-picker__field">
                <input
                    id="location-picker-query"
                    x-ref="input"
                    type="text"
                    role="combobox"
                    aria-autocomplete="list"
                    aria-controls="location-picker-suggestions"
                    aria-expanded="{{ $suggestions->isNotEmpty() ? 'true' : 'false' }}"
                    wire:model.live.debounce.250ms="query"
                    placeholder="Typ postcode of gemeente"
                    autocomplete="off"
                    class="location-picker__input"
                    x-on:keydown.down.prevent="focusFirst()"
                    x-on:keydown.escape.prevent="$wire.set('query', '')"
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
                <ul id="location-picker-suggestions" role="listbox" aria-label="Voorgestelde gemeentes" class="location-picker__suggestions">
                    @foreach ($suggestions as $pc)
                        <li role="presentation" wire:key="pc-{{ $pc->zip }}">
                            <button
                                type="button"
                                role="option"
                                aria-selected="false"
                                data-option
                                wire:click="choose('{{ $pc->zip }}')"
                                class="location-picker__suggestion link-plain"
                                x-on:click="stashScroll()"
                                x-on:focus="$el.setAttribute('aria-selected', true)"
                                x-on:blur="$el.setAttribute('aria-selected', false)"
                                x-on:keydown.down.prevent="move(1, $el)"
                                x-on:keydown.up.prevent="move(-1, $el)"
                                x-on:keydown.escape.prevent="dismiss()"
                            >
                                <strong>{{ $pc->zip }}</strong> <span>{{ $pc->name }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</div>
