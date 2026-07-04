{{--
    Design-choices prototype — /design-choices (non-production only, unlinked).
    Internal decision page for the about admin pass: two open decisions
    (person-card layout, quote treatment) rendered as live variants with the
    real site components, a radio per decision and a copyable summary.
    Additive-only; reproduces existing markup/classes, changes nothing else.
    Throwaway page — deleted once the picks land in Tasks 6/8.
--}}
@php
    $bioText = 'Leticia fietst elke week met haar twee kinderen door Brussel. Ze coördineert de vormingen en is het eerste aanspreekpunt voor nieuwe groepen.';
    $photo = asset('img/photography/ride-cinquantenaire-crowd.jpg');

    $decisions = [
        'd1' => [
            'nr' => 'D1', 'slug' => 'person-card', 'title' => 'Persoonskaart: portret-stack of horizontale rij?',
            'context' => 'Het coördinatieduo krijgt straks een foto en een korte bio. Twee lay-outrichtingen, elk met de disc-fallback voor wie (nog) geen foto aanlevert.',
            'options' => [
                'A' => 'Portrait stack — foto/disc boven, tekst eronder (huidige richting)',
                'B' => 'Horizontal row — foto/disc links, naam/rol/bio rechts',
            ],
        ],
        'd2' => [
            'nr' => 'D2', 'slug' => 'quote', 'title' => 'Pull-quote: groot en gecentreerd, of rustige kolom?',
            'context' => 'De missie-quote van Julienne staat vandaag als grote gecentreerde uitspraak (--large). Een alternatief: een stillere kolombehandeling met een randlijn, dichter bij het verhaal.',
            'options' => [
                'A' => 'Baseline — groot, gecentreerd, huidige --large',
                'B' => 'Rustige kolom — links uitgelijnd met randlijn, --column',
            ],
        ],
    ];

    $defaults = collect($decisions)->map(fn () => 'A');
    $summaryData = collect($decisions)->map(fn (array $d) => [
        'nr' => $d['nr'],
        'slug' => $d['slug'],
        'options' => $d['options'],
    ]);
@endphp

