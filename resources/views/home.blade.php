<x-layouts::site>
    {{-- ① HERO BACKDROP — fixed and one viewport tall from md up; the white panel
         scrolls up over it. The video + title fill the top, a brand-blue band
         carries the lead and the two entry links. --}}
    <div class="home-backdrop">
        {{-- Video covers the whole backdrop (hero + lead band), so it reads as one
             continuous frame; the blue band fades in over it with its copy. --}}
        <div class="home-hero__video" aria-hidden="true">
            {{-- Looped via the IFrame Player API (seek to 0 on end), NOT the
                 &loop=&playlist= trick — a playlist makes YouTube show the
                 prev/next centre arrows, which controls=0 doesn't suppress. --}}
            <iframe
                id="home-hero-player"
                src="https://www.youtube-nocookie.com/embed/VXiIgU9vI-4?autoplay=1&mute=1&controls=0&showinfo=0&modestbranding=1&playsinline=1&rel=0&enablejsapi=1&disablekb=1"
                title="" tabindex="-1" frameborder="0"
                allow="autoplay; encrypted-media; picture-in-picture"
            ></iframe>
        </div>

        <x-layout-proposal>
        <section class="home-hero">
            {{-- One span per word so the words can pop in reading order (the
                 --word-index drives the stagger). French high punctuation is
                 glued to its word with a no-break space, so "!" never wraps alone. --}}
            @php
                $heroTitleWords = explode(' ', preg_replace('/ +([!?:;»])/u', "\u{00A0}\$1", trim(__('home.hero.title'))));
            @endphp
            <h1 class="home-hero__title"><span class="home-hero__title-ride"><span class="home-hero__title-line">@foreach ($heroTitleWords as $word)<span class="home-hero__word" style="--word-index: {{ $loop->index }}">{{ $word }}</span>{{ $loop->last ? '' : ' ' }}@endforeach</span></span></h1>
        </section>

        <section class="home-intro">
            <div class="home-intro__inner container mx-auto px-6 md:px-4 text-center">
                <x-intro-text size="lead" class="home-intro__lead">
                    <p>{{ __('home.hero.lead') }}</p>
                </x-intro-text>
                <div class="home-intro__cta">
                    <x-cta-button href="#volgende-rit">{{ __('home.hero.cta') }}</x-cta-button>
                </div>
            </div>
        </section>
        </x-layout-proposal>
    </div>

    {{-- Holds the fixed backdrop's place in normal flow (md+). --}}
    <div class="home-backdrop__spacer" aria-hidden="true"></div>

    {{-- Hero fit check (md+): the pinned backdrop is one viewport (+2rem) tall, so
         under text zoom or on a short screen the title and blue band can outgrow it
         and get cropped. When they don't fit, flag the backdrop to stack in normal
         flow instead (see home.css). Runs inline, right after the markup, so the
         first paint already has the right mode; the observer re-checks when fonts
         load or the text size changes. The measurement ignores the mode itself,
         so switching can't loop. --}}
    <script>
        (function () {
            var backdrop = document.querySelector('.home-backdrop');
            var hero = backdrop && backdrop.querySelector('.home-hero');
            var title = backdrop && backdrop.querySelector('.home-hero__title');
            var intro = backdrop && backdrop.querySelector('.home-intro');
            if (!hero || !title || !intro) {
                return;
            }

            function checkHeroFit() {
                var heroStyle = getComputedStyle(hero);
                var rem = parseFloat(getComputedStyle(document.documentElement).fontSize);
                var needed = parseFloat(heroStyle.paddingTop) + title.offsetHeight
                    + parseFloat(heroStyle.paddingBottom) + intro.offsetHeight;
                var pinned = window.innerHeight + 2 * rem;
                backdrop.setAttribute('data-hero-fit', needed > pinned ? 'flow' : 'pinned');
            }

            checkHeroFit();
            window.addEventListener('resize', checkHeroFit);
            if ('ResizeObserver' in window) {
                var observer = new ResizeObserver(checkHeroFit);
                observer.observe(title);
                observer.observe(intro);
            }
        })();
    </script>

    {{-- White rounded-top panel; scrolls up over the fixed backdrop (shared .page-panel). --}}
    <div class="page-panel page-panel--home">
        <div class="page-panel__inner container mx-auto px-4 space-y-16 md:space-y-20">
        {{-- ② DE VOLGENDE RIT BIJ JOU — location-aware rides (proof + utility).
             A right-facing rider (flag up, matching the "fietsparade" lead) anchors the
             left fifth from md up and is pulled up over the panel's rounded top edge, so
             a sliver shows in the blue hero view before you scroll. See .home-nextride. --}}
        <section class="home-nextride scroll-mt-28 md:scroll-mt-48 pt-12" id="volgende-rit">
            <div class="home-nextride__art" aria-hidden="true">
                <img src="{{ asset('img/illustrations/rider-with-flag.svg') }}" alt="" loading="lazy">
            </div>

            <div class="home-nextride__body space-y-6">
                <h2 class="text-kidical-ink">{{ $hasLocation ? __('home.next_rides.heading_nearby') : __('home.next_rides.heading') }}</h2>
                <p class="text-kidical-ink/70">{{ __('home.hero.mission') }}</p>

                @if (! $hasUpcoming)
                    <p class="text-kidical-ink/70">
                        {{ __('home.next_rides.off_season') }}
                        <a href="{{ localized_route('getting-started') }}" class="font-bold text-kidical-blue hover:underline">{{ __('home.next_rides.off_season_link') }}</a>
                    </p>

                @elseif (! $hasLocation)
                    @foreach ($upcomingRides as $day)
                        <x-ride-day :period-key="$day['date']" :rows="$day['rows']" />
                    @endforeach

                    <div class="max-w-lg">
                        <livewire:location-picker />
                    </div>

                @else
                    <livewire:location-picker />

                    @if ($nextRideIsFar)
                        <p class="text-kidical-ink/70">{{ __('home.next_rides.far_away') }}</p>
                    @endif

                    @foreach ($upcomingRides as $day)
                        <x-ride-day :period-key="$day['date']" :rows="$day['rows']" />
                    @endforeach

                    <div class="flex justify-start">
                        <x-cta-button :href="localized_route('activities.index')" variant="secondary">{{ __('home.next_rides.all') }}</x-cta-button>
                    </div>
                @endif
            </div>
        </section>

        {{-- VIER BEATS: scrollytelling. Each section reads on its own; one sticky
             bike rides in to match the section you're reading (see <x-scroll-sequence>).
             No is-active on the first item, so it rolls in like the rest on first view.
             Mobile: each section shows its own illustration inline (home.css), no ride. --}}
        <x-scroll-sequence media-side="right" class="home-routes" active-margin="-28% 0px -50% 0px">
            @php
                // One collage per beat (PAT-20). Each is a [data-seq-media] item the
                // scroll-sequence crossfades; the riding bike below rides per beat.
                $routeCollages = [
                    [
                        ['src' => 'img/photography/ride-child-thumbsup-red-helmet.webp', 'alt' => __('home.photos.first_time.photo_1'), 'x' => '38%', 'y' => '34%', 'w' => '56%', 'r' => '-5deg', 'pos' => 'center 35%'],
                        ['src' => 'img/photography/ride-brussels-two-boys-at-start.webp', 'alt' => __('home.photos.first_time.photo_2'), 'x' => '70%', 'y' => '64%', 'w' => '50%', 'r' => '6deg', 'pos' => 'center 40%'],
                    ],
                    [
                        ['src' => 'img/photography/ride-cinquantenaire-crowd.webp', 'alt' => __('home.photos.local_group.photo_1'), 'x' => '64%', 'y' => '33%', 'w' => '54%', 'r' => '5deg', 'pos' => 'center 35%'],
                        ['src' => 'img/photography/cargo-bike-mother-two-kids-flag.webp', 'alt' => __('home.photos.local_group.photo_2'), 'x' => '36%', 'y' => '62%', 'w' => '50%', 'r' => '-6deg'],
                    ],
                    [
                        ['src' => 'img/photography/volunteers-pink-vest-group-cobbles.webp', 'alt' => __('home.photos.help_out.photo_1'), 'x' => '40%', 'y' => '32%', 'w' => '56%', 'r' => '-6deg', 'pos' => 'center 40%'],
                        ['src' => 'img/photography/volunteer-fistbump-kids-park.webp', 'alt' => __('home.photos.help_out.photo_2'), 'x' => '71%', 'y' => '63%', 'w' => '48%', 'r' => '6deg'],
                    ],
                ];
            @endphp
            <x-slot:media>
                {{-- A centred square stage holds the crossfading collages and the
                     illustration that rides off the stage's bottom edge per beat. --}}
                <div class="home-routes__stage">
                    @foreach ($routeCollages as $i => $collagePhotos)
                        <x-photo-collage
                            class="home-routes__media{{ $i === 0 ? ' is-active' : '' }}"
                            :photos="$collagePhotos"
                            :reveal="false"
                            data-seq-media="{{ $i }}" />
                    @endforeach

                    <img class="home-routes__illu" data-seq-media="0" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" loading="lazy">
                    <img class="home-routes__illu" data-seq-media="1" src="{{ asset('img/illustrations/longtail-with-kid.svg') }}" alt="" loading="lazy">
                    <img class="home-routes__illu" data-seq-media="2" src="{{ asset('img/illustrations/volunteer-with-wrench.svg') }}" alt="" loading="lazy">

                    {{-- Beat 4 has no bike: the map takes the stage while the helper
                         above rides off (it turns is-past), so nothing rides in. --}}
                    <div class="home-routes__map" data-seq-media="3">
                        <x-belgium-map :map="$belgiumMap" />
                    </div>
                </div>
            </x-slot:media>

            <div class="scroll-sequence__block" data-seq-block="0">
                <img class="home-routes__block-illu" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" aria-hidden="true" loading="lazy">
                <h2 class="text-kidical-ink">{{ __('home.routes.first_time.heading') }}</h2>
                <p class="text-kidical-ink/70">{{ __('home.routes.first_time.body') }}</p>
                <p><x-cta-button :href="localized_route('getting-started')" variant="secondary" disc="green">{{ __('home.routes.first_time.cta') }}</x-cta-button></p>
            </div>

            <div class="scroll-sequence__block" data-seq-block="1">
                <img class="home-routes__block-illu" src="{{ asset('img/illustrations/longtail-with-kid.svg') }}" alt="" aria-hidden="true" loading="lazy">
                <h2 class="text-kidical-ink">{{ __('home.routes.local_group.heading') }}</h2>
                <p class="text-kidical-ink/70">{{ __('home.routes.local_group.body') }}</p>
                <p><x-cta-button :href="localized_route('groups.index')" variant="secondary" disc="orange">{{ __('home.routes.local_group.cta') }}</x-cta-button></p>
            </div>

            <div class="scroll-sequence__block" data-seq-block="2">
                <img class="home-routes__block-illu" src="{{ asset('img/illustrations/volunteer-with-wrench.svg') }}" alt="" aria-hidden="true" loading="lazy">
                <h2 class="text-kidical-ink">{{ __('home.routes.help_out.heading') }}</h2>
                <p class="text-kidical-ink/70">{{ __('home.routes.help_out.body') }}</p>
                <p><x-cta-button :href="localized_route('volunteer')" variant="secondary">{{ __('home.routes.help_out.cta') }}</x-cta-button></p>
            </div>

            {{-- Eén beweging: the national picture. No illustration; the static map
                 (<x-belgium-map>) takes the sticky stage on lg+ and sits inline
                 under the stats below lg. Figures: App\Support\MovementStats. --}}
            <div class="scroll-sequence__block home-movement" data-seq-block="3" data-home-movement>
                <h2 class="text-kidical-ink">{{ __('home.routes.movement.heading') }}</h2>
                <p class="text-kidical-ink/70">{{ __('home.routes.movement.body') }}</p>
                @if ($movementStats)
                    <dl class="home-movement__stats">
                        @foreach ($movementStats as $stat)
                            <div class="home-movement__stat home-movement__stat--{{ $stat['color'] }}" data-stat="{{ $stat['key'] }}">
                                <dt>{{ $stat['label'] }}</dt>
                                {{-- Width in digits (a separator counts as half), so home.css can shrink a long figure to fit its column. --}}
                                <dd style="--stat-digits: {{ mb_strlen($stat['value']) - mb_strlen(preg_replace('/\d/u', '', $stat['value'])) / 2 }}">{{ $stat['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
                <x-belgium-map :map="$belgiumMap" class="home-movement__map" />
                <p><x-cta-button :href="localized_route('about')" variant="secondary" disc="blue">{{ __('home.routes.movement.cta') }}</x-cta-button></p>
            </div>
        </x-scroll-sequence>

        </div>
    </div>

    <x-slot:closing>
        <x-newsletter-cta />
    </x-slot:closing>

    @push('scripts')
        {{-- Hero video: loop without the &playlist= trick (which adds the
             prev/next centre arrows). Seek back to 0 when the clip ends, and
             keep it muted + playing so YouTube never parks on its paused-state
             centre controls. --}}
        <script src="https://www.youtube.com/iframe_api"></script>
        <script>
            function onYouTubeIframeAPIReady() {
                var player = new YT.Player('home-hero-player', {
                    events: {
                        onReady: function (event) {
                            event.target.mute();
                            event.target.playVideo();
                        },
                        onStateChange: function (event) {
                            // Pointer events are off, so the only way the player
                            // leaves PLAYING is YouTube parking on its centre
                            // play/pause overlay (a buffer, an autoplay hiccup, the
                            // loop seek). Resume immediately so that button never
                            // lingers; reset to the start when the clip ends.
                            if (event.data === YT.PlayerState.ENDED) {
                                event.target.seekTo(0);
                                event.target.playVideo();
                            } else if (event.data === YT.PlayerState.PAUSED) {
                                event.target.playVideo();
                            }
                        },
                    },
                });

                // Loop a beat *before* the clip actually ends, so the player never
                // reaches YouTube's end screen - which is when the centre button is
                // drawn and which controls=0 can't suppress. seekTo while playing
                // keeps it playing, so there's no paused frame at the loop point.
                setInterval(function () {
                    if (typeof player.getDuration !== 'function') {
                        return;
                    }
                    var duration = player.getDuration();
                    if (duration > 0 && player.getCurrentTime() >= duration - 0.4) {
                        player.seekTo(0);
                    }
                }, 250);
            }
        </script>
    @endpush
</x-layouts::site>
