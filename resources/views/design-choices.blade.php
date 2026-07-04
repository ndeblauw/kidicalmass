{{--
    Design-choices prototype — /design-choices (non-production only, unlinked).
    Internal decision page for the about-section "normalize" pass: each open
    decision (D1-D9) rendered as live variants with the real site components,
    a radio per decision and a copyable summary at the bottom. Additive-only;
    reproduces existing markup/classes, changes nothing else.
--}}
@php
    $decisions = [
        'd1' => [
            'nr' => 'D1', 'slug' => 'statistieken', 'title' => 'Wie toont de cijfers?',
            'context' => 'Dezelfde AboutStats-cijfers staan vandaag twee keer in de sectie: als band op de hub en als deck op Wat we doen.',
            'recommended' => 'B',
            'options' => [
                'A' => 'Hub houdt de band, missie laat het deck vallen',
                'B' => 'Missie houdt het deck, hub laat de band vallen',
                'C' => 'Beide behouden (bewuste herhaling)',
            ],
        ],
        'd2' => [
            'nr' => 'D2', 'slug' => 'kaart-tiers', 'title' => 'Wat betekent een tilt? (kaart-tiers)',
            'context' => 'Drie voorgestelde kaartniveaus: display (tilt, wit), note (lichtgeel, zachte tilt) en quiet (recht, hairline). Beleid: tilt is de stem van de beweging; recht is navigatie, derden, serieus register.',
            'recommended' => 'A',
            'options' => [
                'A' => 'Beleid overnemen zoals voorgesteld',
                'B' => 'Overal tilt (speels overal)',
                'C' => 'Minder tilt overal (rustiger)',
            ],
        ],
        'd3' => [
            'nr' => 'D3', 'slug' => 'sectiekoppen', 'title' => 'Sectiekoppen op banden',
            'context' => 'De bandtitel (about-band__title) topt kléiner dan de basis h2-schaal die elders op de site staat. Dat verschil is de bug waarover beslist wordt.',
            'recommended' => 'A',
            'options' => [
                'A' => 'Eén h2-schaal overal, about-band__title verdwijnt',
                'B' => 'Kleinere bandtitel behouden, maar als bewuste variant overal consequent',
            ],
        ],
        'd4' => [
            'nr' => 'D4', 'slug' => 'lees-meer-links', 'title' => "'Lees meer'-links",
            'context' => 'Er circuleren twee linkstijlen: 700 body-face blauw (about-section__link) en 800 heading-face (info-card__link, nu voor het manifest en de persmail).',
            'recommended' => 'A',
            'options' => [
                'A' => 'Alles 700 body-face',
                'B' => 'Alles 800 heading-face',
                'C' => '700 voor inline links, 800 alleen voor de e-mail/manifest-display-links',
            ],
        ],
        'd5' => [
            'nr' => 'D5', 'slug' => 'nieuws-layout', 'title' => 'Nieuws-index layout',
            'context' => 'De nieuwsindex is nu een uniform 3-koloms grid. Met weinig artikels kan een featured-first opzet meer hiërarchie geven.',
            'recommended' => 'B',
            'options' => [
                'A' => 'Huidig: uniform 3-koloms grid',
                'B' => 'Featured-first: nieuwste artikel groot en vol-breed, rest in rustiger grid eronder',
                'C' => 'Featured + compacte lijst: nieuwste groot, de rest als rustige rijen met mini-thumbnail',
            ],
        ],
        'd6' => [
            'nr' => 'D6', 'slug' => 'pers-afsluiter', 'title' => 'Pers: afsluiter',
            'context' => 'De perspagina bevat het contact al (perskaart met mailadres). De vraag: komt er daarna nog een gele afsluitband?',
            'recommended' => 'A',
            'options' => [
                'A' => 'Geen closing CTA, de pagina ís het contact en de band sluit strak af',
                'B' => 'Slanke gele afsluitband met mailto',
            ],
        ],
        'd7' => [
            'nr' => 'D7', 'slug' => 'partners-afsluiter', 'title' => 'Partners: één afsluiter',
            'context' => 'Vandaag eindigt Partners met een enquiry-band (formulier) én daarna nog een generieke gele CTA: twee afsluiters na elkaar.',
            'recommended' => 'A',
            'options' => [
                'A' => 'Enquiry-band is de afsluiter, generieke CTA weg',
                'B' => 'Beide houden',
            ],
        ],
        'd8' => [
            'nr' => 'D8', 'slug' => 'hub-leeskaarten', 'title' => 'Hub: 6 of 4 leeskaarten?',
            'context' => 'De intent-pillen bovenaan de hub linken al naar Pers en Partners; het leesgrid eronder herhaalt die twee als kaart.',
            'recommended' => 'A',
            'options' => [
                'A' => '4 kaarten: pillen doen, kaarten lezen, geen overlap',
                'B' => '6 kaarten houden',
            ],
        ],
        'd9' => [
            'nr' => 'D9', 'slug' => 'scroll-reveal', 'title' => 'Scroll-reveal beleid',
            'context' => 'Waar mogen kaarten in-faden bij het scrollen? Geen demo nodig, dit is een beleidskeuze.',
            'recommended' => 'A',
            'options' => [
                'A' => 'Alleen op kaart-grids: hub, missie, visie, partners, plus het nieuws-grid erbij; organisatie en pers statisch',
                'B' => 'Op alle about-pagina\'s',
                'C' => 'Reveals helemaal weg',
            ],
        ],
    ];

    $defaults = collect($decisions)->map(fn (array $d) => $d['recommended']);
    $summaryData = collect($decisions)->map(fn (array $d) => [
        'nr' => $d['nr'],
        'slug' => $d['slug'],
        'options' => $d['options'],
    ]);

    $featured = $articles->first();
    $rest = $articles->skip(1);
