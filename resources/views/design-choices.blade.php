{{--
    Design-choices prototype — /design-choices (non-production only, unlinked).
    Quote-normalisatie voor de about-sectie: vandaag bestaan er twee
    behandelingen naast elkaar (missie: --column met randlijn, visie: --card
    gele tegel). Deze pagina toont per optie EEN genormaliseerde behandeling
    in beide echte contexten: de witte verhaalkolom (missie) en de
    lichtblauwe eisen-band met grid (visie). Alle varianten zijn CSS-only op
    de bestaande pull-quote markup (figure > blockquote > p + figcaption).
    Throwaway page — verwijderd zodra de keuze in de component landt.
--}}
@php
    $stripMarks = fn (string $q): string => str_replace(['“', '”'], '', $q);

    $julienneQuote = __('about.mission_quote');
    $julienneName = __('about.mission_quote_attribution');
    $fatimaQuote = __('about.vision_quote_fatima');
    $fatimaName = __('about.vision_quote_fatima_attribution');
    $camilleQuote = __('about.vision_quote_camille');
    $camilleName = __('about.vision_quote_camille_attribution');

    /** @var array<string, array{label: string, desc: string, mission: string, vision: string, strip: bool}> $variants */
    $variants = [
        'A' => [
            'label' => 'Baseline — de huidige twee',
            'desc' => 'Ter referentie: missie gebruikt vandaag de kolom met rode randlijn, visie de gele kaart-tegel. Twee gezichten voor hetzelfde soort inhoud.',
            'mission' => 'column',
            'vision' => 'card',
            'strip' => false,
        ],
        'B' => [
            'label' => 'Groot & vrij',
            'desc' => 'Geen kader. Een reuzengroot rood aanhalingsteken in Caprasimo opent de quote, de tekst zelf staat groot in de kopletter met gebalanceerde regelval. De attributie krijgt een kort rood streepje als anker.',
            'mission' => 'display',
            'vision' => 'display',
            'strip' => true,
        ],
        'C' => [
            'label' => 'Markeerstift',
            'desc' => 'De stem van de ouder als aangestreepte zin: een gele highlight loopt per regel mee (zoals een markeerstift op papier). Vet gezet in de broodletter, ruime interlinie zodat de strepen ademen.',
            'mission' => 'marker',
            'vision' => 'marker',
            'strip' => false,
        ],
        'D' => [
            'label' => 'Sprekende bubbel',
            'desc' => 'Dit zijn letterlijk stemmen van ouders uit de interviews: een witte tekstballon met staartje maakt dat expliciet. De attributie staat als afzender onder de ballon, uitgelijnd op het staartje.',
            'mission' => 'bubble',
            'vision' => 'bubble',
            'strip' => false,
        ],
    ];
@endphp

