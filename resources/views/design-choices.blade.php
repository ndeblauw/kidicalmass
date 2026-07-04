{{--
    Design-choices prototype — /design-choices (non-production only, unlinked).
    RONDE 2 van de quote-normalisatie: richting C (markeerstift) is gekozen,
    met als notities "iets meer airy" en "behoud van een grote rode curly
    quote". Deze ronde toont vier variaties binnen die richting; de attributie
    (naam + detail) is het belangrijkste speelveld. Elke variatie staat weer
    in beide echte contexten (missie-verhaalkolom op wit, visie-eisen-grid op
    de lichtblauwe band). Quote-aanhalingstekens zitten in het rode teken, dus
    de kopij is overal gestript.
    Throwaway page — verwijderd zodra de keuze in de component landt.
--}}
@php
    $stripMarks = fn (string $q): string => str_replace(['“', '”'], '', $q);
    /** @return array{name: string, detail: string} */
    $splitAttribution = function (string $full): array {
        $parts = explode(', ', $full, 2);

        return ['name' => $parts[0], 'detail' => $parts[1] ?? ''];
    };

    $julienne = ['text' => $stripMarks(__('about.mission_quote'))] + $splitAttribution(__('about.mission_quote_attribution'));
    $fatima = ['text' => $stripMarks(__('about.vision_quote_fatima'))] + $splitAttribution(__('about.vision_quote_fatima_attribution'));
    $camille = ['text' => $stripMarks(__('about.vision_quote_camille'))] + $splitAttribution(__('about.vision_quote_camille_attribution'));

    /** @var array<string, array{label: string, desc: string, class: string, attr: string}> $variants */
    $variants = [
        'C1' => [
            'label' => 'Anker — stille naam',
            'desc' => 'De rechte mix: rood teken als blok boven de quote, volle gele streep, ruime interlinie en marges. Attributie zoals in ronde 1: een stille gedempte regel.',
            'class' => 'pqx',
            'attr' => 'plain',
        ],
        'C2' => [
            'label' => 'Hangend teken — zachte streep, rood streepje',
            'desc' => 'Het rode teken hangt links naast de tekst in plaats van erboven, en de streep is de zachtere lichtgele toon. De attributie krijgt een kort rood streepje als anker.',
            'class' => 'pqx pqx--hang',
            'attr' => 'rule',
        ],
        'C3' => [
            'label' => 'Rode naam',
            'desc' => 'Zelfde opbouw als C1, maar de attributie splitst: de voornaam in kidical-rood en vet, het detail (mama van…, gemeente) gedempt en licht. De naam wordt zo een tweede rood accent naast het teken.',
            'class' => 'pqx',
            'attr' => 'redname',
        ],
        'C4' => [
            'label' => 'Afzender met initiaal-disc',
            'desc' => 'De attributie als afzender: een rode disc met de initiaal in Caprasimo, daarnaast naam (vet) en detail (gedempt) op twee regels. Het meest "dit is een echte persoon".',
            'class' => 'pqx',
            'attr' => 'disc',
        ],
    ];
@endphp

