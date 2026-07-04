{{--
    Design-choices prototype — /design-choices (non-production only, unlinked).
    RONDE 3 van de quote-normalisatie: de quote zelf ligt vast (C2 uit ronde 2:
    hangend rood teken, zachte lichtgele streep, rood streepje als anker).
    Frederiks notitie: de afzender-sectie mist typografische verfijning, naam
    en detail zijn nu "te veel hetzelfde". Deze ronde toont drie
    afzender-behandelingen onder exact dezelfde quote, weer in beide contexten.
    Throwaway page — verwijderd zodra de keuze in de component landt.
--}}
@php
    $stripMarks = fn (string $q): string => str_replace(['“', '”'], '', $q);
    /** @return array{name: string, detail: string, parts: list<string>} */
    $splitAttribution = function (string $full): array {
        $parts = explode(', ', $full, 2);

        return [
            'name' => $parts[0],
            'detail' => $parts[1] ?? '',
            'parts' => $parts[1] ?? '' ? explode(', ', $parts[1]) : [],
        ];
    };

    $julienne = ['text' => $stripMarks(__('about.mission_quote'))] + $splitAttribution(__('about.mission_quote_attribution'));
    $fatima = ['text' => $stripMarks(__('about.vision_quote_fatima'))] + $splitAttribution(__('about.vision_quote_fatima_attribution'));
    $camille = ['text' => $stripMarks(__('about.vision_quote_camille'))] + $splitAttribution(__('about.vision_quote_camille_attribution'));

    /** @var array<string, array{label: string, desc: string, attr: string}> $variants */
    $variants = [
        'F1' => [
            'label' => 'Twee regels',
            'desc' => 'Het rode streepje ankert de naam (vet, vol ink); het detail zakt naar een tweede regel, licht en gedempt, uitgelijnd onder de naam. De afzender wordt een klein blokje met eigen hiërarchie.',
            'attr' => 'lines',
        ],
        'F2' => [
            'label' => 'Kleinkapitaal naam',
            'desc' => 'De naam als klein kapitaal met letterspatiëring (het eyebrow-register van de site), het detail erachter in gewone zetting, licht en gedempt. Eén regel, maar duidelijk twee stemmen.',
            'attr' => 'caps',
        ],
        'F3' => [
            'label' => 'Naam met rode middots',
            'desc' => 'Eén regel: naam vet in vol ink, de detaildelen gescheiden door rode middots in plaats van komma\'s. De middots echoën het rode streepje en het teken.',
            'attr' => 'dots',
        ],
    ];
@endphp

