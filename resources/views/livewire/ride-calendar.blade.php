<div>
    <x-page-hero
        eyebrow="Kalender"
        title="Spring op de fiets, wij rijden samen."
        illustration="img/illustrations/kid-on-bike.png">

        {{-- Filter row: location picker + radius tabs. Hidden on past-rides view. --}}
        @if ($when !== 'voorbije')
            <div class="kal-filterrow">
                <div class="kal-filterrow__loc">
                    <livewire:location-picker />
                </div>

                <div class="kal-filterrow__sep" aria-hidden="true"></div>

                <div class="kal-filterrow__radius">
                    @if ($location)
                        <div class="kal-filterrow__tabs">
                            <button
                                type="button"
                                wire:click="setRadius('dichtbij')"
                                class="kal-filterrow__tab{{ $radius === 'dichtbij' ? ' kal-filterrow__tab--active' : '' }}"
                            >Dicht bij</button>
                            <button
                                type="button"
                                wire:click="setRadius('regio')"
                                class="kal-filterrow__tab{{ $radius === 'regio' ? ' kal-filterrow__tab--active' : '' }}"
                            >Ruimere regio</button>
                            <button
                                type="button"
                                wire:click="setRadius('belgie')"
                                class="kal-filterrow__tab{{ $radius === 'belgie' ? ' kal-filterrow__tab--active' : '' }}"
                            >Heel België</button>
                        </div>
                    @else
                        <p class="kal-filterrow__radius-hint">Hoe ver wil je kijken?</p>
                        <div class="kal-filterrow__tabs kal-filterrow__tabs--disabled" aria-hidden="true">
                            <span class="kal-filterrow__tab">Dicht bij</span>
                            <span class="kal-filterrow__tab">Ruimere regio</span>
                            <span class="kal-filterrow__tab">Heel België</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Two-column body: agenda left, sticky sidebar right. --}}
        <div class="kal-body">
            <div class="kal-agenda">

                @if (! $hasActivities)
                    <p class="kal-empty">
                        @if ($when === 'voorbije')
                            Er zijn nog geen voorbije fietstochten om te tonen.
                        @else
                            Er zijn momenteel geen fietstochten gepland. Het seizoen loopt van maart tot november. Kom snel terug!
                        @endif
                    </p>

                @elseif ($when === 'voorbije')
                    <div class="kal-days">
                        @foreach ($byPeriod as $periodKey => $rides)
                            <x-kal-month-band :period-key="$periodKey" :rides="$rides" />
                        @endforeach
                    </div>

                @elseif ($isEmpty)
                    @php
                        $radiusLabel = match($radius) {
                            'regio'  => 'Ruimere regio',
                            'belgie' => 'Heel België',
                            default  => 'Dicht bij',
                        };
                    @endphp
                    <p class="kal-empty">
                        Geen ritten in de categorie "{{ $radiusLabel }}" van {{ $location['name'] }}.
                        Kies een ruimere regio om meer te zien.
                    </p>

                @else
                    <div class="kal-days">
                        @foreach ($byPeriod as $periodKey => $rows)
                            <x-kal-day-band :period-key="$periodKey" :rows="$rows" />
                        @endforeach
                    </div>
                @endif

                {{-- Past-rides link at bottom of agenda --}}
                <div class="kal-pastbar">
                    @if ($when === 'aankomend')
                        <button type="button" wire:click="showPast" class="kal-pastlink">Bekijk voorbije ritten →</button>
                    @else
                        <button type="button" wire:click="showUpcoming" class="kal-pastlink">← Terug naar aankomende ritten</button>
                    @endif
                </div>

            </div>{{-- /.kal-agenda --}}

            {{-- Sticky sidebar (desktop only; hidden on mobile via CSS) --}}
            @if ($when !== 'voorbije')
                <aside class="kal-sidebar">
                    @if ($location)
                        {{-- Newsletter CTA --}}
                        <div class="kal-sidebar__panel kal-sidebar__panel--newsletter">
                            <h3 class="kal-sidebar__heading">Mis geen rit</h3>
                            <p class="kal-sidebar__body">Één seintje per maand met ritten bij jou in de buurt. Geen spam, altijd uitschrijfbaar.</p>
                            <button type="button" class="kal-sidebar__btn">Schrijf je in</button>
                        </div>
                    @else
                        {{-- Location nudge --}}
                        <div class="kal-sidebar__panel kal-sidebar__panel--nudge">
                            <flux:icon.map-pin variant="solid" class="kal-sidebar__nudge-icon" aria-hidden="true" />
                            <p class="kal-sidebar__body">Stel je buurt in en zie alleen de ritten bij jou in de buurt.</p>
                            <button
                                type="button"
                                class="kal-sidebar__btn"
                                @click="$dispatch('focus-picker')"
                            >Stel locatie in</button>
                        </div>
                    @endif
                </aside>
            @endif

        </div>{{-- /.kal-body --}}

    </x-page-hero>
</div>
