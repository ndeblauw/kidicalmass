<div>
    <x-page-hero
        :eyebrow="__('calendar.hero.eyebrow')"
        :title="__('calendar.hero.title')"
        illustration="img/illustrations/cargo-bike-family.svg">

        {{-- Filter row: the shared bar hosts the location picker. Hidden on past-rides view. --}}
        @if ($when !== 'voorbije')
            <x-filter-bar />
        @endif

        {{-- Two-column body: agenda left, sticky sidebar right. --}}
        <div class="kal-body">
            <div class="kal-agenda">

                {{-- Screen-reader announcement: switching views re-renders the list silently
                     otherwise. The text changes with every when switch or location change,
                     so live regions pick it up. --}}
                <p class="sr-only" role="status">
                    @if (! $hasActivities)
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
                            {!! __('calendar.empty_none', ['link' => '<a href="'.localized_route('newsletter.show').'">'.__('calendar.newsletter_link').'</a>']) !!}
                        @endif
                    </p>

                @elseif ($when === 'voorbije')
                    <div class="kal-days">
                        @foreach ($byPeriod as $periodKey => $rides)
                            <x-ride-month :period-key="$periodKey" :rides="$rides" />
                        @endforeach
                    </div>

                @elseif ($sections)
                    {{-- Nearest first, nothing hidden: distance bands in order, each by date.
                         Empty leading bands collapse into this one quiet line. --}}
                    @if ($emptyLead)
                        <p class="kal-lead" data-proximity-empty-lead>
                            {{ __('calendar.proximity.none_within', ['km' => \Illuminate\Support\Number::format($emptyLead['km'], maxPrecision: 1, locale: app()->getLocale()), 'place' => $emptyLead['place']]) }}
                        </p>
                    @endif
                    <div class="flex flex-col gap-20">
                        @foreach ($sections as $section)
                            <x-ride-proximity-section :band="$section['band']" :radius-km="$section['radius_km']" :by-day="$section['byDay']" />
                        @endforeach
                    </div>

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