<x-layouts::site title="Design-keuzes — quote-normalisatie · ronde 2">

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

        /* ── Gedeelde basis: markeerstift + groot rood teken, airy ──────── */
        .pqx { margin-block: calc(var(--spacing) * 14); }
        .pqx blockquote { margin: 0; }
        .pqx blockquote::before {
            content: '\201C';
            display: block;
            font-family: var(--font-heading);
            font-size: clamp(3.5rem, 5vw, 4.5rem);
            line-height: 0.75;
            color: var(--color-kidical-red);
            margin-bottom: 1rem;
        }
        .pqx blockquote p {
            display: inline;
            margin: 0;
            font-size: clamp(var(--text-xl), 1.8vw, var(--text-2xl));
            font-weight: 700;
            line-height: 1.8;
            color: var(--color-kidical-ink);
            background: var(--color-kidical-yellow);
            padding: 0.15em 0.5em;
            -webkit-box-decoration-break: clone;
            box-decoration-break: clone;
        }
        .pqx figcaption {
            margin-top: 1.5rem;
            font-size: var(--text-sm);
            font-weight: 700;
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
        }

        /* C2 — teken hangt links, zachtere lichtgele streep, rood streepje. */
        .pqx--hang blockquote {
            position: relative;
            padding-left: clamp(3.25rem, 5vw, 4rem);
        }
        .pqx--hang blockquote::before {
            position: absolute;
            left: 0;
            top: 0.05em;
            margin: 0;
            font-size: clamp(2.75rem, 4vw, 3.5rem);
        }
        .pqx--hang blockquote p {
            background: var(--color-kidical-light-yellow);
        }
        .pqx--hang figcaption {
            padding-left: clamp(3.25rem, 5vw, 4rem);
        }
        .pqx--hang figcaption::before {
            content: '';
            display: inline-block;
            width: 1.75rem;
            height: 3px;
            border-radius: 2px;
            background: var(--color-kidical-red);
            margin-right: 0.6rem;
            vertical-align: 0.25em;
        }

        /* C3 — voornaam in rood, detail gedempt en licht. */
        .pq-name {
            color: var(--color-kidical-red);
            font-weight: 700;
        }
        .pqx figcaption .pq-detail {
            font-weight: 400;
        }

        /* C4 — afzender: initiaal-disc + naam/detail op twee regels. */
        .pqx figcaption.pq-sender {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1.25rem;
        }
        .pq-disc {
            display: grid;
            place-items: center;
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 50%;
            background: var(--color-kidical-red);
            color: var(--color-white);
            font-family: var(--font-heading);
            font-size: var(--text-xl);
            flex-shrink: 0;
        }
        .pq-sender-text {
            display: flex;
            flex-direction: column;
            line-height: 1.35;
        }
        .pq-sender-text strong { color: var(--color-kidical-ink); }
    </style>

    <script>
        window.designChoices = function () {
            return {
                options: @js(collect($variants)->map(fn (array $v) => $v['label'])),
                choice: 'C1',
                notes: '',
                copied: false,
                init() {
                    try {
                        const saved = JSON.parse(localStorage.getItem('design-choices-quotes-r2') ?? 'null');
                        if (saved) {
                            this.choice = saved.choice ?? 'C1';
                            this.notes = saved.notes ?? '';
                        }
                    } catch (e) { /* corrupt storage: keep defaults */ }
                },
                save() {
                    localStorage.setItem('design-choices-quotes-r2', JSON.stringify({ choice: this.choice, notes: this.notes }));
                },
                summary() {
                    const lines = [
                        'Quote-normalisatie · ronde 2 (markeerstift-variaties)',
                        `Q1 variatie: ${this.choice} — ${this.options[this.choice]}`,
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
            <p class="text-sm font-semibold uppercase tracking-widest text-kidical-ink/50">Intern · niet zichtbaar in productie · ronde 2</p>
            <h1>Quote-normalisatie · markeerstift-variaties</h1>
            <p class="max-w-2xl">Richting gekozen: markeerstift, airy, met een grote rode curly quote.
                Vier variaties binnen die richting; het grootste verschil zit in de attributie.
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
                            @foreach ([$julienne] as $quote)
                                <figure class="pull-quote {{ $variant['class'] }}">
                                    <blockquote><p>{{ $quote['text'] }}</p></blockquote>
                                    @if ($variant['attr'] === 'disc')
                                        <figcaption class="pq-sender">
                                            <span class="pq-disc" aria-hidden="true">{{ mb_substr($quote['name'], 0, 1) }}</span>
                                            <span class="pq-sender-text">
                                                <strong>{{ $quote['name'] }}</strong>
                                                <span class="pq-detail">{{ $quote['detail'] }}</span>
                                            </span>
                                        </figcaption>
                                    @elseif ($variant['attr'] === 'redname')
                                        <figcaption><span class="pq-name">{{ $quote['name'] }},</span> <span class="pq-detail">{{ $quote['detail'] }}</span></figcaption>
                                    @else
                                        <figcaption>{{ $quote['name'] }}, {{ $quote['detail'] }}</figcaption>
                                    @endif
                                </figure>
                            @endforeach
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
                                            <figure class="pull-quote {{ $variant['class'] }}">
                                                <blockquote><p>{{ $demand['quote']['text'] }}</p></blockquote>
                                                @if ($variant['attr'] === 'disc')
                                                    <figcaption class="pq-sender">
                                                        <span class="pq-disc" aria-hidden="true">{{ mb_substr($demand['quote']['name'], 0, 1) }}</span>
                                                        <span class="pq-sender-text">
                                                            <strong>{{ $demand['quote']['name'] }}</strong>
                                                            <span class="pq-detail">{{ $demand['quote']['detail'] }}</span>
                                                        </span>
                                                    </figcaption>
                                                @elseif ($variant['attr'] === 'redname')
                                                    <figcaption><span class="pq-name">{{ $demand['quote']['name'] }},</span> <span class="pq-detail">{{ $demand['quote']['detail'] }}</span></figcaption>
                                                @else
                                                    <figcaption>{{ $demand['quote']['name'] }}, {{ $demand['quote']['detail'] }}</figcaption>
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
                <legend class="sr-only">Keuze markeerstift-variatie</legend>
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
