<div>
    <x-page-hero
        eyebrow="Kalender"
        title="Spring op de fiets, wij rijden samen."
        illustration="img/illustrations/cargo-bike-family.svg">

        {{-- Filter row: shared bar + agenda-only radius tabs. Hidden on past-rides view. --}}
        @if ($when !== 'voorbije')
            <x-filter-bar>
                @if ($location)
                    <div class="filter-bar__radius">
                        <span class="filter-bar__sep" aria-hidden="true">·</span>
                        <span class="filter-bar__radius-label">Hoe ver</span>
                        <div class="filter-bar__tabs">
                            <button
                                type="button"
                                wire:click="setRadius('dichtbij')"
                                class="filter-bar__tab{{ $radius === 'dichtbij' ? ' filter-bar__tab--active' : '' }}"
                            >Dichtbij</button>
                            <button
                                type="button"
                                wire:click="setRadius('regio')"
                                class="filter-bar__tab{{ $radius === 'regio' ? ' filter-bar__tab--active' : '' }}"
                            >In de regio</button>
                            <button
                                type="button"
                                wire:click="setRadius('belgie')"
                                class="filter-bar__tab{{ $radius === 'belgie' ? ' filter-bar__tab--active' : '' }}"
                            >Heel België</button>
                        </div>
                    </div>
                @endif
            </x-filter-bar>
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
                        <x-cta-button wire:click="showPast" x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })" variant="secondary">Bekijk voorbije ritten</x-cta-button>
                    @else
                        <x-cta-button wire:click="showUpcoming" x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })" variant="secondary" icon="back">Terug naar aankomende ritten</x-cta-button>
                    @endif
                </div>

            </div>{{-- /.kal-agenda --}}

            {{-- Sticky sidebar (desktop only; hidden on mobile via CSS) --}}
            @if ($when !== 'voorbije')
                <aside class="kal-sidebar">
                    <x-newsletter-optin />
                </aside>
            @endif

        </div>{{-- /.kal-body --}}

    </x-page-hero>
</div>
