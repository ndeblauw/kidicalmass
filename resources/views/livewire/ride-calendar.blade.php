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
                        <span class="filter-bar__radius-label">Toon ritten</span>
                        <div class="filter-bar__tabs">
                            <button
                                type="button"
                                wire:click="setRadius('dichtbij')"
                                aria-pressed="{{ $radius === 'dichtbij' ? 'true' : 'false' }}"
                                class="filter-bar__tab{{ $radius === 'dichtbij' ? ' filter-bar__tab--active' : '' }}"
                            >Dichtbij</button>
                            <button
                                type="button"
                                wire:click="setRadius('regio')"
                                aria-pressed="{{ $radius === 'regio' ? 'true' : 'false' }}"
                                class="filter-bar__tab{{ $radius === 'regio' ? ' filter-bar__tab--active' : '' }}"
                            >In de regio</button>
                            <button
                                type="button"
                                wire:click="setRadius('belgie')"
                                aria-pressed="{{ $radius === 'belgie' ? 'true' : 'false' }}"
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

                {{-- Screen-reader announcement: filtering re-renders the list silently
                     otherwise. The text changes with every radius/when switch, so live
                     regions pick it up. --}}
                <p class="sr-only" role="status">
                    @if (! $hasActivities || $isEmpty)
                        Geen ritten gevonden.
                    @else
                        {{ $rideCount }} {{ $rideCount === 1 ? 'rit' : 'ritten' }} gevonden.
                    @endif
                </p>

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

            {{-- Right-column lockup: opt-in card with the decorative sign tucked
                 beneath it, overlapping the card's bottom edge. The lockup is
                 bottom-aligned in its grid cell and dips into the yellow footer
                 band below (see .kal-sidebar in calendar.css). --}}
            @if ($when !== 'voorbije')
                <aside class="kal-sidebar">
                    <x-newsletter-optin />
                    <div class="kal-illu" aria-hidden="true">
                        <img src="{{ asset('img/illustrations/heart-30-sign.svg') }}" alt="" class="kal-illu__img">
                    </div>
                </aside>
            @endif

        </div>{{-- /.kal-body --}}

    </x-page-hero>
</div>
