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

    {{-- PITCH (contained) — vertically centred in its white zone so it clears the
         mascotte that pokes up from the band below --}}
    <div class="ho-intro">
        <x-intro-text>
            <p>Meehelpen bij Kidical Mass is opkomen voor je eigen buurt, samen met ouders en buren die
            meer kinderen op de fiets willen. Een paar uur per maand, een hoop nieuwe gezichten, en het
            goede gevoel dat je er echt toe doet. Je krijgt er veel meer voor terug dan je erin steekt.</p>
        </x-intro-text>
    </div>

    {{-- HOE JE KAN HELPEN — carousel (zelfde aanpak als de teamband op de groep-pagina).
         De illustratie + titel blijven als vaste voorgrond links; de kaarten scrollen
         eronder door en vervagen links in het geel (spiegelt de bleed rechts buiten beeld). --}}
    @php
        $helpRoles = [
            ['icon' => 'shield-check', 'name' => 'Roze hesje', 'text' => 'Hou je van de actie? Als roze hesje fiets je mee naast de groep, houd je de kinderen samen en zorg je dat iedereen veilig en vrolijk aankomt.'],
            ['icon' => 'calendar-days', 'name' => 'Mede-organisator', 'text' => 'Elke rit begint met iemand die hem plant. Jij kiest de route, het tijdstip en het vertrekpunt, en stemt af met het lokale team. Dankbaar werk.'],
            ['icon' => 'megaphone', 'name' => 'Communicator', 'text' => 'Jij zorgt dat de buurt komt opdagen. Sociale media, flyers, schoolgroepen, mond-tot-mond. Elke nieuwe familie aan de start is een beetje jouw verdienste.'],
            ['icon' => 'camera', 'name' => 'Fotograaf', 'text' => 'Een foto van veertig kinderen op de fiets zegt meer dan duizend woorden. Jij vangt de mooiste momenten en deelt ze met het team.'],
            ['icon' => 'musical-note', 'name' => 'DJ', 'text' => 'Muziek maakt het feest. Jij zet de toon voor de rit, houdt de energie hoog onderweg en stuurt iedereen met een glimlach naar huis.'],
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
                this.start = t.scrollLeft <= 1;
                this.end = t.scrollLeft >= max - 1;
            }
        }"
        x-init="$nextTick(() => update())"
        x-on:resize.window="update()">

        {{-- foreground anchor: mascotte pokes over the top seam, title sits under it.
             Cards scroll behind it and fade out (opacity) as they pass under. --}}
        <div class="ho-roles__fg" x-ref="fg">
            <img class="ho-roles__mascot" src="{{ asset('img/illustrations/cyclist-peace-sign.svg') }}" alt="" aria-hidden="true" loading="lazy">
            <h2 id="ho-roles-title" class="ho-roles__title">Hoe je kan helpen</h2>
        </div>

        <div class="ho-roles__nav">
            <button type="button" class="ho-roles__btn" aria-label="Vorige rollen" x-on:click="page(-1)" :disabled="start">‹</button>
            <button type="button" class="ho-roles__btn" aria-label="Volgende rollen" x-on:click="page(1)" :disabled="end">›</button>
        </div>

        <ul class="ho-roles__track" x-ref="track" role="list" aria-label="Manieren om te helpen" x-on:scroll.passive="update()">
            @foreach ($helpRoles as $role)
                <li class="ho-roles__card">
                    <span class="ho-roles__chip">
                        <flux:icon :name="$role['icon']" variant="solid" class="ho-roles__icon" aria-hidden="true" />
                    </span>
                    <strong>{{ $role['name'] }}</strong>
                    <p>{{ $role['text'] }}</p>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- WAT MEEDOEN INHOUDT — scrollytelling (Frederik 2026-06-03). Each block holds ~the
         viewport with ample air; the large photo on the right crossfades to match the block
         you're reading (IntersectionObserver swaps the active image at the viewport centre).
         Bolder type, white (no band). Mobile: stacks, both photos shown, no swap. --}}
    <section class="ho-deal">
        <div class="container mx-auto px-4">
            <div class="ho-deal__layout">

                <div class="ho-deal__text">
                    <div class="ho-deal__block" data-ho-photo="0">
                        <x-titled-list-block title="Wat je krijgt">
                            <li>Kidical Mass-materiaal en steun vanaf dag één</li>
                            <li>Opleiding rond veiligheid en routeplanning, als je dat wil</li>
                            <li>Vier gezellige vrijwilligersmomenten per jaar, met lekker eten</li>
                            <li>Een warme bende ouders en fietsers die echte vrienden worden</li>
                        </x-titled-list-block>
                    </div>

                    <div class="ho-deal__block" data-ho-photo="1">
                        <x-titled-list-block title="Wat we vragen">
                            <li>Kom met goesting en een vrolijke, respectvolle houding</li>
                            <li>Onderschrijf onze afspraken rond vriendelijkheid en veiligheid</li>
                            <li>Maak je deel uit van een lokaal team? Stuur één afgevaardigde naar het jaarlijkse meetup-moment</li>
                        </x-titled-list-block>
                    </div>
                </div>

                <div class="ho-deal__media">
                    <div class="ho-deal__media-sticky">
                        <figure class="ho-deal__frame">
                            <img class="ho-deal__img is-active" data-ho-img="0" src="{{ asset('img/photography/volunteers/volunteers-pink-vests-with-flag.jpg') }}" alt="Een warme bende vrijwilligers in hesjes zwaait blij met de Kidical Mass-vlag" loading="lazy">
                            <img class="ho-deal__img" data-ho-img="1" src="{{ asset('img/photography/volunteers/volunteer-selfie-stop-sign.jpg') }}" alt="Vrijwilliger in roze hesje houdt met een stopbord een kruispunt vrij" loading="lazy">
                        </figure>
                    </div>
                </div>

            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const blocks = document.querySelectorAll('.ho-deal__block');
            const imgs = document.querySelectorAll('.ho-deal__img');
            if (blocks.length < 2 || imgs.length < 2) return;

            const setActive = (idx) => imgs.forEach((img, i) => img.classList.toggle('is-active', i === idx));

            // A thin band at the viewport centre: whichever block crosses it drives the photo.
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        setActive(parseInt(entry.target.dataset.hoPhoto, 10) || 0);
                    }
                });
            }, { rootMargin: '-45% 0px -45% 0px', threshold: 0 });

            blocks.forEach((block) => io.observe(block));
        });
    </script>
    @endpush

    {{-- VIND JE LOKALE GROEP — light-blue band, the climax. Tap your group → its form. --}}
    <section class="ho-find">
        <div class="container mx-auto px-4">
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
                <ul role="list" class="ho-groups">
                    @foreach ($nearestGroups as $row)
                        <li>
                            <a class="ho-group link-plain" href="{{ route('groups.show', ['group' => $row['item'], 'intent' => 'volunteer']) }}#aanmelden">
                                <span class="ho-group__name">{{ $row['item']->name }}</span>
                                @if ($row['item']->zip)
                                    <span class="ho-group__zip">{{ $row['item']->zip }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    {{-- NOG GEEN LOKALE GROEP? quiet coda — funnels to the start-a-group page
         (replaces the old mailto:bike@ black hole, D-12). --}}
    <section class="ho-start">
        <h2 class="ho-start__title">Nog geen lokale groep in je buurt?</h2>
        <p>
            Misschien start jij er een. Een kernteam van twee of drie mensen en wat goesting volstaan
            om te beginnen, de rest doen we samen. We tonen je precies wat het inhoudt, en je kan eerst
            praten met iemand die het al deed.
        </p>
        <p>
            <a href="{{ route('groups.start') }}">Zo start je een groep →</a>
        </p>
        <p>
            <small>
                Bekijk welke steden al een lokale groep hebben: <a href="{{ route('groups.index') }}">alle groepen →</a>
            </small>
        </p>
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
        <x-closing-cta heading="Geef de straat terug aan kinderen"
            :href="route('membership')" label="Word lid" icon="heart" />
    </x-slot:closing>

</x-layouts::site>