@endphp

<x-layouts::site title="Design-keuzes — Over ons normalize">

    {{-- Internal non-prod prototype: bespoke demo CSS only (band containment inside
         demo frames + the D2 radius comparison). Deliberately NOT part of the site's
         CSS architecture; this page never ships to production. --}}
    <style>
        /* Full-bleed bands (100vw trick) must stay inside their demo frame. */
        .dc-frame .about-stats,
        .dc-frame .about-band {
            width: 100%;
            margin-left: 0;
            margin-block: 0;
            padding-block: 2.5rem;
        }
        /* Closing CTA compact in demo frames (real one has royal padding). */
        .dc-frame .closing-cta > div { padding-block: 3rem; }
        /* D2: note-kaarten at the converged radius (--radius-tile) next to today's 1.25rem. */
        .dc-radius-tile .info-card,
        .dc-radius-tile .pull-quote--card {
            border-radius: var(--radius-tile);
        }
    </style>

    <script>
        window.designChoices = function () {
            return {
                decisions: @js($summaryData),
                choices: @js($defaults),
                notes: '',
                copied: false,
                init() {
                    try {
                        const saved = JSON.parse(localStorage.getItem('design-choices') ?? 'null');
                        if (saved) {
                            this.choices = Object.assign({}, this.choices, saved.choices ?? {});
                            this.notes = saved.notes ?? '';
                        }
                    } catch (e) { /* corrupt storage: keep defaults */ }
                },
                save() {
                    localStorage.setItem('design-choices', JSON.stringify({ choices: this.choices, notes: this.notes }));
                },
                pickLabel(id) {
                    const d = this.decisions[id];
                    return `${d.nr} ${d.slug}: ${this.choices[id]} — ${d.options[this.choices[id]]}`;
                },
                summary() {
                    const lines = ['Over ons · normalize keuzes'];
                    for (const id of Object.keys(this.decisions)) {
                        lines.push(this.pickLabel(id));
                    }
                    if (this.notes.trim() !== '') {
                        lines.push('', 'Notities: ' + this.notes.trim());
                    }
                    return lines.join('\n');
                },
                async copy() {
                    try {
                        await navigator.clipboard.writeText(this.summary());
                        this.copied = true;
                        setTimeout(() => { this.copied = false; }, 2000);
                    } catch (e) {
                        window.prompt('Kopieer handmatig:', this.summary());
                    }
                },
            };
        };
    </script>

    <div x-data="designChoices()" class="flex flex-col gap-16 pb-16">

        <header class="flex flex-col gap-4">
            <p class="text-sm font-semibold uppercase tracking-widest text-kidical-ink/50">Intern · niet zichtbaar in productie</p>
            <h1>Over ons · normalize keuzes</h1>
            <p class="max-w-2xl">Kies per beslissing een optie, voeg onderaan notities toe en kopieer
                de samenvatting om terug te plakken in de chat. Je keuzes blijven lokaal bewaard.</p>
        </header>

        @foreach ($decisions as $id => $d)
            <section id="{{ strtolower($d['nr']) }}" class="flex flex-col gap-6 border-t border-kidical-ink/10 pt-12">
                <div class="flex flex-col gap-2">
                    <h2 class="flex items-baseline gap-3"><span class="text-kidical-red">{{ $d['nr'] }}</span><span>{{ $d['title'] }}</span></h2>
                    <p class="max-w-3xl text-kidical-ink/70">{{ $d['context'] }}</p>
                </div>

                {{-- ================= LIVE VARIANTS ================= --}}
                @if ($id === 'd1')
                    <div class="flex flex-col gap-6">
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">A-visueel</span>
                                <span class="text-sm text-kidical-ink/70">de band zoals op de hub (.about-stats, lichtblauw, vol-breed)</span>
                            </p>
                            <section class="about-stats" aria-label="Kidical Mass in cijfers (demo)">
                                <div class="px-6">
                                    <ul class="about-stats__grid" role="list">
                                        @foreach ($statCards as $card)
                                            <li class="about-stat"><span class="about-stat__num">{{ $card['value'] }}</span><span class="about-stat__label">{{ $card['label'] }}</span></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </section>
                        </div>
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">B-visueel</span>
                                <span class="text-sm text-kidical-ink/70">het deck zoals op Wat we doen (x-stat-card, daar als kolom naast het verhaal)</span>
                            </p>
                            <div class="p-6">
                                <div class="grid max-w-2xl gap-4 sm:grid-cols-2" role="list">
                                    @foreach ($statCards as $card)
                                        <x-stat-card role="listitem" :value="$card['value']" :label="$card['label']" :color="$card['color']" />
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($id === 'd2')
                    <div class="grid gap-6 xl:grid-cols-2">
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">Display</span>
                                <span class="text-sm text-kidical-ink/70">tilt, wit, rounded-card + shadow-card</span>
                            </p>
                            <div class="p-6">
                                <div class="max-w-sm -rotate-1">
                                    <x-feature-card icon="rocket-launch" color="red" title="Fietsparades organiseren">
                                        Grote en kleine ritten waar kinderen de straat even helemaal voor zich hebben.
                                    </x-feature-card>
                                </div>
                            </div>
                        </div>
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">Quiet</span>
                                <span class="text-sm text-kidical-ink/70">recht, wit, hairline</span>
                            </p>
                            <div class="flex flex-col gap-4 p-6 bg-kidical-ink/3">
                                @if ($featured)
                                    <div class="max-w-sm">
                                        <x-article-card :article="$featured" />
                                    </div>
                                @endif
                                <div class="about-partner-card max-w-sm">
                                    <strong>Fietsersbond</strong>
                                    <p>Partner in veilige schoolroutes en fietsbeleid.</p>
                                </div>
                            </div>
                        </div>
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm xl:col-span-2">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">Note</span>
                                <span class="text-sm text-kidical-ink/70">lichtgeel, zachte tilt · radius huidig vs genormaliseerd</span>
                            </p>
                            <div class="grid max-w-4xl gap-6 p-6 sm:grid-cols-2">
                                <div class="flex flex-col gap-4">
                                    <p class="m-0 text-xs font-bold uppercase tracking-wider text-kidical-ink/50">huidig (1.25rem)</p>
                                    <x-info-card label="Perscontact">
                                        <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                                        <p class="info-card__note">We antwoorden binnen twee werkdagen.</p>
                                    </x-info-card>
                                    <x-pull-quote variant="card" attribution="Julienne, mama van twee">
                                        Wat hij zo leuk vindt aan fietsen is die vrijheid om buiten te zijn.
                                    </x-pull-quote>
                                </div>
                                <div class="dc-radius-tile flex flex-col gap-4">
                                    <p class="m-0 text-xs font-bold uppercase tracking-wider text-kidical-ink/50">genormaliseerd (--radius-tile, 1rem)</p>
                                    <x-info-card label="Perscontact">
                                        <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                                        <p class="info-card__note">We antwoorden binnen twee werkdagen.</p>
                                    </x-info-card>
                                    <x-pull-quote variant="card" attribution="Julienne, mama van twee">
                                        Wat hij zo leuk vindt aan fietsen is die vrijheid om buiten te zijn.
                                    </x-pull-quote>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="m-0 max-w-3xl text-sm font-semibold text-kidical-ink/60">Beleid: tilt = de stem van de beweging; recht = navigatie, derden, serieus register.</p>
                @endif

                @if ($id === 'd3')
                    <div class="flex flex-col gap-6">
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">Huidig</span>
                                <span class="text-sm text-kidical-ink/70">.about-band__title, topt kleiner dan de basis h2</span>
                            </p>
                            <section class="about-band about-band--light-blue">
                                <div class="flex flex-col gap-4 px-6">
                                    <h2 class="about-band__title mb-0">Drie dingen die we doen</h2>
                                    <p class="m-0 max-w-prose">Fietsparades, lokale groepen en de weg naar veilige straten.</p>
                                </div>
                            </section>
                        </div>
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">Genormaliseerd</span>
                                <span class="text-sm text-kidical-ink/70">x-section-heading op de basis h2-schaal</span>
                            </p>
                            <section class="about-band about-band--light-blue">
                                <div class="flex flex-col gap-4 px-6">
                                    <x-section-heading>Drie dingen die we doen</x-section-heading>
                                    <p class="m-0 max-w-prose">Fietsparades, lokale groepen en de weg naar veilige straten.</p>
                                </div>
                            </section>
                        </div>
                    </div>
                @endif

                @if ($id === 'd4')
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">Op wit</span>
                                <span class="text-sm text-kidical-ink/70">beide stijlen naast elkaar</span>
                            </p>
                            <div class="flex flex-col gap-6 p-6">
                                <div class="flex flex-col gap-1">
                                    <p class="m-0 text-xs font-bold uppercase tracking-wider text-kidical-ink/50">700 body-face (about-section__link)</p>
                                    <p class="about-section__link m-0"><a href="#d4">Ontdek hoe je kan meedoen</a></p>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <p class="m-0 text-xs font-bold uppercase tracking-wider text-kidical-ink/50">800 heading-face (info-card__link)</p>
                                    <p class="m-0"><a href="#d4" class="info-card__link">Ontdek hoe je kan meedoen</a></p>
                                </div>
                            </div>
                        </div>
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">In een info-card</span>
                                <span class="text-sm text-kidical-ink/70">zoals de manifest-link vandaag</span>
                            </p>
                            <div class="grid gap-4 p-6 sm:grid-cols-2">
                                <x-info-card label="Manifest">
                                    <p class="about-section__link m-0"><a href="#d4">Lees het manifest (pdf)</a></p>
                                    <p class="info-card__note">700 body-face</p>
                                </x-info-card>
                                <x-info-card label="Manifest">
                                    <a href="#d4" class="info-card__link">Lees het manifest (pdf)</a>
                                    <p class="info-card__note">800 heading-face (huidig)</p>
                                </x-info-card>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($id === 'd5')
                    <div class="flex flex-col gap-6">
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">A</span>
                                <span class="text-sm text-kidical-ink/70">huidig: uniform 3-koloms grid</span>
                            </p>
                            <div class="p-6">
                                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($articles->take(3) as $article)
                                        <x-article-card :article="$article" />
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">B</span>
                                <span class="text-sm text-kidical-ink/70">featured-first: nieuwste groot en vol-breed, rest in rustiger grid</span>
                            </p>
                            <div class="flex flex-col gap-8 p-6">
                                @if ($featured)
                                    <a href="{{ route('articles.show', $featured) }}"
                                        class="link-plain group grid overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm transition-shadow hover:shadow-md md:grid-cols-[55%_1fr]">
                                        @if ($featured->getFirstMediaUrl('main'))
                                            <div class="aspect-[16/9] overflow-hidden md:aspect-auto md:min-h-full">
                                                <img src="{{ $featured->getFirstMediaUrl('main') }}" alt="{{ $featured->title_nl }}"
                                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" decoding="async">
                                            </div>
                                        @endif
                                        <div class="flex flex-col gap-3 p-8">
                                            <h3 class="text-kidical-blue transition-colors group-hover:text-kidical-orange">{{ $featured->title_nl }}</h3>
                                            <p class="m-0 text-xs font-semibold text-kidical-ink/50">
                                                @if ($featured->author) {{ $featured->author->name }} · @endif
                                                <time datetime="{{ ($featured->published_at ?? $featured->created_at)->format('Y-m-d') }}">{{ ($featured->published_at ?? $featured->created_at)->isoFormat('D MMM YYYY') }}</time>
                                            </p>
                                            <p class="m-0 text-kidical-ink/80">{{ Str::limit(strip_tags($featured->content_nl), 260) }}</p>
                                        </div>
                                    </a>
                                @endif
                                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($rest->take(4) as $article)
                                        <x-article-card :article="$article" />
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">C</span>
                                <span class="text-sm text-kidical-ink/70">featured + compacte lijst: rustige rijen met mini-thumbnail (persarchief-register)</span>
                            </p>
                            <div class="flex flex-col gap-8 p-6">
                                @if ($featured)
                                    <a href="{{ route('articles.show', $featured) }}"
                                        class="link-plain group grid overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm transition-shadow hover:shadow-md md:grid-cols-[55%_1fr]">
                                        @if ($featured->getFirstMediaUrl('main'))
                                            <div class="aspect-[16/9] overflow-hidden md:aspect-auto md:min-h-full">
                                                <img src="{{ $featured->getFirstMediaUrl('main') }}" alt="{{ $featured->title_nl }}"
                                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" decoding="async">
                                            </div>
                                        @endif
                                        <div class="flex flex-col gap-3 p-8">
                                            <h3 class="text-kidical-blue transition-colors group-hover:text-kidical-orange">{{ $featured->title_nl }}</h3>
                                            <p class="m-0 text-xs font-semibold text-kidical-ink/50">
                                                @if ($featured->author) {{ $featured->author->name }} · @endif
                                                <time datetime="{{ ($featured->published_at ?? $featured->created_at)->format('Y-m-d') }}">{{ ($featured->published_at ?? $featured->created_at)->isoFormat('D MMM YYYY') }}</time>
                                            </p>
                                            <p class="m-0 text-kidical-ink/80">{{ Str::limit(strip_tags($featured->content_nl), 260) }}</p>
                                        </div>
                                    </a>
                                @endif
                                <ul class="m-0 list-none p-0" role="list">
                                    @foreach ($rest->take(4) as $article)
                                        <li class="border-b border-kidical-ink/10 last:border-b-0">
                                            <a href="{{ route('articles.show', $article) }}"
                                                class="link-plain group grid grid-cols-[auto_1fr] items-center gap-4 py-3 md:grid-cols-[auto_1fr_auto]">
                                                @if ($article->getFirstMediaUrl('main', 'thumb'))
                                                    <img src="{{ $article->getFirstMediaUrl('main', 'thumb') }}" alt=""
                                                        class="size-14 rounded-lg object-cover" loading="lazy" decoding="async">
                                                @else
                                                    <span class="size-14 rounded-lg bg-kidical-light-blue" aria-hidden="true"></span>
                                                @endif
                                                <span class="font-bold text-kidical-ink group-hover:text-kidical-blue">{{ $article->title_nl }}</span>
                                                <time class="hidden text-sm text-kidical-ink/60 md:block"
                                                    datetime="{{ ($article->published_at ?? $article->created_at)->format('Y-m-d') }}">{{ ($article->published_at ?? $article->created_at)->isoFormat('D MMM YYYY') }}</time>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($id === 'd6')
                    <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                        <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                            <span class="font-heading font-bold text-kidical-ink">B live</span>
                            <span class="text-sm text-kidical-ink/70">slanke gele afsluitband met mailto (x-closing-cta, hier compacter getoond)</span>
                        </p>
                        <x-closing-cta heading="Schrijf je over ons?" href="mailto:bike@kidicalmass.be" label="Mail ons" />
                    </div>
                    <p class="m-0 max-w-3xl text-sm text-kidical-ink/60">Optie A heeft geen demo nodig: de pagina eindigt dan strak op de laatste band.</p>
                @endif

                @if ($id === 'd7')
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">A · voorstel</span>
                                <span class="text-sm text-kidical-ink/70">de enquiry-band sluit de pagina af</span>
                            </p>
                            <section class="about-band about-band--light-blue">
                                <div class="flex flex-col gap-4 px-6">
                                    <h2 class="about-band__title mb-0">Werk met ons samen</h2>
                                    <div class="rounded-tile border-2 border-dashed border-kidical-blue/40 bg-white/70 p-8 text-center font-bold text-kidical-ink/60">Enquiry-band (formulier)</div>
                                </div>
                            </section>
                        </div>
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">B · huidig</span>
                                <span class="text-sm text-kidical-ink/70">enquiry-band én daarna nog een generieke CTA</span>
                            </p>
                            <div class="flex flex-col">
                                <section class="about-band about-band--light-blue">
                                    <div class="flex flex-col gap-4 px-6">
                                        <h2 class="about-band__title mb-0">Werk met ons samen</h2>
                                        <div class="rounded-tile border-2 border-dashed border-kidical-blue/40 bg-white/70 p-8 text-center font-bold text-kidical-ink/60">Enquiry-band (formulier)</div>
                                    </div>
                                </section>
                                <x-closing-cta heading="Rij mee met de buurt" :href="route('activities.index')" label="Vind een rit" />
                            </div>
                        </div>
                    </div>
                @endif

                @if ($id === 'd8')
                    <div class="flex flex-col gap-6">
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">Context</span>
                                <span class="text-sm text-kidical-ink/70">de intent-pillen bovenaan de hub linken al naar Pers en Partners</span>
                            </p>
                            <div class="p-6">
                                <ul class="about-intent" role="list">
                                    <li><a href="{{ route('volunteer') }}" class="about-intent-card link-plain"><span class="about-intent-card__label">Een groep starten of meehelpen</span><span class="about-intent-card__arrow" aria-hidden="true">→</span></a></li>
                                    <li><a href="{{ route('about.press') }}" class="about-intent-card link-plain"><span class="about-intent-card__label">Ik ben pers</span><span class="about-intent-card__arrow" aria-hidden="true">→</span></a></li>
                                    <li><a href="{{ route('about.partners') }}" class="about-intent-card link-plain"><span class="about-intent-card__label">Partner of sponsor worden</span><span class="about-intent-card__arrow" aria-hidden="true">→</span></a></li>
                                    <li><a href="{{ route('membership') }}" class="about-intent-card link-plain"><span class="about-intent-card__label">De beweging steunen</span><span class="about-intent-card__arrow" aria-hidden="true">→</span></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">B · huidig</span>
                                <span class="text-sm text-kidical-ink/70">6 leeskaarten, met Pers en Partners erin</span>
                            </p>
                            <div class="bg-kidical-ink/3 p-6">
                                <ul class="about-nav" role="list">
                                    <li>
                                        <a href="{{ route('about.mission') }}" class="about-nav-card link-plain">
                                            <span class="about-nav-card__chip"><flux:icon.flag variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                                            <h3 class="about-nav-card__title">{{ __('nav.mission') }}</h3>
                                            <p class="about-nav-card__desc">Fietsparades, lokale groepen en de weg naar veilige straten.</p>
                                            <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('about.vision') }}" class="about-nav-card link-plain">
                                            <span class="about-nav-card__chip"><flux:icon.eye variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                                            <h3 class="about-nav-card__title">{{ __('nav.vision') }}</h3>
                                            <p class="about-nav-card__desc">Vier duidelijke vragen aan steden en gemeenten.</p>
                                            <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('about.organisation') }}" class="about-nav-card link-plain">
                                            <span class="about-nav-card__chip"><flux:icon.building-office-2 variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                                            <h3 class="about-nav-card__title">{{ __('nav.organisation') }}</h3>
                                            <p class="about-nav-card__desc">Lokaal geworteld, licht gecoördineerd, gedragen door vrijwilligers.</p>
                                            <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('articles.index') }}" class="about-nav-card link-plain">
                                            <span class="about-nav-card__chip"><flux:icon.newspaper variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                                            <h3 class="about-nav-card__title">Nieuws</h3>
                                            <p class="about-nav-card__desc">Updates uit het netwerk.</p>
                                            <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('about.press') }}" class="about-nav-card link-plain">
                                            <span class="about-nav-card__chip"><flux:icon.megaphone variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                                            <h3 class="about-nav-card__title">Pers</h3>
                                            <p class="about-nav-card__desc">Kidical Mass in de media.</p>
                                            <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('about.partners') }}" class="about-nav-card link-plain">
                                            <span class="about-nav-card__chip"><flux:icon.user-group variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                                            <h3 class="about-nav-card__title">Partners</h3>
                                            <p class="about-nav-card__desc">Wie de beweging mee mogelijk maakt.</p>
                                            <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">A · voorstel</span>
                                <span class="text-sm text-kidical-ink/70">4 leeskaarten, Pers en Partners alleen via de pillen</span>
                            </p>
                            <div class="bg-kidical-ink/3 p-6">
                                <ul class="about-nav" role="list">
                                    <li>
                                        <a href="{{ route('about.mission') }}" class="about-nav-card link-plain">
                                            <span class="about-nav-card__chip"><flux:icon.flag variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                                            <h3 class="about-nav-card__title">{{ __('nav.mission') }}</h3>
                                            <p class="about-nav-card__desc">Fietsparades, lokale groepen en de weg naar veilige straten.</p>
                                            <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('about.vision') }}" class="about-nav-card link-plain">
                                            <span class="about-nav-card__chip"><flux:icon.eye variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                                            <h3 class="about-nav-card__title">{{ __('nav.vision') }}</h3>
                                            <p class="about-nav-card__desc">Vier duidelijke vragen aan steden en gemeenten.</p>
                                            <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('about.organisation') }}" class="about-nav-card link-plain">
                                            <span class="about-nav-card__chip"><flux:icon.building-office-2 variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                                            <h3 class="about-nav-card__title">{{ __('nav.organisation') }}</h3>
                                            <p class="about-nav-card__desc">Lokaal geworteld, licht gecoördineerd, gedragen door vrijwilligers.</p>
                                            <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('articles.index') }}" class="about-nav-card link-plain">
                                            <span class="about-nav-card__chip"><flux:icon.newspaper variant="solid" class="about-nav-card__icon" aria-hidden="true" /></span>
                                            <h3 class="about-nav-card__title">Nieuws</h3>
                                            <p class="about-nav-card__desc">Updates uit het netwerk.</p>
                                            <span class="about-nav-card__arrow" aria-hidden="true">→</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ================= CHOICE ================= --}}
                <fieldset class="m-0 border-0 p-0">
                    <legend class="sr-only">Keuze {{ $d['nr'] }}</legend>
                    <div class="grid gap-3 md:grid-cols-3">
                        @foreach ($d['options'] as $key => $label)
                            <label class="flex cursor-pointer items-start gap-3 rounded-tile border-2 border-kidical-ink/10 bg-white px-4 py-3 transition-colors has-[:checked]:border-kidical-blue has-[:checked]:bg-kidical-light-blue">
                                <input type="radio" name="{{ $id }}" value="{{ $key }}" x-model="choices.{{ $id }}" @change="save()" class="mt-1 accent-kidical-blue">
                                <span class="text-sm leading-snug">
                                    <strong class="font-heading">{{ $key }}.</strong> {{ $label }}
                                    @if ($key === $d['recommended'])
                                        <span class="ml-1 inline-block rounded-pill bg-kidical-green px-2 py-0.5 align-middle text-xs font-bold text-white">aanbevolen</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            </section>
        @endforeach

        {{-- ================= SUMMARY ================= --}}
        <section class="flex flex-col gap-5 rounded-card border border-kidical-ink/10 bg-white p-8 shadow-card">
            <h2>Samenvatting</h2>
            <ul class="m-0 flex list-none flex-col gap-1.5 p-0 text-sm">
                <template x-for="id in Object.keys(decisions)" :key="id">
                    <li x-text="pickLabel(id)"></li>
                </template>
            </ul>
            <label class="flex flex-col gap-2">
                <span class="text-sm font-bold text-kidical-ink">Notities</span>
                <textarea x-model="notes" @input.debounce.500ms="save()" rows="3"
                    placeholder="Opmerkingen, twijfels, mengvormen…"
                    class="w-full rounded-tile border border-kidical-ink/20 p-3 text-sm focus:border-kidical-blue focus:outline-none"></textarea>
            </label>
            <div class="flex items-center gap-4">
                <button type="button" class="about-cta__btn about-cta__btn--primary" @click="copy()"
                    x-text="copied ? 'Gekopieerd!' : 'Kopieer keuzes'"></button>
                <p class="m-0 text-sm text-kidical-ink/50">Plak dit terug in de chat. Keuzes en notities blijven lokaal bewaard.</p>
            </div>
        </section>

    </div>
</x-layouts::site>
