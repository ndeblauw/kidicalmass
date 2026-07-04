{{--
    Design-choices prototype — /design-choices-pers (non-production only, unlinked).
    Internal decision page for the Pers-pagina arrange/polish/distill pass:
    titel, layout (artikels links, perscontact rechts), notitie-copy, lijnen
    en rij-anatomie van het archief. Elke keuze staat als live variant met de
    echte PressArticle-data en site-tokens. Throwaway; verdwijnt zodra de
    pers-pass geland is.
--}}
@php
    $decisions = [
        'k1' => [
            'nr' => 'K1', 'slug' => 'sectiekop', 'title' => 'De kop boven het perscontact',
            'context' => "'Journalisten, we praten graag' zit er wat over voor dit register. In de nieuwe layout (K2) wordt het contactblok een zijkolom, dus de vraag is ook of er nog een aparte kop nodig is.",
            'recommended' => 'A',
            'options' => [
                'A' => "Geen aparte kop: het kaartlabel 'Perscontact' volstaat, het archief krijgt de kop 'In de pers'",
                'B' => "Sobere kop 'Voor de pers' boven het contactblok",
                'C' => 'Huidige kop behouden',
            ],
        ],
        'k2' => [
            'nr' => 'K2', 'slug' => 'layout', 'title' => 'Artikels links, perscontact rechts',
            'context' => 'Vandaag: contactsectie bovenaan, archief vol-breed op een lichtblauwe band eronder. Het voorstel: één rustige tweekoloms-opzet waarin de artikels de hoofdrol spelen en het perscontact als blokje rechts meeloopt.',
            'recommended' => 'A',
            'options' => [
                'A' => 'Witte pagina, perskaart rechts en sticky: blijft in beeld terwijl je door de artikels scrolt',
                'B' => 'Witte pagina, perskaart rechts bovenaan (niet sticky)',
                'C' => 'Tweekoloms, maar het geheel blijft op de lichtblauwe band',
            ],
        ],
        'k3' => [
            'nr' => 'K3', 'slug' => 'notitie', 'title' => 'De notitie onder het mailadres',
            'context' => "'We antwoorden zo snel als vrijwilligers dat kunnen.' klopt niet: wie de persmail beantwoordt is geen vrijwilliger. Wat komt er in de plaats?",
            'recommended' => 'C',
            'options' => [
                'A' => 'Weglaten, het mailadres volstaat',
                'B' => "'We antwoorden meestal binnen een dag.'",
                'C' => "'Je hoort snel van ons.'",
            ],
        ],
        'k4' => [
            'nr' => 'K4', 'slug' => 'lijnen', 'title' => 'Minder lijnen in het archief',
            'context' => 'Elke rij heeft nu een hairline eronder: bij 28 artikels wordt dat een ladder. Witruimte kan hetzelfde werk doen.',
            'recommended' => 'A',
            'options' => [
                'A' => 'Geen lijnen tussen rijen, witruimte scheidt',
                'B' => 'Alleen een lijn onder het jaartal, rijen zelf schoon',
                'C' => 'Huidig: lijn onder elke rij',
            ],
        ],
        'k5' => [
            'nr' => 'K5', 'slug' => 'rij-anatomie', 'title' => 'De rij verder gedistilleerd',
            'context' => 'Een rij toont nu: outlet (vet) + volledige datum in een eigen kolom, dan de titel, dan een pdf-chip. Het jaartal staat al in de groepskop, dus de datum kan korter. Vraag: wat scant een bezoeker eerst, de titel of de outlet?',
            'recommended' => 'A',
            'options' => [
                'A' => "Titel eerst als link, daaronder gedempt 'outlet · 14 mei', pdf-link in diezelfde regel",
                'B' => 'Huidige kolommen houden, maar korte datum (dag + maand) zonder jaartal',
                'C' => 'Alles op één regel: outlet vet, titel, datum rechts',
            ],
        ],
    ];

    $defaults = collect($decisions)->map(fn (array $d) => $d['recommended']);
    $summaryData = collect($decisions)->map(fn (array $d) => [
        'nr' => $d['nr'],
        'slug' => $d['slug'],
        'options' => $d['options'],
    ]);

    $latestYear = $articlesByYear->keys()->first();
    $sample = $articlesByYear->first()?->take(5) ?? collect();
@endphp

