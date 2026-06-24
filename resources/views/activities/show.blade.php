<x-layouts::site title="{{ $activity->title_nl }}">

@php($routeCoords = $activity->route_coordinates)
@php($hasMap = count($routeCoords) > 0)
@php($mainImage = $activity->getFirstMedia('main'))

    {{-- HERO — blue band, tilted photo card dipping into the white below (new look) --}}
    <header class="activity-head">
        <div class="container mx-auto px-4 activity-head__inner">

            <div class="activity-head__copy">
                <p class="activity-head__eyebrow">
                    <time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ \Illuminate\Support\Str::ucfirst($activity->dateFull) }} &middot; {{ $activity->timeLabel }}</time>
                </p>

                <h1 class="page-hero__title">{{ $activity->title_nl }}</h1>

                @if($activity->content_nl)
                    <x-intro-text class="activity-head__lead">{!! nl2br(e($activity->content_nl)) !!}</x-intro-text>
                @endif

                @if($activity->groups->isNotEmpty())
                    <div class="activity-head__org">
                        <img src="{{ asset('img/logos/logo-icon.png') }}" alt="" aria-hidden="true" class="activity-head__org-mark">
                        <div class="activity-head__org-label">
                            @foreach($activity->groups as $group)
                                <span class="activity-head__org-name">{{ $group->name }}</span>
                                @if($group->zip)
                                    <span class="activity-head__org-zip">{{ $group->zip }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <x-share-links
                    :url="route('activities.show', $activity)"
                    :title="$activity->title_nl"
                    :date="\Illuminate\Support\Str::ucfirst($activity->dateFull)"
                    class="activity-head__share" />
            </div>

            <figure class="activity-head__media">
                @if($mainImage)
                    <img src="{{ $mainImage->getUrl() }}" alt="{{ $activity->title_nl }}" class="activity-head__photo">
                @else
                    <img src="{{ asset('img/photography/ride-cinquantenaire-crowd.jpg') }}" alt="" aria-hidden="true" class="activity-head__photo">
                @endif
            </figure>

        </div>
    </header>

    {{-- CONTAINED BODY — calm white column, soft cards, colour only as accent --}}
    <div class="activity-stack">

        {{-- PRAKTISCH — facts + route, paired with a "stay in the loop" card --}}
        <section class="activity-praktisch">
            <article class="activity-facts">
                <h2 class="activity-facts__title">Praktisch</h2>
                <dl class="activity-facts__meta">
                    <div class="activity-facts__meta-item">
                        <x-icon-chip color="light-blue" size="sm" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        </x-icon-chip>
                        <div>
                            <dt>Startuur</dt>
                            <dd><time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ \Illuminate\Support\Str::ucfirst($activity->dateFull) }}, {{ $activity->timeLabel }}</time></dd>
                        </div>
                    </div>

                    <div class="activity-facts__meta-item">
                        <x-icon-chip color="light-blue" size="sm" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-7 7-12a7 7 0 1 0-14 0c0 5 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/></svg>
                        </x-icon-chip>
                        <div>
                            <dt>Vertrekpunt</dt>
                            <dd>{!! nl2br(e($activity->location)) !!}</dd>
                        </div>
                    </div>

                    @if($activity->distance)
                        <div class="activity-facts__meta-item">
                            <x-icon-chip color="light-blue" size="sm" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7L4 11l4 4"/><path d="M4 11h16"/><path d="M16 17l4-4-4-4"/></svg>
                            </x-icon-chip>
                            <div>
                                <dt>Afstand</dt>
                                <dd>{{ $activity->distance }}</dd>
                            </div>
                        </div>
                    @endif

                    @if($activity->duration_label)
                        <div class="activity-facts__meta-item">
                            <x-icon-chip color="light-blue" size="sm" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                            </x-icon-chip>
                            <div>
                                <dt>Duur</dt>
                                <dd>{{ $activity->duration_label }}</dd>
                            </div>
                        </div>
                    @endif

                    <div class="activity-facts__meta-item">
                        <x-icon-chip color="light-blue" size="sm" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2 2 2 0 0 0 0 4 2 2 0 0 1-2 2H6a2 2 0 0 1-2-2 2 2 0 0 0 0-4z"/></svg>
                        </x-icon-chip>
                        <div>
                            <dt>Deelname</dt>
                            <dd>Gratis &middot; geen inschrijving nodig</dd>
                        </div>
                    </div>
                </dl>

                {{-- Route — the real GPX track when present, else the stylised brand fallback
                     (same dual logic as the chapter page's <x-next-ride>). --}}
                <div class="activity-facts__map">
                    @if($hasMap)
                        <x-route-map :coordinates="$routeCoords" :interactive="false" class="activity-facts__route" aria-hidden="true" />
                    @else
                        <div class="activity-facts__route-faux" aria-hidden="true">
                            <svg viewBox="0 0 440 320" preserveAspectRatio="xMidYMid slice" class="activity-facts__route-svg">
                                <path class="activity-facts__route-line" d="M50 270 C 120 260, 150 210, 200 200 S 300 180, 330 120 400 75 405 45" fill="none"/>
                                <circle class="activity-facts__route-dot" cx="200" cy="200" r="5"/>
                                <circle class="activity-facts__route-dot" cx="330" cy="120" r="5"/>
                                <circle class="activity-facts__route-start" cx="50" cy="270" r="10"/>
                            </svg>
                        </div>
                    @endif
                    <div class="activity-map-info-strip">
                        <div class="activity-map-info-strip__stats">
                            <span class="activity-map-stat">
                                <flux:icon.arrows-right-left class="activity-map-stat__icon" aria-hidden="true" />
                                {{ $activity->distance ?? '—' }}
                            </span>
                            <span class="activity-map-stat">
                                <flux:icon.clock class="activity-map-stat__icon" aria-hidden="true" />
                                {{ $activity->duration_label ?? '—' }}
                            </span>
                        </div>
                        @if($activity->komoot_url)
                            <a href="{{ $activity->komoot_url }}" target="_blank" rel="noopener noreferrer" class="activity-map-komoot-link">
                                Bekijk op Komoot
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </article>

            {{-- UPDATES — invite people to follow this chapter's rides --}}
            <x-newsletter-optin :group="$activity->groups->first()" class="activity-updates h-full flex flex-col justify-center" />
        </section>

        {{-- WAT KUN JE VERWACHTEN — the fixed promises, as soft cards --}}
        <section class="activity-promises">
            <p class="activity-eyebrow">Wat kun je verwachten?</p>
            <ul class="activity-promises__grid" role="list">
                <li class="activity-promises__item">
                    <div class="activity-promises__icon">
                        <flux:icon.clock variant="solid" aria-hidden="true" />
                    </div>
                    <strong>Op het tempo van het jongste kind</strong>
                    <p>We wachten op iedereen. Geen kind blijft achter.</p>
                </li>
                <li class="activity-promises__item">
                    <div class="activity-promises__icon">
                        <flux:icon.shield-check variant="solid" aria-hidden="true" />
                    </div>
                    <strong>Roze hesjes voor en achter</strong>
                    <p>Vrijwilligers begeleiden de groep en zorgen dat iedereen veilig aankomt.</p>
                </li>
                <li class="activity-promises__item">
                    <div class="activity-promises__icon">
                        <flux:icon.musical-note variant="solid" aria-hidden="true" />
                    </div>
                    <strong>Muziek onderweg</strong>
                    <p>Een versierde bakfiets met geluid maakt van elke rit een feest.</p>
                </li>
                <li class="activity-promises__item">
                    <div class="activity-promises__icon">
                        <flux:icon.flag variant="solid" aria-hidden="true" />
                    </div>
                    <strong>Korte verkeersbespreking aan de start</strong>
                    <p>Zodat iedereen weet wat te doen, ook als het je eerste keer is.</p>
                </li>
            </ul>
        </section>

        {{-- VAN EN VOOR DE BUURT — organisers + volunteer reveal --}}
        <section class="activity-team" x-data="{ open: false }">

            <template x-if="!open"><div>
                <p class="activity-eyebrow">Van en voor de buurt</p>

                @if($activity->groups->isNotEmpty())
                    <p class="activity-team__lead">
                        Georganiseerd door vrijwilligers van Kidical Mass
                        @foreach($activity->groups as $group)
                            <a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>@if(!$loop->last), @endif
                        @endforeach
                    </p>
                @endif

                <div class="activity-team__person">
                    <img src="https://i.pravatar.cc/128?img=47" alt="{{ Str::before($activity->author->name, ' ') }}" class="activity-team__avatar" loading="lazy">
                    <div>
                        <span class="activity-team__name">{{ Str::before($activity->author->name, ' ') }}</span>
                        <span class="activity-team__role">Organisator</span>
                    </div>
                </div>

                <div class="activity-volunteer">
                    <h3>Roze hesje worden?</h3>
                    <p>Als roze hesje begeleid je de groep en zorg je dat iedereen veilig aankomt. Je rijdt vooraan of achteraan, houdt kruispunten vrij en zorgt dat geen enkel kind achterblijft. <a href="{{ route('volunteer') }}">Lees hoe dat werkt.</a></p>
                    <button x-on:click="open = true" class="activity-volunteer__btn">
                        Ik wil meedoen als roze hesje
                    </button>
                </div>
            </div></template>

            <template x-if="open"><div class="activity-team__signup">
                <button x-on:click="open = false" class="activity-team__back">
                    <flux:icon.arrow-left class="activity-team__back-icon" aria-hidden="true" />
                    Terug
                </button>
                <h2 class="activity-facts__title">Meld je aan</h2>
                <p class="activity-team__lead">We nemen zo snel mogelijk contact met je op als roze hesje.</p>
                <livewire:volunteer-signup />
            </div></template>

        </section>

        {{-- STEUN — contextual support ask, quiet & contained --}}
        <x-support-callout variant="event" :contained="true" />

        {{-- DEEL — invite a gezin, quiet & contained --}}
        <x-share-band
            :url="route('activities.show', $activity)"
            :title="$activity->title_nl"
            :date="$activity->begin_date->translatedFormat('l j F')"
            :contained="true" />

    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const cards = document.querySelectorAll('.activity-promises__item');
        cards.forEach((card, i) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = `opacity 0.4s cubic-bezier(0.25, 1, 0.5, 1), transform 0.4s cubic-bezier(0.25, 1, 0.5, 1)`;
            card.style.transitionDelay = `${i * 90}ms`;
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

    <x-slot:closing>
        <x-closing-cta heading="Nog niet zeker hoe het werkt?"
            :href="route('getting-started')" label="Lees hoe je meerijdt" />
    </x-slot:closing>

</x-layouts::site>
