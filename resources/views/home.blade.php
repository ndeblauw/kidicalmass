<x-layouts::site title="Kidical Mass Belgium">
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
                src="https://www.youtube.com/embed/VXiIgU9vI-4?autoplay=1&mute=1&controls=0&showinfo=0&modestbranding=1&playsinline=1&rel=0&enablejsapi=1&disablekb=1"
                title="" tabindex="-1" frameborder="0"
                allow="autoplay; encrypted-media; picture-in-picture"
            ></iframe>
        </div>

        <section class="home-hero">
            <h1 class="home-hero__title"><span class="home-hero__title-ride"><span class="home-hero__title-line"><span class="home-hero__word">Het</span> <span class="home-hero__word">leukste</span> <span class="home-hero__word">uur</span><br><span class="home-hero__word">op</span> <span class="home-hero__word">de</span> <span class="home-hero__word">fiets</span></span></span></h1>
        </section>

        <section class="home-intro">
            <div class="home-intro__inner container mx-auto px-4 text-center">
                <x-intro-text size="lead" class="home-intro__lead">
                    <p>Een vrolijke fietsparade bij jou in de buurt. <br>
                    Samen tonen we dat de straat ook van kinderen is.</p>
                </x-intro-text>
            </div>
        </section>
    </div>

    {{-- Holds the fixed backdrop's place in normal flow (md+). --}}
    <div class="home-backdrop__spacer" aria-hidden="true"></div>

    {{-- White rounded-top panel; scrolls up over the fixed backdrop (shared .page-panel). --}}
    <div class="page-panel page-panel--home">
        {{-- Scroll cue that straddles the seam where the white panel meets the blue
             band. Lives on the panel (not the band) so it rides up with the panel as
             it scrolls over the fixed hero, staying on the seam. --}}
        <a href="#volgende-rit" class="home-seam-cue" aria-label="Naar de volgende ritten">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>

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
                <h2 class="text-kidical-ink">{{ $hasLocation ? 'De volgende ritten bij jou' : 'Volgende ritten' }}</h2>

                @if (! $hasUpcoming)
                    <p class="text-kidical-ink/70">
                        Het fietsseizoen loopt van maart tot november.
                        <a href="{{ route('getting-started') }}" class="font-bold text-kidical-blue hover:underline">Ontdek hoe een rit werkt →</a>
                    </p>

                @elseif (! $hasLocation)
                    @foreach ($upcomingRides as $periodKey => $rows)
                        <x-ride-day :period-key="$periodKey" :rows="$rows" />
                    @endforeach

                    <div class="max-w-lg">
                        <livewire:location-picker :compact="true" />
                    </div>

                @else
                    <livewire:location-picker :compact="true" />

                    @if ($nextRideIsFar)
                        <p class="text-kidical-ink/70">Geen rit vlakbij op dit moment. De eerstvolgende iets verderaf:</p>
                    @endif

                    @foreach ($upcomingRides as $periodKey => $rows)
                        <x-ride-day :period-key="$periodKey" :rows="$rows" />
                    @endforeach

                    <div class="flex justify-start">
                        <x-cta-button :href="route('activities.index')" variant="secondary">Alle ritten</x-cta-button>
                    </div>
                @endif
            </div>
        </section>

        {{-- DRIE ROUTES: scrollytelling. Each section reads on its own; one sticky
             bike rides in to match the section you're reading (see <x-scroll-sequence>).
             No is-active on the first item, so it rolls in like the rest on first view.
             Mobile: each section shows its own illustration inline (home.css), no ride. --}}
        <x-scroll-sequence media-side="right" class="home-routes" active-margin="-12% 0px -61% 0px">
            @php
                // One collage per beat (PAT-20). Each is a [data-seq-media] item the
                // scroll-sequence crossfades; the riding bike below rides per beat.
                $routeCollages = [
                    [
                        ['src' => 'img/photography/ride-child-thumbsup-red-helmet.webp', 'alt' => 'Stralende jongen met rode helm steekt zijn duim op tijdens een rit.', 'x' => '38%', 'y' => '34%', 'w' => '56%', 'r' => '-5deg', 'pos' => 'center 35%'],
                        ['src' => 'img/photography/ride-brussels-two-boys-at-start.webp', 'alt' => 'Twee jongens staan arm in arm met hun fietsen aan de start van een rit.', 'x' => '70%', 'y' => '64%', 'w' => '50%', 'r' => '6deg', 'pos' => 'center 40%'],
                    ],
                    [
                        ['src' => 'img/photography/ride-cinquantenaire-crowd.jpg', 'alt' => 'Een grote groep gezinnen fietst samen onder de triomfboog van het Jubelpark.', 'x' => '64%', 'y' => '33%', 'w' => '54%', 'r' => '5deg', 'pos' => 'center 35%'],
                        ['src' => 'img/photography/cargo-bike-mother-two-kids-flag.webp', 'alt' => 'Lachende vrouw fietst met twee kinderen in haar bakfiets en een Kidical Mass-vlag.', 'x' => '36%', 'y' => '62%', 'w' => '50%', 'r' => '-6deg'],
                    ],
                    [
                        ['src' => 'img/photography/volunteers-pink-vest-group-cobbles.webp', 'alt' => 'Een bende vrijwilligers in roze hesjes lacht en zwaait op een plein.', 'x' => '40%', 'y' => '32%', 'w' => '56%', 'r' => '-6deg', 'pos' => 'center 40%'],
                        ['src' => 'img/photography/volunteer-fistbump-kids-park.webp', 'alt' => 'Vrijwilliger geeft een boks aan een klein kind tussen fietsende kinderen in het park.', 'x' => '71%', 'y' => '63%', 'w' => '48%', 'r' => '6deg'],
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
                </div>
            </x-slot:media>

            <div class="scroll-sequence__block" data-seq-block="0">
                <img class="home-routes__block-illu" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" aria-hidden="true" loading="lazy">
                <h2 class="text-kidical-ink">Nieuw hier?</h2>
                <p class="text-kidical-ink/70">Nog nooit meegefietst? Geen zorgen. Een Kidical Mass is een rustige, vrolijke fietsparade door je eigen buurt, op kindertempo, met de kruispunten veilig vrijgehouden. Je hoeft niets te kunnen en je hoeft je niet in te schrijven. Gewoon komen en meefietsen.</p>
                <p><x-cta-button :href="route('getting-started')" variant="secondary" disc="green">Zo werkt een rit</x-cta-button></p>
            </div>

            <div class="scroll-sequence__block" data-seq-block="1">
                <img class="home-routes__block-illu" src="{{ asset('img/illustrations/longtail-with-kid.svg') }}" alt="" aria-hidden="true" loading="lazy">
                <h2 class="text-kidical-ink">Vind je lokale groep</h2>
                <p class="text-kidical-ink/70">Kidical Mass is geen organisatie ver weg, maar de mensen in jouw buurt. Overal in Vlaanderen en Brussel plannen lokale groepen hun eigen ritten. Vind de groep bij jou, en je weet meteen wanneer de volgende rit vertrekt en wie erachter zit.</p>
                <p><x-cta-button :href="route('groups.index')" variant="secondary" disc="orange">Vind je groep</x-cta-button></p>
            </div>

            <div class="scroll-sequence__block" data-seq-block="2">
                <img class="home-routes__block-illu" src="{{ asset('img/illustrations/volunteer-with-wrench.svg') }}" alt="" aria-hidden="true" loading="lazy">
                <h2 class="text-kidical-ink">Help mee</h2>
                <p class="text-kidical-ink/70">Een rit ontstaat niet vanzelf. Achter elke parade staan ouders en buren die de route uittekenen, de boel aankondigen en in een roze hesje meefietsen. Een paar uur per maand, en je krijgt er een warme bende vrienden voor terug.</p>
                <p><x-cta-button :href="route('volunteer')" variant="secondary">Word vrijwilliger</x-cta-button></p>
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
