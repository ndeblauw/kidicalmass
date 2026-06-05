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

    <x-page-hero eyebrow="Meehelpen" title="Jouw handen maken de stoet." illustration="img/illustrations/kid-waving.png">

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

    {{-- WAT MEEDOEN INHOUDT — scrollytelling (Frederik 2026-06-03). Each block holds ~the
         viewport with ample air; the large photo on the right crossfades to match the block
         you're reading (IntersectionObserver swaps the active image at the viewport centre).
         Bolder type, white (no band). Mobile: stacks, both photos shown, no swap. --}}
    <section class="ho-deal">
        <div class="container mx-auto px-4">
            <div class="ho-deal__layout">

                <div class="ho-deal__text">
                    <div class="ho-deal__block" data-ho-photo="0">
                        <h3 class="ho-deal__subtitle">Wat je krijgt</h3>
                        <ul role="list" class="ho-deal__list ho-deal__list--get">
                            <li>Kidical Mass-materiaal en steun vanaf dag één</li>
                            <li>Opleiding rond veiligheid en routeplanning, als je dat wil</li>
                            <li>Vier gezellige vrijwilligersmomenten per jaar, met lekker eten</li>
                            <li>Een warme bende ouders en fietsers die echte vrienden worden</li>
                        </ul>
                    </div>

                    <div class="ho-deal__block" data-ho-photo="1">
                        <h3 class="ho-deal__subtitle">Wat we vragen</h3>
                        <ul role="list" class="ho-deal__list ho-deal__list--ask">
                            <li>Kom met goesting en een vrolijke, respectvolle houding</li>
                            <li>Onderschrijf onze afspraken rond vriendelijkheid en veiligheid</li>
                            <li>Maak je deel uit van een lokaal team? Stuur één afgevaardigde naar het jaarlijkse meetup-moment</li>
                        </ul>
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

    </x-page-hero>

</x-layouts::site>
