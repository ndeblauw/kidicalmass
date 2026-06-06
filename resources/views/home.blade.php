<x-layouts::site title="Kidical Mass Belgium">
    {{-- ① HERO — video-led emotional pitch. Joy is the argument ("is het de moeite waard?"). --}}
    <section class="home-hero">
        <div class="home-hero__video" aria-hidden="true">
            <iframe
                src="https://www.youtube.com/embed/VXiIgU9vI-4?autoplay=1&mute=1&loop=1&playlist=VXiIgU9vI-4&controls=0&showinfo=0&modestbranding=1&playsinline=1&rel=0"
                title="" tabindex="-1" frameborder="0"
                allow="autoplay; encrypted-media; picture-in-picture"
            ></iframe>
        </div>

        <div class="home-hero__inner">
            <h1 class="home-hero__title">Het leukste uur op de fiets, door autovrije straten.</h1>
            <p class="home-hero__lead">
                Een vrolijke gezinsfietstocht door autovrije straten, bij jou in de buurt.
                Samen laten we zien dat de straat ook van kinderen is.
            </p>
            <div class="home-hero__actions">
                <x-cta-button :href="route('activities.index')" class="link-plain">Vind een rit in de buurt</x-cta-button>
                <a href="{{ route('getting-started') }}" class="home-hero__secondary link-plain">Nieuw hier? Zo werkt het →</a>
            </div>
        </div>
    </section>

    <div class="space-y-20">
        {{-- ② DE VOLGENDE RIT BIJ JOU — one location-aware ride (proof + utility). --}}
        <section class="home-nextride space-y-6 scroll-mt-24" id="volgende-rit">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-kidical-ink">De volgende rit bij jou</h2>
                <a href="{{ route('activities.index') }}" class="shrink-0 font-bold text-kidical-blue hover:underline">Bekijk alle ritten →</a>
            </div>

            @if (! $hasUpcoming)
                <p class="text-kidical-ink/70">
                    Het fietsseizoen loopt van maart tot november.
                    <a href="{{ route('getting-started') }}" class="font-bold text-kidical-blue hover:underline">Ontdek hoe een rit werkt →</a>
                </p>
            @elseif (! $hasLocation)
                <livewire:location-picker />
            @else
                @if ($nextRideIsFar)
                    <p class="text-kidical-ink/70">Geen rit vlakbij op dit moment. De eerstvolgende iets verderaf:</p>
                @endif

                <div class="event-list">
                    <x-event-card :activity="$nextRide" />
                </div>

                @if ($nextRideDistanceKm !== null)
                    <p class="home-nextride__distance text-sm font-semibold text-kidical-ink/60">
                        {{ str_replace('.', ',', (string) $nextRideDistanceKm) }} km van jou
                    </p>
                @endif

                <livewire:location-picker />
            @endif
        </section>

        {{-- ③ DISPATCHER — three equal routes. Home is a crossroads, not a content dump. --}}
        <section class="home-routes grid gap-5 sm:grid-cols-3">
            <a href="{{ route('getting-started') }}" class="home-route link-plain">
                <span class="home-route__title">Nieuw hier?</span>
                <span class="home-route__desc">Zo werkt een Kidical Mass rit.</span>
            </a>
            <a href="{{ route('volunteer') }}" class="home-route link-plain">
                <span class="home-route__title">Help mee</span>
                <span class="home-route__desc">Word vrijwilliger bij een rit.</span>
            </a>
            <a href="{{ route('groups.index') }}" class="home-route link-plain">
                <span class="home-route__title">Vind je lokale groep</span>
                <span class="home-route__desc">Ontdek de groep bij jou in de buurt.</span>
            </a>
        </section>

        {{-- ④ Quiet support beat (reuses the tested home callout). --}}
        <x-support-callout variant="home" />
    </div>

    <x-slot:closing>
        <x-closing-cta heading="Klaar voor je eerste rit?"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>
</x-layouts::site>
