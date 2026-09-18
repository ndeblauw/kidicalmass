<div>
    <x-page-hero
        :eyebrow="__('calendar.hero.eyebrow')"
        :title="__('calendar.hero.title')"
        illustration="img/illustrations/cargo-bike-family.svg">

        {{-- Filter row: shared bar + agenda-only radius tabs. Hidden on past-rides view. --}}
        @if ($when !== 'voorbije')
            <x-filter-bar>
                @if ($location)
                    <div class="filter-bar__radius">
                        <span class="filter-bar__radius-label">{{ __('calendar.filter_label') }}</span>
                        <div class="filter-bar__tabs">
                            <button
                                type="button"
                                wire:click="setRadius('dichtbij')"
                                aria-pressed="{{ $radius === 'dichtbij' ? 'true' : 'false' }}"
                                class="filter-bar__tab{{ $radius === 'dichtbij' ? ' filter-bar__tab--active' : '' }}"
                            >{{ __('calendar.radius.nearby') }}</button>
                            <button
                                type="button"
                                wire:click="setRadius('regio')"
                                aria-pressed="{{ $radius === 'regio' ? 'true' : 'false' }}"
                                class="filter-bar__tab{{ $radius === 'regio' ? ' filter-bar__tab--active' : '' }}"
                            >{{ __('calendar.radius.region') }}</button>
                            <button
                                type="button"
                                wire:click="setRadius('belgie')"
                                aria-pressed="{{ $radius === 'belgie' ? 'true' : 'false' }}"
                                class="filter-bar__tab{{ $radius === 'belgie' ? ' filter-bar__tab--active' : '' }}"
                            >{{ __('calendar.radius.belgium') }}</button>
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
                        {{ __('calendar.none') }}
                    @else
                        {{ trans_choice('calendar.found', $rideCount) }}
                    @endif
                </p>

                @if (! $hasActivities)
                    <p class="kal-empty">
                        @if ($when === 'voorbije')
                            {{ __('calendar.empty_past') }}
                        @else
                            {{ __('calendar.empty_none') }}
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
                            'regio'  => __('calendar.radius.region'),
                            'belgie' => __('calendar.radius.belgium'),
                            default  => __('calendar.radius.nearby'),
                        };
                    @endphp
                    <p class="kal-empty">
                        {{ __('calendar.empty_radius', ['radius' => $radiusLabel, 'place' => $location['name']]) }}<br>
                        {{ __('calendar.empty_radius_hint') }}
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
                        <x-cta-button wire:click="showPast" x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })" variant="secondary">{{ __('calendar.show_past') }}</x-cta-button>
                    @else
                        <x-cta-button wire:click="showUpcoming" x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })" variant="secondary" icon="back">{{ __('calendar.show_upcoming') }}</x-cta-button>
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
