<x-layouts::site title="Kidical Mass Belgium">
    {{-- ① HERO BACKDROP — fixed and one viewport tall from md up; the white panel
         scrolls up over it. The video + title fill the top, a brand-blue band
         carries the lead and the two entry links. --}}
    <div class="home-backdrop">
        <section class="home-hero">
            <div class="home-hero__video" aria-hidden="true">
                <iframe
                    src="https://www.youtube.com/embed/VXiIgU9vI-4?autoplay=1&mute=1&loop=1&playlist=VXiIgU9vI-4&controls=0&showinfo=0&modestbranding=1&playsinline=1&rel=0"
                    title="" tabindex="-1" frameborder="0"
                    allow="autoplay; encrypted-media; picture-in-picture"
                ></iframe>
            </div>

            <h1 class="home-hero__title"><span class="home-hero__title-line"><span class="home-hero__word">Het</span> <span class="home-hero__word">leukste</span> <span class="home-hero__word">uur</span> <span class="home-hero__word">op</span> <span class="home-hero__word">de</span> <span class="home-hero__word">fiets</span></span></h1>
        </section>

        <section class="home-intro">
            <div class="home-intro__inner container mx-auto px-4 text-center">
                <x-intro-text size="lead" class="home-intro__lead">
                    <p>Een vrolijke fietsparade bij jou in de buurt. <br>
                    Samen tonen we dat de straat ook van kinderen is.</p>
                </x-intro-text>

                <div class="home-intro__actions mb-8">
                    <a href="#volgende-rit" class="home-intro__scroll" aria-label="Naar de volgende ritten">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    </div>

    {{-- Holds the fixed backdrop's place in normal flow (md+). --}}
    <div class="home-backdrop__spacer" aria-hidden="true"></div>

    {{-- White rounded-top panel; scrolls up over the fixed backdrop (shared .page-panel). --}}
    <div class="page-panel page-panel--home">
        <div class="page-panel__inner container mx-auto px-4 space-y-16 md:space-y-20">
        {{-- ② DE VOLGENDE RIT BIJ JOU — location-aware rides (proof + utility).
             A right-facing rider (flag up, matching the "fietsparade" lead) anchors the
             left fifth from md up and is pulled up over the panel's rounded top edge, so
             a sliver shows in the blue hero view before you scroll. See .home-nextride. --}}
        <section class="home-nextride scroll-mt-28 md:scroll-mt-48" id="volgende-rit">
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

                    <div class="flex justify-end">
                        <x-cta-button :href="route('activities.index')" variant="secondary">Alle ritten</x-cta-button>
                    </div>
                @endif
            </div>
        </section>

        {{-- DRIE ROUTES: scrollytelling. Each section reads on its own; one sticky
             bike rides in to match the section you're reading (see <x-scroll-sequence>).
             No is-active on the first item, so it rolls in like the rest on first view.
             Mobile: each section shows its own illustration inline (home.css), no ride. --}}
        <x-scroll-sequence media-side="right" class="home-routes">
            <x-slot:media>
                <img class="home-routes__illu" data-seq-media="0" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" loading="lazy">
                <img class="home-routes__illu" data-seq-media="1" src="{{ asset('img/illustrations/longtail-with-kid.svg') }}" alt="" loading="lazy">
                <img class="home-routes__illu" data-seq-media="2" src="{{ asset('img/illustrations/volunteer-with-wrench.svg') }}" alt="" loading="lazy">
            </x-slot:media>

            <div class="scroll-sequence__block" data-seq-block="0">
                <img class="home-routes__block-illu" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" aria-hidden="true" loading="lazy">
                <h2 class="text-kidical-ink">Nieuw hier?</h2>
                <p class="text-kidical-ink/70">Nog nooit meegefietst? Geen zorgen. Een Kidical Mass is een rustige, vrolijke fietsparade door je eigen buurt, op kindertempo, met de kruispunten veilig vrijgehouden. Je hoeft niets te kunnen en je hoeft je niet in te schrijven. Gewoon komen en meefietsen.</p>
                <p><x-cta-button :href="route('getting-started')" variant="secondary">Zo werkt een rit</x-cta-button></p>
            </div>

            <div class="scroll-sequence__block" data-seq-block="1">
                <img class="home-routes__block-illu" src="{{ asset('img/illustrations/longtail-with-kid.svg') }}" alt="" aria-hidden="true" loading="lazy">
                <h2 class="text-kidical-ink">Vind je lokale groep</h2>
                <p class="text-kidical-ink/70">Kidical Mass is geen organisatie ver weg, maar de mensen in jouw buurt. Overal in Vlaanderen en Brussel plannen lokale groepen hun eigen ritten. Vind de groep bij jou, en je weet meteen wanneer de volgende rit vertrekt en wie erachter zit.</p>
                <p><x-cta-button :href="route('groups.index')" variant="secondary">Vind je groep</x-cta-button></p>
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
        <x-closing-cta heading="Geef de straat terug aan kinderen"
            :href="route('membership')" label="Word lid" icon="heart" />
    </x-slot:closing>
</x-layouts::site>