<x-layouts::site title="Design-keuzes — quote-normalisatie">

    {{-- Internal non-prod prototype: bespoke demo CSS only. Deliberately NOT
         part of the site's CSS architecture; this page never ships. --}}
    <style>
        /* Full-bleed bands (100vw trick) must stay inside their demo frame. */
        .dc-frame .about-band {
            width: 100%;
            margin-left: 0;
            margin-block: 0;
            padding-block: 2.5rem;
        }
        /* In het eisen-grid regelt de li-gap de ruimte, niet de quote zelf. */
        .dc-vision .pull-quote { margin-block: 0.25rem 0; }

        /* B — Groot & vrij: vrijstaand display, reuzenteken, geen kader. */
        .pull-quote--display { margin-block: calc(var(--spacing) * 12); }
        .pull-quote--display blockquote { margin: 0; }
        .pull-quote--display blockquote::before {
            content: '\201C';
            display: block;
            font-family: var(--font-heading);
            font-size: clamp(4rem, 7vw, 5.5rem);
            line-height: 0.75;
            color: var(--color-kidical-red);
        }
        .pull-quote--display blockquote p {
            margin: 0;
            font-family: var(--font-heading);
            font-weight: 400;
            font-size: clamp(var(--text-2xl), 2.6vw, var(--text-4xl));
            line-height: 1.25;
            color: var(--color-kidical-ink);
            text-wrap: balance;
        }
        .pull-quote--display figcaption {
            margin-top: 1.25rem;
            font-size: var(--text-sm);
            font-weight: 700;
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
        }
        .pull-quote--display figcaption::before {
            content: '';
            display: inline-block;
            width: 1.75rem;
            height: 3px;
            border-radius: 2px;
            background: var(--color-kidical-red);
            margin-right: 0.6rem;
            vertical-align: 0.25em;
        }

        /* C — Markeerstift: gele highlight die per regel meeloopt. */
        .pull-quote--marker { margin-block: calc(var(--spacing) * 10); }
        .pull-quote--marker blockquote { margin: 0; }
        .pull-quote--marker blockquote p {
            display: inline;
            margin: 0;
            font-size: clamp(var(--text-xl), 1.8vw, var(--text-2xl));
            font-weight: 700;
            line-height: 1.65;
            color: var(--color-kidical-ink);
            background: var(--color-kidical-yellow);
            padding: 0.12em 0.45em;
            -webkit-box-decoration-break: clone;
            box-decoration-break: clone;
        }
        .pull-quote--marker figcaption {
            margin-top: 1rem;
            font-size: var(--text-sm);
            font-weight: 700;
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
        }

        /* D — Sprekende bubbel: witte ballon met staartje, afzender eronder. */
        .pull-quote--bubble { margin-block: calc(var(--spacing) * 8); }
        .pull-quote--bubble blockquote {
            position: relative;
            margin: 0;
            background: var(--color-white);
            border-radius: var(--radius-tile);
            padding: 1.5rem 1.75rem;
            box-shadow: var(--shadow-float);
        }
        .pull-quote--bubble blockquote::after {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            left: 1.9rem;
            width: 1.1rem;
            height: 1.1rem;
            background: var(--color-white);
            border-bottom-right-radius: 0.25rem;
            transform: rotate(45deg);
        }
        .pull-quote--bubble blockquote p {
            margin: 0;
            font-size: var(--text-xl);
            font-weight: 700;
            line-height: 1.5;
            color: var(--color-kidical-ink);
            text-indent: -0.4em;
        }
        .pull-quote--bubble figcaption {
            margin-top: 1rem;
            padding-left: 1.9rem;
            font-size: var(--text-sm);
            font-weight: 700;
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
        }
    </style>

    <script>
        window.designChoices = function () {
            return {
                options: @js(collect($variants)->map(fn (array $v) => $v['label'])),
                choice: 'A',
                notes: '',
                copied: false,
                init() {
                    try {
                        const saved = JSON.parse(localStorage.getItem('design-choices-quotes') ?? 'null');
                        if (saved) {
                            this.choice = saved.choice ?? 'A';
                            this.notes = saved.notes ?? '';
                        }
                    } catch (e) { /* corrupt storage: keep defaults */ }
                },
                save() {
                    localStorage.setItem('design-choices-quotes', JSON.stringify({ choice: this.choice, notes: this.notes }));
                },
                summary() {
                    const lines = [
                        'Quote-normalisatie · design-keuze',
                        `Q1 quote-behandeling: ${this.choice} — ${this.options[this.choice]}`,
                    ];
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
            <h1>Quote-normalisatie · design-keuzes</h1>
            <p class="max-w-2xl">Vandaag heeft de about-sectie twee quote-behandelingen (missie-kolom,
                visie-kaart). Elke optie hieronder is EEN behandeling, getoond in beide echte
                contexten. Kies onderaan, voeg notities toe en kopieer de samenvatting terug
                naar de chat.</p>
        </header>

        @foreach ($variants as $key => $variant)
            <section id="optie-{{ strtolower($key) }}" class="flex flex-col gap-6 border-t border-kidical-ink/10 pt-12">
                <div class="flex flex-col gap-2">
                    <h2 class="flex items-baseline gap-3"><span class="text-kidical-red">{{ $key }}</span><span>{{ $variant['label'] }}</span></h2>
                    <p class="max-w-3xl text-kidical-ink/70">{{ $variant['desc'] }}</p>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    {{-- Context 1: missie — witte verhaalkolom --}}
                    <div class="dc-frame overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                        <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                            <span class="font-heading font-bold text-kidical-ink">Missie</span>
                            <span class="text-sm text-kidical-ink/70">verhaalkolom op wit</span>
                        </p>
                        <div class="max-w-prose p-6">
                            <p>{{ __('about.mission_welcome_body') }}</p>
                            <x-pull-quote :variant="$variant['mission']" :attribution="$julienneName">
                                {{ $variant['strip'] ? $stripMarks($julienneQuote) : $julienneQuote }}
                            </x-pull-quote>
                            <p>{{ __('about.mission_axis1_body') }}</p>
                        </div>
                    </div>

                    {{-- Context 2: visie — eisen-grid op de lichtblauwe band --}}
                    <div class="dc-frame dc-vision overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                        <p class="m-0 flex items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                            <span class="font-heading font-bold text-kidical-ink">Visie</span>
                            <span class="text-sm text-kidical-ink/70">eisen-grid op de lichtblauwe band</span>
                        </p>
                        <section class="about-band about-band--light-blue">
                            <div class="px-6">
                                <ol class="about-demand-grid">
                                    <li>
                                        <x-numbered-item number="1" :title="__('about.vision_demand1_title')">
                                            {{ __('about.vision_demand1_body') }}
                                        </x-numbered-item>
                                        <x-pull-quote :variant="$variant['vision']" :attribution="$fatimaName">
                                            {{ $variant['strip'] ? $stripMarks($fatimaQuote) : $fatimaQuote }}
                                        </x-pull-quote>
                                    </li>
                                    <li>
                                        <x-numbered-item number="2" :title="__('about.vision_demand2_title')">
                                            {{ __('about.vision_demand2_body') }}
                                        </x-numbered-item>
                                        <x-pull-quote :variant="$variant['vision']" :attribution="$camilleName">
                                            {{ $variant['strip'] ? $stripMarks($camilleQuote) : $camilleQuote }}
                                        </x-pull-quote>
                                    </li>
                                </ol>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        @endforeach

        {{-- ================= KEUZE + SAMENVATTING ================= --}}
        <section class="flex flex-col gap-5 rounded-card border border-kidical-ink/10 bg-white p-8 shadow-card">
            <h2>Jouw keuze</h2>
            <fieldset class="m-0 border-0 p-0">
                <legend class="sr-only">Keuze quote-behandeling</legend>
                <div class="grid gap-3 md:grid-cols-2">
                    @foreach ($variants as $key => $variant)
                        <label class="flex cursor-pointer items-start gap-3 rounded-tile border-2 border-kidical-ink/10 bg-white px-4 py-3 transition-colors has-[:checked]:border-kidical-blue has-[:checked]:bg-kidical-light-blue">
                            <input type="radio" name="q1" value="{{ $key }}" x-model="choice" @change="save()" class="mt-1 accent-kidical-blue">
                            <span class="text-sm leading-snug"><strong class="font-heading">{{ $key }}.</strong> {{ $variant['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
            <label class="flex flex-col gap-2">
                <span class="text-sm font-bold text-kidical-ink">Notities</span>
                <textarea x-model="notes" @input.debounce.500ms="save()" rows="3"
                    placeholder="Opmerkingen, twijfels, mengvormen…"
                    class="w-full rounded-tile border border-kidical-ink/20 p-3 text-sm focus:border-kidical-blue focus:outline-none"></textarea>
            </label>
            <div class="flex items-center gap-4">
                <button type="button" @click="copy()"
                    class="rounded-pill bg-kidical-blue px-5 py-2.5 text-sm font-bold text-white transition-colors hover:bg-kidical-blue/90"
                    x-text="copied ? 'Gekopieerd!' : 'Kopieer keuze'"></button>
                <p class="m-0 text-sm text-kidical-ink/50">Plak dit terug in de chat. Keuze en notities blijven lokaal bewaard.</p>
            </div>
        </section>

    </div>
</x-layouts::site>
