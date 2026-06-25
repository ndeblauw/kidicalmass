{{--
    Help out / "Meehelpen" (P-13, J2)
    Surface pass 2026-06-02 (re-skin to the ride/show kit, per DESIGN.md):
    - HERO reuses .activity-hero* — solid blue full-bleed, daisy, circular PHOTO of real
      volunteers, sky "Doe mee" badge, -3° white headline.
    - ROLES reuse .activity-promises* — yellow band, white tilted cards, red Flux-icon chips
      (emoji are gone; chips are canonical now).
    - A real-photo JOY band (the live site's signature happy vibe), the group picker as the
      climax on a light-blue band, and a quiet "start a group" coda.
    Structure only; appearance lives in app.css (.ho-* deltas on the reused kit). Orientation
    page: motivates and ROUTES (form on the chapter page, ?intent=volunteer#aanmelden).
    Plan: docs/wiki/design/30-skeleton/help-out.md
--}}
<x-layouts::site title="Meehelpen">

    <x-page-hero eyebrow="Meehelpen" title="Jouw handen maken de stoet." illustration="img/illustrations/volunteer-with-wrench.svg">

    {{-- PITCH — the cyclist rides in from the left, breaking out past the viewport
         edge (rear wheel runs off-frame), and sits beside the opening pitch. --}}
    <div class="ho-intro">
        <div class="ho-intro__inner">
            <img class="ho-intro__mascot" src="{{ asset('img/illustrations/cyclist-peace-sign.svg') }}" alt="" aria-hidden="true" loading="lazy">
            <div class="ho-intro__text">
                <x-intro-text>
                    <p>Meehelpen bij Kidical Mass is opkomen voor je eigen buurt, samen met ouders en buren die
                    meer kinderen op de fiets willen. Een paar uur per maand, een hoop nieuwe gezichten, en het
                    goede gevoel dat je er echt toe doet. Je krijgt er veel meer voor terug dan je erin steekt.</p>
                </x-intro-text>
            </div>
        </div>
    </div>

    {{-- HOE JE KAN HELPEN — carousel (zelfde aanpak als de teamband op de groep-pagina).
         De illustratie + titel blijven als vaste voorgrond links; de kaarten scrollen
         eronder door en vervagen links in het geel (spiegelt de bleed rechts buiten beeld). --}}
    @php
        $helpRoles = [
            ['icon' => 'shield-check', 'color' => 'red', 'name' => 'Roze hesje', 'text' => 'Hou je van de actie? Als roze hesje fiets je mee naast de groep, houd je de kinderen samen en zorg je dat iedereen veilig en vrolijk aankomt.'],
            ['icon' => 'calendar-days', 'color' => 'blue', 'name' => 'Mede-organisator', 'text' => 'Elke rit begint met iemand die hem plant. Jij kiest de route, het tijdstip en het vertrekpunt, en stemt af met het lokale team. Dankbaar werk.'],
            ['icon' => 'megaphone', 'color' => 'green', 'name' => 'Communicator', 'text' => 'Jij zorgt dat de buurt komt opdagen. Sociale media, flyers, schoolgroepen, mond-tot-mond. Elke nieuwe familie aan de start is een beetje jouw verdienste.'],
            ['icon' => 'camera', 'color' => 'orange', 'name' => 'Fotograaf', 'text' => 'Een foto van veertig kinderen op de fiets zegt meer dan duizend woorden. Jij vangt de mooiste momenten en deelt ze met het team.'],
            ['icon' => 'musical-note', 'color' => 'violet', 'name' => 'DJ', 'text' => 'Muziek maakt het feest. Jij zet de toon voor de rit, houdt de energie hoog onderweg en stuurt iedereen met een glimlach naar huis.'],
        ];
    @endphp
    <section class="ho-roles" aria-labelledby="ho-roles-title"
        x-data="{
            start: true,
            end: false,
            page(dir) { const t = $refs.track; const card = t.querySelector('.ho-roles__card'); if (!card) return; const step = card.offsetWidth + parseFloat(getComputedStyle(t).columnGap || 0); t.scrollBy({ left: dir * step, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }); },
            update() {
                const t = $refs.track, fg = $refs.fg;
                if (fg) {
                    const mobile = window.matchMedia('(max-width: 47.99rem)').matches;
                    const edge = fg.getBoundingClientRect().right;
                    t.querySelectorAll('.ho-roles__card').forEach(c => {
                        if (mobile) { c.style.opacity = ''; return; }
                        const r = c.getBoundingClientRect();
                        // share of the card still clear of the foreground (1 = fully clear, 0 = fully under)
                        const clear = (r.right - edge) / r.width;
                        // dissolve fully before the card reaches the biker: opaque until 90% clear, gone by 40%
                        c.style.opacity = Math.max(0, Math.min(1, (clear - 0.4) / 0.5));
                    });
                }
                const max = t.scrollWidth - t.clientWidth;
                const card = t.querySelector('.ho-roles__card');
                const step = card ? card.offsetWidth + parseFloat(getComputedStyle(t).columnGap || 0) : 0;
                this.start = t.scrollLeft <= 1;
                // within half a card of the end, tolerant of the few-px gap mandatory
                // snap leaves between the last snap point and the raw max scroll
                this.end = step > 0 && max - t.scrollLeft <= step / 2;
            }
        }"
        x-init="$nextTick(() => update())"
        x-on:resize.window="update()">

        {{-- foreground anchor: the title holds the left fade zone. Cards scroll behind
             it and fade out (opacity) as they pass under. --}}
        <div class="ho-roles__fg" x-ref="fg">
            <h2 id="ho-roles-title" class="ho-roles__title">Hoe je kan helpen</h2>
        </div>

        <ul class="ho-roles__track" x-ref="track" role="list" aria-label="Manieren om te helpen" x-on:scroll.passive="update()">
            @foreach ($helpRoles as $role)
                <li class="ho-roles__card">
                    <x-feature-card :icon="$role['icon']" :color="$role['color']" :title="$role['name']">
                        {{ $role['text'] }}
                    </x-feature-card>
                </li>
            @endforeach
        </ul>

        <div class="ho-roles__nav">
            <button type="button" class="ho-roles__btn" aria-label="Vorige rollen" x-on:click="page(-1)" :disabled="start">
                <flux:icon.chevron-left aria-hidden="true" />
            </button>
            <button type="button" class="ho-roles__btn" aria-label="Volgende rollen" x-on:click="page(1)" :disabled="end">
                <flux:icon.chevron-right aria-hidden="true" />
            </button>
        </div>
    </section>

    {{-- WAT MEEDOEN INHOUDT — scroll-sequence (gedeelde component). De collage rechts
         crossfade't naar het blok dat je leest; een zwaaiende fietser ankert linksonder.
         Mobiel: beide collages gestapeld, geen swap. --}}
    <section class="ho-deal">
        <div class="container mx-auto px-4">
            <x-scroll-sequence media-side="right" active-margin="-25% 0px -66% 0px">
                <x-slot:media>
                    <div class="ho-deal__collage ho-deal__collage--a is-active" data-seq-media="0">
                        <figure class="ho-deal__photo ho-deal__photo--lead">
                            <x-photo src="img/photography/ride-trio-pink-vest-lei-portrait.webp" alt="Drie vrijwilligers lachen samen tijdens een rit, één met een roze hesje en een bloemenkrans" />
                        </figure>
                        <figure class="ho-deal__photo ho-deal__photo--trail">
                            <x-photo src="img/photography/team-blue-sweatshirts-celebration.webp" alt="Organisatoren in blauwe truien juichen samen onder de lichtjes tijdens een vrijwilligersfeest" />
                        </figure>
                        <img class="ho-deal__doodle" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" aria-hidden="true">
                    </div>
                    <div class="ho-deal__collage ho-deal__collage--b" data-seq-media="1">
                        <figure class="ho-deal__photo ho-deal__photo--lead">
                            <x-photo src="img/photography/ride-crowd-intersection.webp" alt="Twee vrijwilligers in roze hesje houden samen een kruispunt in het oog terwijl de stoet passeert" />
                        </figure>
                        <figure class="ho-deal__photo ho-deal__photo--trail">
                            <x-photo src="img/photography/volunteers-season-launch-meetup.webp" alt="Vrijwilligers kletsen gezellig na tijdens de jaarlijkse meetup van de Roze Hesjes" />
                        </figure>
                        <img class="ho-deal__doodle" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" aria-hidden="true">
                    </div>
                </x-slot:media>

                <div class="scroll-sequence__block" data-seq-block="0">
                    <x-titled-list-block title="Wat je krijgt" variant="get" level="h2">
                        <li>Kidical Mass-materiaal en steun vanaf dag één</li>
                        <li>Opleiding rond veiligheid en routeplanning, als je dat wil</li>
                        <li>Vier gezellige vrijwilligersmomenten per jaar, met lekker eten</li>
                        <li>Een warme bende ouders en fietsers die echte vrienden worden</li>
                    </x-titled-list-block>
                </div>

                <div class="scroll-sequence__block" data-seq-block="1">
                    <x-titled-list-block title="Wat we vragen" variant="ask" level="h2">
                        <li>Kom met goesting en een vrolijke, respectvolle houding</li>
                        <li>Onderschrijf onze afspraken rond vriendelijkheid en veiligheid</li>
                        <li>Maak je deel uit van een lokaal team? Stuur één afgevaardigde naar het jaarlijkse meetup-moment</li>
                    </x-titled-list-block>
                </div>
            </x-scroll-sequence>
        </div>
    </section>

    {{-- VIND JE LOKALE GROEP — light-blue band, the climax. Tap your group → its form. --}}
    <section class="ho-find">
        <div class="container mx-auto px-4">
            <div class="ho-find__layout">
                <div class="ho-find__art" aria-hidden="true">
                    <img src="{{ asset('img/illustrations/zone-30-sign.svg') }}" alt="" loading="lazy">
                </div>

                <div class="ho-find__body">
                    <h2 class="ho-find__title">Vind je lokale groep</h2>
                    <p class="ho-find__lead">
                        Welke rol je ook kiest, je begint op dezelfde plek: bij de mensen in je eigen buurt.
                        Kies je groep, dan kom je rechtstreeks bij hun team terecht. Niet via een centrale
                        mailbox.
                    </p>

                    <div class="ho-find__picker">
                        <livewire:location-picker :compact="true" />
                    </div>

                    @if ($location && $nearestGroups->isNotEmpty())
                        <h3 class="ho-find__nearest-title">Het dichtst bij {{ $location['name'] }}</h3>
                        <p class="ho-find__nearest">
                            @foreach ($nearestGroups as $row)
                                <a href="{{ route('groups.show', ['group' => $row['item'], 'intent' => 'volunteer']) }}#aanmelden">{{ $row['item']->name }}</a>@if (! $loop->last), @endif
                            @endforeach
                        </p>
                    @endif

                    <p class="ho-find__all">
                        <a href="{{ route('groups.index') }}">Bekijk alle lokale groepen</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Scroll reveal for the role cards (mirrors the ride page) --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const cards = document.querySelectorAll('.ho-roles .activity-promises__item');
        cards.forEach((card, i) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.4s cubic-bezier(0.25, 1, 0.5, 1), transform 0.4s cubic-bezier(0.25, 1, 0.5, 1)';
            card.style.transitionDelay = `${i * 80}ms`;
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        cards.forEach(card => observer.observe(card));
    });
    </script>
    @endpush

    </x-page-hero>

    <x-slot:closing>
        {{-- Closing coda on the yellow band: the "start a group" route replaces the
             generic membership CTA on this page. Content left, sign-holder right. --}}
        <section class="ho-coda relative z-30 bg-kidical-yellow">
            <div class="container mx-auto px-4">
                <div class="ho-coda__layout">
                    <div class="ho-coda__body">
                        <h2 class="ho-coda__title">Nog geen lokale groep in je buurt?</h2>
                        <p>
                            Misschien start jij er een. Twee of drie buren en wat goesting volstaan,
                            de rest doen we samen.
                        </p>
                        <p class="ho-coda__cta">
                            <x-cta-button :href="route('groups.start')" variant="blue">Zo start je een groep</x-cta-button>
                        </p>
                    </div>

                    <div class="ho-coda__art" aria-hidden="true">
                        <img src="{{ asset('img/illustrations/heart-sign-holder.svg') }}" alt="" loading="lazy">
                    </div>
                </div>
            </div>
        </section>
    </x-slot:closing>

</x-layouts::site>