<x-layouts::site title="Design-keuzes — about admin pass">

    {{-- Internal non-prod prototype: bespoke demo CSS only, per Task 1 brief.
         Deliberately NOT part of the site's CSS architecture; this page
         never ships to production. --}}
    <style>
        /* Full-bleed bands (100vw trick) must stay inside their demo frame. */
        .dc-frame .about-band {
            width: 100%;
            margin-left: 0;
            margin-block: 0;
            padding-block: 2.5rem;
        }

        /* D1 — initial-letter disc fallback for the person-card. */
        .person-card__disc {
            display: grid;
            place-items: center;
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            background: var(--color-kidical-red);
            color: var(--color-white);
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: var(--text-2xl);
            margin-bottom: 0.5rem;
        }
        .person-card__bio {
            margin: 0.5rem 0 0;
            font-size: var(--text-sm);
            line-height: 1.5;
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 25%);
        }
        /* Variant B: row layout */
        .person-card--row {
            flex-direction: row;
            align-items: flex-start;
            gap: 1.25rem;
            padding: 1.25rem 1.5rem;
        }
        .person-card--row .person-card__photo,
        .person-card--row .person-card__disc {
            flex-shrink: 0;
            margin-bottom: 0;
        }
        .person-card__text {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }
        /* Demo-only: stack the duo list vertically for the row variant. */
        .dc-duo-stack { flex-direction: column; }

        /* D2 — quieter column treatment. */
        .pull-quote--column {
            margin-block: calc(var(--spacing) * 10);
            padding-inline-start: 1.5rem;
            border-inline-start: 4px solid var(--color-kidical-red);
        }
        .pull-quote--column blockquote { margin: 0; }
        .pull-quote--column blockquote p {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: var(--text-xl);
            line-height: 1.35;
            color: var(--color-kidical-ink);
            margin: 0;
        }
        .pull-quote--column figcaption {
            margin-top: 0.75rem;
            font-size: var(--text-sm);
            font-weight: 700;
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
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
                    const lines = ['About admin pass · design-keuzes'];
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
            <h1>About admin pass · design-keuzes</h1>
            <p class="max-w-2xl">Kies per beslissing een optie, voeg onderaan notities toe en kopieer
                de samenvatting om terug te plakken in de chat. Je keuzes blijven lokaal bewaard.</p>
        </header>

        {{-- ================= D1 · PERSON CARD ================= --}}
        @php $d = $decisions['d1']; @endphp
        <section id="d1" class="flex flex-col gap-6 border-t border-kidical-ink/10 pt-12">
            <div class="flex flex-col gap-2">
                <h2 class="flex items-baseline gap-3"><span class="text-kidical-red">{{ $d['nr'] }}</span><span>{{ $d['title'] }}</span></h2>
                <p class="max-w-3xl text-kidical-ink/70">{{ $d['context'] }}</p>
            </div>

            <div class="flex flex-col gap-6">
                <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                    <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                        <span class="font-heading font-bold text-kidical-ink">A. Portrait stack</span>
                        <span class="text-sm text-kidical-ink/70">A1 met foto, A2 met disc-fallback</span>
                    </p>
                    <section class="about-section px-6">
                        <x-section-heading>Het coördinatieduo</x-section-heading>
                        <ul class="about-duo" role="list">
                            <li>
                                <div class="person-card">
                                    <img src="{{ $photo }}" alt="Leticia" class="person-card__photo" loading="lazy">
                                    <span class="person-card__name">Leticia</span>
                                    <span class="person-card__role">Coördinatie</span>
                                    <p class="person-card__bio">{{ $bioText }}</p>
                                </div>
                            </li>
                            <li>
                                <div class="person-card">
                                    <div class="person-card__disc" aria-hidden="true">L</div>
                                    <span class="person-card__name">Leticia</span>
                                    <span class="person-card__role">Coördinatie</span>
                                    <p class="person-card__bio">{{ $bioText }}</p>
                                </div>
                            </li>
                        </ul>
                    </section>
                </div>

                <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                    <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                        <span class="font-heading font-bold text-kidical-ink">B. Horizontal row</span>
                        <span class="text-sm text-kidical-ink/70">B1 met foto, B2 met disc-fallback, gestapeld</span>
                    </p>
                    <section class="about-section px-6">
                        <x-section-heading>Het coördinatieduo</x-section-heading>
                        <ul class="about-duo dc-duo-stack" role="list">
                            <li>
                                <div class="person-card person-card--row">
                                    <img src="{{ $photo }}" alt="Leticia" class="person-card__photo" loading="lazy">
                                    <div class="person-card__text">
                                        <span class="person-card__name">Leticia</span>
                                        <span class="person-card__role">Coördinatie</span>
                                        <p class="person-card__bio">{{ $bioText }}</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="person-card person-card--row">
                                    <div class="person-card__disc" aria-hidden="true">L</div>
                                    <div class="person-card__text">
                                        <span class="person-card__name">Leticia</span>
                                        <span class="person-card__role">Coördinatie</span>
                                        <p class="person-card__bio">{{ $bioText }}</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </section>
                </div>
            </div>

            <fieldset class="m-0 border-0 p-0">
                <legend class="sr-only">Keuze {{ $d['nr'] }}</legend>
                <div class="grid gap-3 md:grid-cols-2">
                    @foreach ($d['options'] as $key => $label)
                        <label class="flex cursor-pointer items-start gap-3 rounded-tile border-2 border-kidical-ink/10 bg-white px-4 py-3 transition-colors has-[:checked]:border-kidical-blue has-[:checked]:bg-kidical-light-blue">
                            <input type="radio" name="d1" value="{{ $key }}" x-model="choices.d1" @change="save()" class="mt-1 accent-kidical-blue">
                            <span class="text-sm leading-snug"><strong class="font-heading">{{ $key }}.</strong> {{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        </section>

        {{-- ================= D2 · PULL QUOTE ================= --}}
        @php $d = $decisions['d2']; @endphp
        <section id="d2" class="flex flex-col gap-6 border-t border-kidical-ink/10 pt-12">
            <div class="flex flex-col gap-2">
                <h2 class="flex items-baseline gap-3"><span class="text-kidical-red">{{ $d['nr'] }}</span><span>{{ $d['title'] }}</span></h2>
                <p class="max-w-3xl text-kidical-ink/70">{{ $d['context'] }}</p>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                    <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                        <span class="font-heading font-bold text-kidical-ink">A. Baseline</span>
                        <span class="text-sm text-kidical-ink/70">--large, groot en gecentreerd</span>
                    </p>
                    <div class="max-w-prose p-6">
                        <p>{{ __('about.mission_welcome_body') }}</p>
                        <x-pull-quote :attribution="__('about.mission_quote_attribution')">{{ __('about.mission_quote') }}</x-pull-quote>
                        <p>{{ __('about.organisation_duo_body') }}</p>
                    </div>
                </div>

                <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                    <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                        <span class="font-heading font-bold text-kidical-ink">B. Rustige kolom</span>
                        <span class="text-sm text-kidical-ink/70">--column, links uitgelijnd met randlijn</span>
                    </p>
                    <div class="max-w-prose p-6">
                        <p>{{ __('about.mission_welcome_body') }}</p>
                        <x-pull-quote variant="column" :attribution="__('about.mission_quote_attribution')">{{ __('about.mission_quote') }}</x-pull-quote>
                        <p>{{ __('about.organisation_duo_body') }}</p>
                    </div>
                </div>
            </div>

            <fieldset class="m-0 border-0 p-0">
                <legend class="sr-only">Keuze {{ $d['nr'] }}</legend>
                <div class="grid gap-3 md:grid-cols-2">
                    @foreach ($d['options'] as $key => $label)
                        <label class="flex cursor-pointer items-start gap-3 rounded-tile border-2 border-kidical-ink/10 bg-white px-4 py-3 transition-colors has-[:checked]:border-kidical-blue has-[:checked]:bg-kidical-light-blue">
                            <input type="radio" name="d2" value="{{ $key }}" x-model="choices.d2" @change="save()" class="mt-1 accent-kidical-blue">
                            <span class="text-sm leading-snug"><strong class="font-heading">{{ $key }}.</strong> {{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        </section>

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
                <button type="button" @click="copy()"
                    class="rounded-pill bg-kidical-blue px-5 py-2.5 text-sm font-bold text-white transition-colors hover:bg-kidical-blue/90"
                    x-text="copied ? 'Gekopieerd!' : 'Kopieer keuzes'"></button>
                <p class="m-0 text-sm text-kidical-ink/50">Plak dit terug in de chat. Keuzes en notities blijven lokaal bewaard.</p>
            </div>
        </section>

    </div>
</x-layouts::site>
