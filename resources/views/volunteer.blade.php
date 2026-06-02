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

    {{-- HERO — poster layout, mirrors the ride/show hero --}}
    <section class="activity-hero ho-hero">

        <img src="{{ asset('img/logo-icon.png') }}" alt="" aria-hidden="true" class="activity-hero__daisy">

        <div class="container mx-auto px-4 activity-hero__inner">

            <div class="activity-hero__copy">
                <span class="ho-hero__badge">
                    <flux:icon.hand-raised variant="solid" aria-hidden="true" />
                    Doe mee
                </span>
                <h1>Meehelpen</h1>
                <p class="ho-hero__lead">Word deel van de ploeg die elke rit tot een feest maakt. Je hoeft geen fietsexpert te zijn, gewoon goesting om mee te doen.</p>
            </div>

            <div class="activity-hero__visual">
                <div class="activity-hero__photo">
                    <img src="{{ asset('img/volunteers/volunteers-pink-vests-with-flag.jpg') }}" alt="Vrijwilligers van Kidical Mass in roze hesjes met vlaggen" class="activity-hero__img">
                </div>
            </div>

        </div>

    </section>

    {{-- PITCH (contained) --}}
    <p class="ho-intro">
        Meehelpen bij Kidical Mass is opkomen voor je eigen buurt, samen met ouders en buren die
        meer kinderen op de fiets willen. Een paar uur per maand, een hoop nieuwe gezichten, en het
        goede gevoel dat je er echt toe doet. Je krijgt er veel meer voor terug dan je erin steekt.
    </p>

    {{-- HOE JE KAN HELPEN — reuses the promises band (yellow) --}}
    <section class="activity-promises ho-roles">
        <div class="activity-promises__layout">

            <div class="activity-promises__illustration">
                <h2>Hoe je kan helpen</h2>
                <img src="{{ asset('img/illustrations/kid-waving.png') }}" alt="" aria-hidden="true" loading="lazy">
            </div>

            <ul class="activity-promises__col" role="list">
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.shield-check variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Roze hesje</strong>
                    <p>Hou je van de actie? Als roze hesje fiets je mee naast de groep, houd je de kinderen samen en zorg je dat iedereen veilig en vrolijk aankomt.</p>
                </li>
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.calendar-days variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Mede-organisator</strong>
                    <p>Elke rit begint met iemand die hem plant. Jij kiest de route, het tijdstip en het vertrekpunt, en stemt af met het lokale team. Dankbaar werk.</p>
                </li>
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.megaphone variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Communicator</strong>
                    <p>Jij zorgt dat de buurt komt opdagen. Sociale media, flyers, schoolgroepen, mond-tot-mond. Elke nieuwe familie aan de start is een beetje jouw verdienste.</p>
                </li>
            </ul>

            <ul class="activity-promises__col" role="list">
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.camera variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>Fotograaf</strong>
                    <p>Een foto van veertig kinderen op de fiets zegt meer dan duizend woorden. Jij vangt de mooiste momenten en deelt ze met het team.</p>
                </li>
                <li class="activity-promises__item">
                    <div class="activity-promises__icon-wrap">
                        <flux:icon.musical-note variant="solid" class="activity-promises__icon" aria-hidden="true" />
                    </div>
                    <strong>DJ</strong>
                    <p>Muziek maakt het feest. Jij zet de toon voor de rit, houdt de energie hoog onderweg en stuurt iedereen met een glimlach naar huis.</p>
                </li>
            </ul>

        </div>
    </section>

    {{-- WAT MEEDOEN INHOUDT — sky band, the honest deal --}}
    <section class="ho-deal">
        <div class="container mx-auto px-4">
            <h2 class="ho-deal__title">Wat meedoen inhoudt</h2>

            <div class="ho-deal__cols">
                <div class="ho-deal__col">
                    <h3>Wat je krijgt</h3>
                    <ul role="list" class="ho-deal__list ho-deal__list--get">
                        <li>Kidical Mass-materiaal en steun vanaf dag één</li>
                        <li>Opleiding rond veiligheid en routeplanning, als je dat wil</li>
                        <li>Vier gezellige vrijwilligersmomenten per jaar, met lekker eten</li>
                        <li>Een warme bende ouders en fietsers die echte vrienden worden</li>
                    </ul>
                </div>
                <div class="ho-deal__col">
                    <h3>Wat we vragen</h3>
                    <ul role="list" class="ho-deal__list ho-deal__list--ask">
                        <li>Kom met goesting en een vrolijke, respectvolle houding</li>
                        <li>Onderschrijf onze afspraken rond vriendelijkheid en veiligheid</li>
                        <li>Maak je deel uit van een lokaal team? Stuur één afgevaardigde naar het jaarlijkse meetup-moment</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- JOY — real volunteers (the live site's happy signal) --}}
    <section class="ho-joy">
        <h2 class="ho-joy__title">Dit is de bende</h2>
        <p class="ho-joy__lead">Echte mensen, echte buurten, elke maand opnieuw. Zo ziet meedoen eruit.</p>

        <ul role="list" class="ho-joy__grid">
            <li class="ho-joy__photo">
                <img src="{{ asset('img/volunteers/volunteers-orange-vests-selfie.jpg') }}" alt="Lachende vrijwilligers maken een selfie" loading="lazy">
            </li>
            <li class="ho-joy__photo">
                <img src="{{ asset('img/volunteers/volunteers-pink-vests-cinquantenaire.jpg') }}" alt="Vrijwilligers in roze hesjes aan het Jubelpark" loading="lazy">
            </li>
            <li class="ho-joy__photo">
                <img src="{{ asset('img/volunteers/volunteer-selfie-stop-sign.jpg') }}" alt="Vrijwilliger met een stopbord onderweg" loading="lazy">
            </li>
            <li class="ho-joy__photo">
                <img src="{{ asset('img/volunteers/volunteers-group-pink-vests-park.avif') }}" alt="Groep vrijwilligers samen in het park" loading="lazy">
            </li>
        </ul>
    </section>

    {{-- VIND JE LOKALE GROEP — light-blue band, the climax. Tap your group → its form. --}}
    <section class="ho-find">
        <div class="container mx-auto px-4">
            <h2 class="ho-find__title">Vind je lokale groep</h2>
            <p class="ho-find__lead">
                Welke rol je ook kiest, je begint op dezelfde plek: bij de mensen in je eigen buurt.
                Kies je groep, dan kom je rechtstreeks bij hun team terecht. Niet via een centrale
                mailbox.
            </p>

            @if ($groups->isNotEmpty())
                <ul role="list" class="ho-groups">
                    @foreach ($groups as $group)
                        <li>
                            <a class="ho-group link-plain" href="{{ route('groups.show', ['group' => $group, 'intent' => 'volunteer']) }}#aanmelden">
                                <span class="ho-group__name">{{ $group->name }}</span>
                                @if ($group->zip)
                                    <span class="ho-group__zip">{{ $group->zip }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>Binnenkort vind je hier alle lokale groepen.</p>
            @endif
        </div>
    </section>

    {{-- NOG GEEN LOKALE GROEP? quiet coda --}}
    <section class="ho-start">
        <h2 class="ho-start__title">Nog geen lokale groep in je buurt?</h2>
        <p>
            Een lokale groep starten vraagt een kernteam van twee of drie mensen, een vertrekpunt en
            een route-idee. Wij zorgen voor het merk, de opleiding en de nationale zichtbaarheid. Jij
            brengt de energie en de lokale kennis. Zin om eraan te beginnen? Laat van je horen.
        </p>
        <p>
            <a href="mailto:bike@kidicalmass.be">Mail het coördinatieteam →</a>
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

</x-layouts::site>