<x-layouts::site title="Design-keuzes — quote-normalisatie · ronde 3">

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
        .dc-vision .pqx { margin-block: 0.75rem 0; }

        /* ── Vaste quote (C2): hangend teken, zachte streep, airy ───────── */
        .pqx { margin-block: calc(var(--spacing) * 14); }
        .pqx blockquote {
            margin: 0;
            position: relative;
            padding-left: clamp(3.25rem, 5vw, 4rem);
        }
        .pqx blockquote::before {
            content: '\201C';
            position: absolute;
            left: 0;
            top: 0.05em;
            font-family: var(--font-heading);
            font-size: clamp(2.75rem, 4vw, 3.5rem);
            line-height: 0.75;
            color: var(--color-kidical-red);
        }
        .pqx blockquote p {
            display: inline;
            margin: 0;
            font-size: clamp(var(--text-xl), 1.8vw, var(--text-2xl));
            font-weight: 700;
            line-height: 1.8;
            color: var(--color-kidical-ink);
            background: var(--color-kidical-light-yellow);
            padding: 0.15em 0.5em;
            -webkit-box-decoration-break: clone;
            box-decoration-break: clone;
        }
        .pqx figcaption {
            margin-top: 1.5rem;
            padding-left: clamp(3.25rem, 5vw, 4rem);
            font-size: var(--text-sm);
            font-weight: 700;
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
        }
        .pqx figcaption::before {
            content: '';
            display: inline-block;
            width: 1.75rem;
            height: 3px;
            border-radius: 2px;
            background: var(--color-kidical-red);
            margin-right: 0.6rem;
            vertical-align: 0.25em;
        }
        /* Gedeeld: detail licht en gedempt, naam vol ink. */
        .pqx figcaption .pq-detail {
            font-weight: 400;
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%);
        }
        .pqx figcaption strong {
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 10%);
        }

        /* F1 — twee regels: streepje + naam op regel 1, detail eronder. */
        .pq-attr-lines {
            display: grid;
            grid-template-columns: auto 1fr;
            column-gap: 0.6rem;
        }
        .pq-attr-lines::before {
            margin-right: 0;
            align-self: center;
        }
        .pq-attr-lines .pq-detail {
            grid-column: 2;
            line-height: 1.4;
        }

        /* F2 — naam als klein kapitaal met letterspatiëring. */
        .pq-name-caps {
            text-transform: uppercase;
            font-size: var(--text-xs);
            letter-spacing: 0.08em;
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 10%);
            margin-right: 0.35rem;
        }

        /* F3 — rode middots tussen de detaildelen. */
        .pq-sep {
            color: var(--color-kidical-red);
            font-weight: 700;
            margin-inline: 0.4rem;
        }
    </style>

    <script>
        window.designChoices = function () {
            return {
                options: @js(collect($variants)->map(fn (array $v) => $v['label'])),
                choice: 'F1',
                notes: '',
                copied: false,
                init() {
                    try {
                        const saved = JSON.parse(localStorage.getItem('design-choices-quotes-r3') ?? 'null');
                        if (saved) {
                            this.choice = saved.choice ?? 'F1';
                            this.notes = saved.notes ?? '';
                        }
                    } catch (e) { /* corrupt storage: keep defaults */ }
                },
                save() {
                    localStorage.setItem('design-choices-quotes-r3', JSON.stringify({ choice: this.choice, notes: this.notes }));
                },
                summary() {
                    const lines = [
                        'Quote-normalisatie · ronde 3 (afzender-verfijning)',
                        `Q1 afzender: ${this.choice} — ${this.options[this.choice]}`,
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
            <p class="text-sm font-semibold uppercase tracking-widest text-kidical-ink/50">Intern · niet zichtbaar in productie · ronde 3</p>
            <h1>Quote-normalisatie · afzender-verfijning</h1>
            <p class="max-w-2xl">De quote ligt vast (C2: hangend rood teken, zachte streep). Drie
                afzender-behandelingen met meer typografisch onderscheid tussen naam en detail.
                Kies onderaan, noteer mengvormen en kopieer de samenvatting terug naar de chat.</p>
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
                            <figure class="pull-quote pqx">
                                <blockquote><p>{{ $julienne['text'] }}</p></blockquote>
                                @if ($variant['attr'] === 'lines')
                                    <figcaption class="pq-attr-lines">
                                        <strong>{{ $julienne['name'] }}</strong>
                                        <span class="pq-detail">{{ $julienne['detail'] }}</span>
                                    </figcaption>
                                @elseif ($variant['attr'] === 'caps')
                                    <figcaption><span class="pq-name-caps">{{ $julienne['name'] }}</span> <span class="pq-detail">{{ $julienne['detail'] }}</span></figcaption>
                                @else
                                    <figcaption><strong>{{ $julienne['name'] }}</strong>@foreach ($julienne['parts'] as $part)<span class="pq-sep" aria-hidden="true">·</span><span class="pq-detail">{{ $part }}</span>@endforeach</figcaption>
                                @endif
                            </figure>
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
                                    @foreach ([['nr' => 1, 'title' => __('about.vision_demand1_title'), 'body' => __('about.vision_demand1_body'), 'quote' => $fatima], ['nr' => 2, 'title' => __('about.vision_demand2_title'), 'body' => __('about.vision_demand2_body'), 'quote' => $camille]] as $demand)
                                        <li>
                                            <x-numbered-item :number="$demand['nr']" :title="$demand['title']">
                                                {{ $demand['body'] }}
                                            </x-numbered-item>
                                            <figure class="pull-quote pqx">
                                                <blockquote><p>{{ $demand['quote']['text'] }}</p></blockquote>
                                                @if ($variant['attr'] === 'lines')
                                                    <figcaption class="pq-attr-lines">
                                                        <strong>{{ $demand['quote']['name'] }}</strong>
                                                        <span class="pq-detail">{{ $demand['quote']['detail'] }}</span>
                                                    </figcaption>
                                                @elseif ($variant['attr'] === 'caps')
                                                    <figcaption><span class="pq-name-caps">{{ $demand['quote']['name'] }}</span> <span class="pq-detail">{{ $demand['quote']['detail'] }}</span></figcaption>
                                                @else
                                                    <figcaption><strong>{{ $demand['quote']['name'] }}</strong>@foreach ($demand['quote']['parts'] as $part)<span class="pq-sep" aria-hidden="true">·</span><span class="pq-detail">{{ $part }}</span>@endforeach</figcaption>
                                                @endif
                                            </figure>
                                        </li>
                                    @endforeach
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
                <legend class="sr-only">Keuze afzender-behandeling</legend>
                <div class="grid gap-3 md:grid-cols-3">
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
