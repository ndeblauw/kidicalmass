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

            <h1 class="home-hero__title">Het leukste uur op de fiets</h1>
        </section>

        <section class="home-intro">
            <div class="home-intro__inner container mx-auto px-4 text-center">
                <x-intro-text size="lead" class="home-intro__lead">
                    <p>Een vrolijke fietsparade bij jou in de buurt. <br>
                    Samen tonen we dat de straat ook van kinderen is.</p>
                </x-intro-text>

                <div class="home-intro__actions mb-8">
                    <x-cta-button href="#volgende-rit" variant="ghost" class="home-intro__scroll">{{ $hasLocation ? 'De volgende rit bij jou' : 'Volgende ritten' }}</x-cta-button>
                </div>
            </div>
        </section>
    </div>

    {{-- Holds the fixed backdrop's place in normal flow (md+). --}}
    <div class="home-backdrop__spacer" aria-hidden="true"></div>

    {{-- White rounded-top panel; scrolls up over the fixed backdrop (shared .page-panel). --}}
    <div class="page-panel">
        <div class="page-panel__inner container mx-auto px-4 space-y-16 md:space-y-20">
        {{-- ② DE VOLGENDE RIT BIJ JOU — location-aware rides (proof + utility). --}}
        <section class="home-nextride space-y-6 scroll-mt-24" id="volgende-rit">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-kidical-ink">{{ $hasLocation ? 'De volgende rit bij jou' : 'Volgende ritten' }}</h2>
                @if ($hasUpcoming && $hasLocation)
                    <a href="{{ route('activities.index') }}" class="shrink-0 font-bold text-kidical-blue hover:underline">Bekijk alle ritten →</a>
                @endif
            </div>

            @if (! $hasUpcoming)
                <p class="text-kidical-ink/70">
                    Het fietsseizoen loopt van maart tot november.
                    <a href="{{ route('getting-started') }}" class="font-bold text-kidical-blue hover:underline">Ontdek hoe een rit werkt →</a>
                </p>

            @elseif (! $hasLocation)
                @foreach ($upcomingRides as $periodKey => $rows)
                    <x-ride-day :period-key="$periodKey" :rows="$rows" />
                @endforeach

                <div class="home-nextride__pair grid gap-8 sm:grid-cols-2 sm:gap-0">
                    <div class="sm:pr-10">
                        <p class="home-nextride__eyebrow">Ritten bij jou</p>
                        <livewire:location-picker :compact="true" />
                        <p class="home-nextride__sub">Vul je gemeente in en we zetten de ritten dichtbij bovenaan.</p>
                    </div>
                    <div class="home-nextride__divide flex flex-col items-start justify-center sm:pl-10">
                        <p class="home-nextride__eyebrow">Of bekijk alles</p>
                        <a href="{{ route('activities.index') }}" class="font-bold text-kidical-blue hover:underline">Alle ritten in de agenda →</a>
                        <p class="home-nextride__sub">De volledige kalender, van maart tot november.</p>
                    </div>
                </div>

            @else
                <livewire:location-picker :compact="true" />

                @if ($nextRideIsFar)
                    <p class="text-kidical-ink/70">Geen rit vlakbij op dit moment. De eerstvolgende iets verderaf:</p>
                @endif

                <x-ride-day :period-key="$nextRide->begin_date->toDateString()" :rows="[['item' => $nextRide]]" />
            @endif
        </section>

        {{-- ③ DISPATCHER — three equal routes. Home is a crossroads, not a content dump.
             Each card previews the character you'll meet on the destination page. --}}
        <section class="home-routes relative grid gap-5 sm:grid-cols-3">
            {{-- Signposts at the crossroads (decorative, desktop only). --}}
            <img src="{{ asset('img/illustrations/zone-30-sign.svg') }}" alt="" aria-hidden="true" loading="lazy"
                class="pointer-events-none absolute right-2 bottom-full mb-2 w-16 rotate-6 hidden sm:block">
            <img src="{{ asset('img/illustrations/heart-30-sign.svg') }}" alt="" aria-hidden="true" loading="lazy"
                class="pointer-events-none absolute left-2 top-full mt-2 w-14 -rotate-6 hidden sm:block">

            <a href="{{ route('getting-started') }}" class="home-route link-plain">
                <span class="home-route__art"><img src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" aria-hidden="true" loading="lazy"></span>
                <span class="home-route__title">Nieuw hier?</span>
                <span class="home-route__desc">Zo werkt een Kidical Mass rit.</span>
            </a>
            <a href="{{ route('volunteer') }}" class="home-route link-plain">
                <span class="home-route__art"><img src="{{ asset('img/illustrations/volunteer-with-wrench.svg') }}" alt="" aria-hidden="true" loading="lazy"></span>
                <span class="home-route__title">Help mee</span>
                <span class="home-route__desc">Word vrijwilliger bij een rit.</span>
            </a>
            <a href="{{ route('groups.index') }}" class="home-route link-plain">
                <span class="home-route__art"><img src="{{ asset('img/illustrations/longtail-with-kid.svg') }}" alt="" aria-hidden="true" loading="lazy"></span>
                <span class="home-route__title">Vind je lokale groep</span>
                <span class="home-route__desc">Ontdek de groep bij jou in de buurt.</span>
            </a>
        </section>

        {{-- ④ Quiet support beat (reuses the tested home callout). --}}
        <x-support-callout variant="home" />
        </div>
    </div>

    <x-slot:closing>
        <x-closing-cta heading="Wil je vaker meerijden?"
            :href="route('groups.index')" label="Vind je lokale groep" />
    </x-slot:closing>
</x-layouts::site>
