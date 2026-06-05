<div
    class="location-picker"
    x-data="{
        locate() {
            if (! navigator.geolocation) { return; }
            navigator.geolocation.getCurrentPosition(
                (pos) => $wire.setFromCoords(pos.coords.latitude, pos.coords.longitude),
                () => {},
            );
        }
    }"
>
    @if ($current && ! $editing)
        <p class="location-picker__current">
            <flux:icon.map-pin variant="solid" aria-hidden="true" />
            Je fietst rond <strong>{{ $current['name'] }}</strong>
            <button type="button" wire:click="$set('editing', true)" class="location-picker__change link-plain">wijzig</button>
        </p>
    @else
        <label class="location-picker__label" for="location-picker-query">Waar woon je?</label>
        <div class="location-picker__field">
            <input
                id="location-picker-query"
                type="text"
                wire:model.live.debounce.250ms="query"
                placeholder="Postcode of gemeente"
                autocomplete="off"
                class="location-picker__input"
            >
            <button type="button" @click="locate()" class="location-picker__geo link-plain">
                <flux:icon.map-pin aria-hidden="true" /> Gebruik mijn locatie
            </button>
        </div>

        @if ($suggestions->isNotEmpty())
            <ul class="location-picker__suggestions">
                @foreach ($suggestions as $pc)
                    <li wire:key="pc-{{ $pc->zip }}">
                        <button type="button" wire:click="choose('{{ $pc->zip }}')" class="location-picker__suggestion link-plain">
                            {{ $pc->zip }} <span>{{ $pc->name }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    @endif
</div>