<x-layouts::site title="Design-keuzes — Pers">

    {{-- Internal non-prod prototype: bespoke demo CSS only. Deliberately NOT part
         of the site's CSS architecture; this page never ships to production. --}}
    <style>
        /* Scrollbare demo-frame zodat sticky gedrag echt te zien is. */
        .dcp-scroll { height: 28rem; overflow-y: auto; }

        /* K4 — lijnvarianten bovenop de bestaande press-archive component. */
        .dcp-lines-none .press-archive__item { border-block-end: none; }
        .dcp-lines-year .press-archive__item { border-block-end: none; }
        .dcp-lines-year .press-archive__year {
            padding-block-end: 0.6rem;
            border-block-end: 1px solid var(--color-kidical-hairline);
        }

        /* K5 A — titel eerst, meta gedempt eronder. */
        .dcp-rows-title { list-style: none; margin: 0; padding: 0; }
        .dcp-rows-title li { display: grid; gap: 0.2rem; padding-block: 0.7rem; }
        .dcp-rows-title .dcp-title { font-weight: 700; line-height: 1.4; }
        .dcp-rows-title .dcp-meta {
            font-size: var(--text-sm);
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 35%);
        }
        .dcp-rows-title .dcp-meta a { font-weight: 700; }

        /* K5 B — huidige kolommen, kortere datumkolom. */
        @media (min-width: 768px) {
            .dcp-cols-short .press-archive__item { grid-template-columns: 9rem minmax(0, 1fr) auto; }
        }

        /* K5 C — alles op één regel. */
        .dcp-rows-line { list-style: none; margin: 0; padding: 0; }
        .dcp-rows-line li {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 0.2rem 0.75rem;
            padding-block: 0.55rem;
        }
        .dcp-rows-line .dcp-outlet {
            font-weight: 800;
            font-size: var(--text-sm);
            color: var(--color-kidical-ink);
        }
        .dcp-rows-line .dcp-line-title { font-weight: 700; line-height: 1.4; }
        .dcp-rows-line .dcp-date {
            margin-left: auto;
            white-space: nowrap;
            font-size: var(--text-sm);
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 35%);
        }
    </style>

    <script>
        window.designChoicesPers = function () {
            return {
                decisions: @js($summaryData),
                choices: @js($defaults),
                notes: '',
                copied: false,
                init() {
                    try {
                        const saved = JSON.parse(localStorage.getItem('design-choices-pers') ?? 'null');
                        if (saved) {
                            this.choices = Object.assign({}, this.choices, saved.choices ?? {});
                            this.notes = saved.notes ?? '';
                        }
                    } catch (e) { /* corrupt storage: keep defaults */ }
                },
                save() {
                    localStorage.setItem('design-choices-pers', JSON.stringify({ choices: this.choices, notes: this.notes }));
                },
                pickLabel(id) {
                    const d = this.decisions[id];
                    return `${d.nr} ${d.slug}: ${this.choices[id]} — ${d.options[this.choices[id]]}`;
                },
                summary() {
                    const lines = ['Pers · arrange/polish/distill keuzes'];
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

    <div x-data="designChoicesPers()" class="flex flex-col gap-16 pb-16">

        <header class="flex flex-col gap-4">
            <p class="text-sm font-semibold uppercase tracking-widest text-kidical-ink/50">Intern · niet zichtbaar in productie</p>
            <h1>Pers · arrange keuzes</h1>
            <p class="max-w-2xl">Vijf beslissingen voor de perspagina, elk met live varianten op de echte
                persdata. Kies per beslissing, voeg onderaan notities toe en kopieer de samenvatting
                terug in de chat. Je keuzes blijven lokaal bewaard.</p>
        </header>

        @foreach ($decisions as $id => $d)
            <section id="{{ strtolower($d['nr']) }}" class="flex flex-col gap-6 border-t border-kidical-ink/10 pt-12">
                <div class="flex flex-col gap-2">
                    <h2 class="flex items-baseline gap-3"><span class="text-kidical-red">{{ $d['nr'] }}</span><span>{{ $d['title'] }}</span></h2>
                    <p class="max-w-3xl text-kidical-ink/70">{{ $d['context'] }}</p>
                </div>

                {{-- ================= LIVE VARIANTS ================= --}}
                @if ($id === 'k1')
                    <div class="grid gap-6 lg:grid-cols-3">
                        <div class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2 font-heading font-bold text-kidical-ink">A</p>
                            <div class="flex flex-col gap-5 p-6">
                                <div>
                                    <h2 class="mb-3">In de pers</h2>
                                    <p class="m-0 text-sm text-kidical-ink/60">boven het archief, links</p>
                                </div>
                                <div class="max-w-xs">
                                    <x-info-card :label="__('about.press_contact_label')">
                                        <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                                    </x-info-card>
                                    <p class="mt-2 text-sm text-kidical-ink/60">de kaart draagt zelf het label, geen kop erboven</p>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2 font-heading font-bold text-kidical-ink">B</p>
                            <div class="p-6">
                                <h2 class="mb-3">Voor de pers</h2>
                                <p class="m-0 text-sm text-kidical-ink/60">sober, boven het contactblok</p>
                            </div>
                        </div>
                        <div class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2 font-heading font-bold text-kidical-ink">C · huidig</p>
                            <div class="p-6">
                                <h2 class="mb-3">{{ __('about.press_contact_title') }}</h2>
                                <p class="m-0 text-sm text-kidical-ink/60">zoals de pagina nu staat</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($id === 'k2')
                    <div class="flex flex-col gap-6">
                        <div class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                <span class="font-heading font-bold text-kidical-ink">A</span>
                                <span class="text-sm text-kidical-ink/70">wit, perskaart sticky · scroll in dit kader om het te voelen</span>
                            </p>
                            <div class="dcp-scroll">
                                <div class="grid items-start gap-10 p-8 md:grid-cols-[1.6fr_1fr]">
                                    <div class="dcp-lines-none">
                                        <h2 class="mb-6">In de pers</h2>
                                        <x-press-archive :articles-by-year="$articlesByYear" />
                                    </div>
                                    <div class="flex flex-col gap-4 md:sticky md:top-6">
                                        <x-info-card :label="__('about.press_contact_label')">
                                            <p class="info-card__note">{{ __('about.press_contact_body') }}</p>
                                            <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                                            <p class="info-card__note">Je hoort snel van ons.</p>
                                        </x-info-card>
                                        <p class="about-section__link m-0"><a href="{{ route('about.mission') }}">{{ __('about.press_background_link') }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-6">
                            <div class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                                <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                    <span class="font-heading font-bold text-kidical-ink">B</span>
                                    <span class="text-sm text-kidical-ink/70">zelfde opzet, kaart niet sticky</span>
                                </p>
                                <div class="grid items-start gap-8 p-8 md:grid-cols-[1.6fr_1fr]">
                                    <div class="dcp-lines-none">
                                        <h2 class="mb-4">In de pers</h2>
                                        <h2 class="press-archive__year">{{ $latestYear }}</h2>
                                        <ul class="press-archive__list" role="list">
                                            @foreach ($sample as $article)
                                                <li class="press-archive__item">
                                                    <span class="press-archive__meta">
                                                        <span class="press-archive__outlet">{{ $article->outlet }}</span>
                                                        <time datetime="{{ $article->published_at->toDateString() }}" class="press-archive__date">{{ $article->published_at->isoFormat('D MMMM YYYY') }}</time>
                                                    </span>
                                                    <span class="press-archive__title">{{ $article->title }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <x-info-card :label="__('about.press_contact_label')">
                                        <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                                        <p class="info-card__note">Je hoort snel van ons.</p>
                                    </x-info-card>
                                </div>
                            </div>
                            <div class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                                <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                                    <span class="font-heading font-bold text-kidical-ink">C</span>
                                    <span class="text-sm text-kidical-ink/70">zelfde opzet op de lichtblauwe band</span>
                                </p>
                                <div class="bg-kidical-light-blue">
                                    <div class="grid items-start gap-8 p-8 md:grid-cols-[1.6fr_1fr]">
                                        <div class="dcp-lines-none">
                                            <h2 class="mb-4">In de pers</h2>
                                            <h2 class="press-archive__year">{{ $latestYear }}</h2>
                                            <ul class="press-archive__list" role="list">
                                                @foreach ($sample as $article)
                                                    <li class="press-archive__item">
                                                        <span class="press-archive__meta">
                                                            <span class="press-archive__outlet">{{ $article->outlet }}</span>
                                                            <time datetime="{{ $article->published_at->toDateString() }}" class="press-archive__date">{{ $article->published_at->isoFormat('D MMMM YYYY') }}</time>
                                                        </span>
                                                        <span class="press-archive__title">{{ $article->title }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <x-info-card :label="__('about.press_contact_label')">
                                            <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                                            <p class="info-card__note">Je hoort snel van ons.</p>
                                        </x-info-card>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($id === 'k3')
                    <div class="grid max-w-4xl gap-6 md:grid-cols-3">
                        <div>
                            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-kidical-ink/50">A · geen notitie</p>
                            <x-info-card :label="__('about.press_contact_label')">
                                <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                            </x-info-card>
                        </div>
                        <div>
                            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-kidical-ink/50">B</p>
                            <x-info-card :label="__('about.press_contact_label')">
                                <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                                <p class="info-card__note">We antwoorden meestal binnen een dag.</p>
                            </x-info-card>
                        </div>
                        <div>
                            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-kidical-ink/50">C</p>
                            <x-info-card :label="__('about.press_contact_label')">
                                <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                                <p class="info-card__note">Je hoort snel van ons.</p>
                            </x-info-card>
                        </div>
                    </div>
                @endif

                @if ($id === 'k4')
                    <div class="grid gap-6 xl:grid-cols-3">
                        <div class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2 font-heading font-bold text-kidical-ink">A · geen lijnen</p>
                            <div class="dcp-lines-none p-6">
                                <h2 class="press-archive__year">{{ $latestYear }}</h2>
                                <ul class="press-archive__list" role="list">
                                    @foreach ($sample as $article)
                                        <li class="press-archive__item">
                                            <span class="press-archive__meta">
                                                <span class="press-archive__outlet">{{ $article->outlet }}</span>
                                                <time datetime="{{ $article->published_at->toDateString() }}" class="press-archive__date">{{ $article->published_at->isoFormat('D MMMM YYYY') }}</time>
                                            </span>
                                            <span class="press-archive__title">{{ $article->title }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2 font-heading font-bold text-kidical-ink">B · lijn onder het jaartal</p>
                            <div class="dcp-lines-year p-6">
                                <h2 class="press-archive__year">{{ $latestYear }}</h2>
                                <ul class="press-archive__list" role="list">
                                    @foreach ($sample as $article)
                                        <li class="press-archive__item">
                                            <span class="press-archive__meta">
                                                <span class="press-archive__outlet">{{ $article->outlet }}</span>
                                                <time datetime="{{ $article->published_at->toDateString() }}" class="press-archive__date">{{ $article->published_at->isoFormat('D MMMM YYYY') }}</time>
                                            </span>
                                            <span class="press-archive__title">{{ $article->title }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2 font-heading font-bold text-kidical-ink">C · huidig</p>
                            <div class="p-6">
                                <h2 class="press-archive__year">{{ $latestYear }}</h2>
                                <ul class="press-archive__list" role="list">
                                    @foreach ($sample as $article)
                                        <li class="press-archive__item">
                                            <span class="press-archive__meta">
                                                <span class="press-archive__outlet">{{ $article->outlet }}</span>
                                                <time datetime="{{ $article->published_at->toDateString() }}" class="press-archive__date">{{ $article->published_at->isoFormat('D MMMM YYYY') }}</time>
                                            </span>
                                            <span class="press-archive__title">{{ $article->title }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($id === 'k5')
                    <div class="grid gap-6 xl:grid-cols-3">
                        <div class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2 font-heading font-bold text-kidical-ink">A · titel eerst</p>
                            <div class="p-6">
                                <h2 class="press-archive__year">{{ $latestYear }}</h2>
                                <ul class="dcp-rows-title" role="list">
                                    @foreach ($sample as $article)
                                        <li>
                                            @if ($article->url)
                                                <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer" class="dcp-title">{{ $article->title }}</a>
                                            @else
                                                <span class="dcp-title">{{ $article->title }}</span>
                                            @endif
                                            <span class="dcp-meta">
                                                {{ $article->outlet }} · {{ $article->published_at->isoFormat('D MMM') }}@if ($article->getFirstMedia('document')) · <a href="{{ $article->getFirstMediaUrl('document') }}" target="_blank" rel="noopener noreferrer">{{ __('about.press_document_label') }}</a>@endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2 font-heading font-bold text-kidical-ink">B · kolommen, korte datum</p>
                            <div class="dcp-lines-none dcp-cols-short p-6">
                                <h2 class="press-archive__year">{{ $latestYear }}</h2>
                                <ul class="press-archive__list" role="list">
                                    @foreach ($sample as $article)
                                        <li class="press-archive__item">
                                            <span class="press-archive__meta">
                                                <span class="press-archive__outlet">{{ $article->outlet }}</span>
                                                <time datetime="{{ $article->published_at->toDateString() }}" class="press-archive__date">{{ $article->published_at->isoFormat('D MMM') }}</time>
                                            </span>
                                            <span class="press-archive__title">{{ $article->title }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                            <p class="m-0 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2 font-heading font-bold text-kidical-ink">C · één regel</p>
                            <div class="p-6">
                                <h2 class="press-archive__year">{{ $latestYear }}</h2>
                                <ul class="dcp-rows-line" role="list">
                                    @foreach ($sample as $article)
                                        <li>
                                            <span class="dcp-outlet">{{ $article->outlet }}</span>
                                            @if ($article->url)
                                                <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer" class="dcp-line-title">{{ $article->title }}</a>
                                            @else
                                                <span class="dcp-line-title">{{ $article->title }}</span>
                                            @endif
                                            <time datetime="{{ $article->published_at->toDateString() }}" class="dcp-date">{{ $article->published_at->isoFormat('D MMM') }}</time>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <p class="m-0 max-w-3xl text-sm font-semibold text-kidical-ink/60">Alle drie hier zonder rijlijnen getoond (K4 A); de lijnkeuze combineert vrij met de rij-anatomie.</p>
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
