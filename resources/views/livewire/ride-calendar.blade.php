<div>
    <x-page-hero
        eyebrow="Kalender"
        title="Spring op de fiets, wij rijden samen."
        illustration="img/illustrations/cargo-bike-family.svg">

        {{-- Filter row: location picker + radius tabs. Hidden on past-rides view. --}}
        @if ($when !== 'voorbije')
            <div class="kal-filterrow">
                <div class="kal-filterrow__loc">
                    <livewire:location-picker />
                </div>

                @if ($location)
                    <div class="kal-filterrow__radius">
                        <span class="kal-filterrow__sep" aria-hidden="true">·</span>
                        <span class="kal-filterrow__radius-label">Hoe ver</span>
                        <div class="kal-filterrow__tabs">
                            <button
                                type="button"
                                wire:click="setRadius('dichtbij')"
                                class="kal-filterrow__tab{{ $radius === 'dichtbij' ? ' kal-filterrow__tab--active' : '' }}"
                            >Dichtbij</button>
                            <button
                                type="button"
                                wire:click="setRadius('regio')"
                                class="kal-filterrow__tab{{ $radius === 'regio' ? ' kal-filterrow__tab--active' : '' }}"
                            >In de regio</button>
                            <button
                                type="button"
                                wire:click="setRadius('belgie')"
                                class="kal-filterrow__tab{{ $radius === 'belgie' ? ' kal-filterrow__tab--active' : '' }}"
                            >Heel België</button>
                        </div>
                    </div>
                @endif
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
                            <x-ride-month :period-key="$periodKey" :rides="$rides" />
                        @endforeach
                    </div>

                @elseif ($isEmpty)
                    @php
                        $radiusLabel = match($radius) {
                            'regio'  => 'In de regio',
                            'belgie' => 'Heel België',
                            default  => 'Dichtbij',
                        };
                    @endphp
                    <p class="kal-empty">
                        Geen ritten in de categorie "{{ $radiusLabel }}" van {{ $location['name'] }}.
                        Kies een ruimere regio om meer te zien.
                    </p>

                @else
                    <div class="kal-days">
                        @foreach ($byPeriod as $periodKey => $rows)
                            <x-ride-day :period-key="$periodKey" :rows="$rows" />
                        @endforeach
                    </div>
                @endif

                {{-- Past-rides link at bottom of agenda --}}
                <div class="kal-pastbar">
                    @if ($when === 'aankomend')
                        <button type="button" wire:click="showPast" class="link-muted">Bekijk voorbije ritten →</button>
                    @else
                        <button type="button" wire:click="showUpcoming" class="link-muted">← Terug naar aankomende ritten</button>
                    @endif
                </div>

            </div>{{-- /.kal-agenda --}}

            {{-- Sticky sidebar (desktop only; hidden on mobile via CSS) --}}
            @if ($when !== 'voorbije')
                <aside class="kal-sidebar">
                    <div class="kal-sidebar__panel kal-sidebar__panel--newsletter">
                        <h3 class="kal-sidebar__heading">Mis geen rit</h3>
                        <p class="kal-sidebar__body">Één seintje per maand met ritten bij jou in de buurt. Geen spam, altijd uitschrijfbaar.</p>
                        <button type="button" class="kal-sidebar__btn">Schrijf je in</button>
                    </div>
                </aside>
            @endif

        </div>{{-- /.kal-body --}}

    </x-page-hero>
</div>
