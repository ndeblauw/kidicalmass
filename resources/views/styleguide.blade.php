<x-layouts::site title="Styleguide — Kidical Mass">
    <div class="flex flex-col gap-6 mb-12">
        <p class="text-sm font-semibold uppercase tracking-widest text-kidical-ink/50">Intern · niet zichtbaar in productie</p>
        <h1>Styleguide</h1>
        <p class="max-w-2xl">Alle bouwstenen van de site op één plek: tokens, componenten en wat er nog
            wacht om een component te worden. Gebruik dit om consistent te bouwen — hergebruik wat er al is.</p>
    </div>

    <div class="flex gap-12">
        {{-- Sticky TOC --}}
        <nav class="sg-toc hidden lg:block w-52 shrink-0" aria-label="Inhoud">
            <div class="sticky top-28 flex flex-col gap-2 text-sm">
                <a href="#tokens">Tokens</a>
                <a href="#componenten">Componenten</a>
                <a href="#nog-te-extraheren">Nog te extraheren</a>
                <a href="#buiten-scope" class="text-kidical-ink/50">Buiten scope</a>
            </div>
        </nav>

        <div class="flex-1 min-w-0 flex flex-col gap-20">

            {{-- ============ TOKENS ============ --}}
            <section id="tokens" class="sg-section flex flex-col gap-8">
                <h2>Tokens</h2>

                <div class="flex flex-col gap-4">
                    <h3>Merkkleuren</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <x-styleguide.swatch token="kidical-blue" name="Blue" />
                        <x-styleguide.swatch token="kidical-red" name="Red" />
                        <x-styleguide.swatch token="kidical-yellow" name="Yellow" />
                        <x-styleguide.swatch token="kidical-green" name="Green" />
                        <x-styleguide.swatch token="kidical-orange" name="Orange" />
                        <x-styleguide.swatch token="kidical-ink" name="Ink" />
                        <x-styleguide.swatch token="kidical-sky" name="Sky" />
                        <x-styleguide.swatch token="kidical-light-blue" name="Light blue" />
                        <x-styleguide.swatch token="kidical-light-yellow" name="Light yellow" />
                        <x-styleguide.swatch token="kidical-violet" name="Violet" />
                        <x-styleguide.swatch token="kidical-coral" name="Coral" />
                        <x-styleguide.swatch token="text-body" name="Tekst" />
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <h3>Typografie</h3>
                    <div class="sg-demo p-8 flex flex-col gap-3">
                        <h1>H1 — Caprasimo, ink</h1>
                        <h2>H2 — sectiekop</h2>
                        <h3>H3 — subkop</h3>
                        <h4>H4 — kleine kop</h4>
                    </div>

                    {{-- Lopende tekst: drie maten, niet meer. Kies er één; verzin
                         nooit een eigen font-size per component. Zie @layer base in app.css. --}}
                    <div class="sg-demo p-8 flex flex-col gap-4">
                        <x-intro-text>Lead — de openingszin van een sectie. Iets groter, gewicht 400. Eén maat, vastgelegd door &lt;x-intro-text&gt;.</x-intro-text>
                        <p>Body — lopende tekst, lijstitems, knoppen en velden. var(--text-xl), gewicht 400 of 700. <strong>Dit stuk is vet (700).</strong></p>
                        <p class="text-sm text-zinc-500">Meta — bijzaken zoals een “wijzig”-link of bijschrift. var(--text-sm), gedempt.</p>
                        <p><a href="#tokens">Een link met de blauwe onderlijn-animatie</a> — een halfblauwe lijn die bij hover doorrolt naar vol blauw.</p>
                    </div>
                </div>
            </section>

            {{-- ============ COMPONENTEN ============ --}}
            <section id="componenten" class="sg-section flex flex-col gap-12">
                <h2>Componenten</h2>

                {{-- Knoppen & CTA's --}}
                <x-styleguide.entry name="cta-button"
                    props="href, variant=yellow|blue|secondary|ghost, icon=arrow|heart, size=md|sm, disabled, loading, block"
                    note="yellow staat op donkere/blauwe grond — daarom hier blue als primair getoond.">
                    <div class="flex flex-col gap-6">
                        {{-- Hiërarchie: primair → secundair → ghost --}}
                        <div class="flex flex-wrap items-center gap-4">
                            <x-cta-button href="#" variant="blue">Primair</x-cta-button>
                            <x-cta-button href="#" variant="secondary">Secundair</x-cta-button>
                            <x-cta-button href="#" variant="ghost">Ghost</x-cta-button>
                        </div>
                        {{-- Iconen, maat, states --}}
                        <div class="flex flex-wrap items-center gap-4">
                            <x-cta-button href="#" variant="blue" icon="heart">Word lid</x-cta-button>
                            <x-cta-button href="#" variant="blue" size="sm">Klein</x-cta-button>
                            <x-cta-button href="#" variant="blue" :disabled="true">Uitgeschakeld</x-cta-button>
                            <x-cta-button href="#" variant="blue" :loading="true">Bezig…</x-cta-button>
                        </div>
                        {{-- Volle breedte --}}
                        <div class="max-w-md">
                            <x-cta-button href="#" variant="secondary" :block="true">Volle breedte (block)</x-cta-button>
                        </div>
                    </div>
                </x-styleguide.entry>

                <x-styleguide.entry name="support-callout" props="variant=home, title, body">
                    <x-support-callout />
                </x-styleguide.entry>

                {{-- Kaarten --}}
                <x-styleguide.entry name="feature-card" props="icon, title, color=red|blue|orange|ink|green|violet|coral">
                    <div class="grid sm:grid-cols-3 gap-6">
                        <x-feature-card icon="clock" title="Op tijd" color="blue">Ritten starten stipt.</x-feature-card>
                        <x-feature-card icon="map" title="Veilige route" color="green">Rustige straten.</x-feature-card>
                        <x-feature-card icon="heart" title="Voor iedereen" color="red">Jong en oud welkom.</x-feature-card>
                    </div>
                </x-styleguide.entry>

                {{-- Tekst & typografie --}}
                <x-styleguide.entry name="intro-text" props="size=base|lead">
                    <x-intro-text>
                        <p>Meehelpen bij Kidical Mass is opkomen voor je eigen buurt, samen met ouders en buren die meer kinderen op de fiets willen. Een paar uur per maand, een hoop nieuwe gezichten.</p>
                    </x-intro-text>
                </x-styleguide.entry>

                <x-styleguide.entry name="section-heading" props="as=h2">
                    <x-section-heading>Iedereen is welkom</x-section-heading>
                </x-styleguide.entry>

                <x-styleguide.entry name="pull-quote" props="attribution, variant=large|card">
                    <div class="flex flex-col gap-8">
                        <x-pull-quote attribution="Julienne, mama van twee kinderen">
                            "Wat hij zo leuk vindt aan fietsen is die vrijheid om buiten te zijn."
                        </x-pull-quote>
                        <div class="about-voices">
                            <x-pull-quote variant="card" attribution="Camille, Sint-Gillis">
                                "Ik heb het gevoel dat ik de hele tijd de levenslust van mijn kinderen afrem."
                            </x-pull-quote>
                            <x-pull-quote variant="card" attribution="Fatima, Jette">
                                "Ik ben constant bang voor de auto's, de trams."
                            </x-pull-quote>
                        </div>
                    </div>
                </x-styleguide.entry>

                <x-styleguide.entry name="numbered-item" props="number, title">
                    <ol class="about-demand-grid">
                        <x-numbered-item number="1" title="Veilige fietsinfrastructuur">Aparte fietspaden die kinderen echt kunnen gebruiken.</x-numbered-item>
                        <x-numbered-item number="2" title="Tragere woonstraten">Minder snel en minder druk verkeer.</x-numbered-item>
                    </ol>
                </x-styleguide.entry>

                <x-styleguide.entry name="person-card" props="name, role, photo?">
                    <div class="flex flex-wrap gap-3">
                        <x-person-card name="Leticia" role="Coördinatie" />
                        <x-person-card name="Cecilia" role="Coördinatie" />
                    </div>
                </x-styleguide.entry>

                {{-- Ride-familie: één rij-atoom (ride-row) → dag-/maandgroepering (ride-day / ride-month).
                     Allemaal gevoed door dezelfde RideDate-woordenschat; hier samen zodat de
                     onderlinge samenhang in één oogopslag te zien is. --}}
                <x-styleguide.entry name="ride-row" props="activity, showDate=false|true"
                    note="Atoom van de ride-familie. Eén rit per rij; type-chip alleen voor niet-ritten; oranje ster markeert een uitgelichte rit.">
                    <div class="flex flex-col" style="max-width: 44rem">
                        <x-ride-row :activity="$activityB" />
                        <x-ride-row :activity="$activity" />
                        <x-ride-row :activity="$workshop" />
                        <x-ride-row :activity="$meeting" />
                    </div>
                </x-styleguide.entry>

                <x-styleguide.entry name="ride-day" props="periodKey, rows=[['item'=>Activity]]"
                    note="Ride-rows onder één dag, met typografische datum-rail. Agenda van aankomende ritten op de kalender.">
                    <x-ride-day :period-key="$activity->begin_date->toDateString()" :rows="[['item' => $activity]]" />
                </x-styleguide.entry>

                <x-styleguide.entry name="ride-month" props="periodKey, rides=[Activity]"
                    note="Ride-rows onder één maandkop. Groepering voor voorbije ritten op de kalender.">
                    <x-ride-month :period-key="$monthPeriodKey" :rides="$monthRides" />
                </x-styleguide.entry>

                <x-styleguide.entry name="info-card" props="label, as">
                    <x-info-card label="Perscontact">
                        <p>We brengen je graag in contact met lokale trekkers en gezinnen.</p>
                        <a href="mailto:{{ config('kidicalmass.contact.email') }}" class="info-card__link">{{ config('kidicalmass.contact.email') }}</a>
                        <p class="info-card__note">Je hoort snel van ons.</p>
                    </x-info-card>
                </x-styleguide.entry>

                <x-styleguide.entry name="titled-list-block" props="title">
                    <div class="max-w-lg">
                        <x-titled-list-block title="Wat je krijgt">
                            <li>Kidical Mass-materiaal en steun vanaf dag één</li>
                            <li>Opleiding rond veiligheid</li>
                            <li>Een warme bende ouders en fietsers</li>
                        </x-titled-list-block>
                    </div>
                </x-styleguide.entry>

                <x-styleguide.entry name="article-card" props="article">
                    <div class="max-w-sm">
                        <x-article-card :article="$article" />
                    </div>
                </x-styleguide.entry>

                <x-styleguide.entry name="stat-card" props="value, label, icon, color=blue|green|red">
                    <div class="grid sm:grid-cols-3 gap-6">
                        <x-stat-card value="5.500" label="fietsers" icon="users" color="blue" />
                        <x-stat-card value="21" label="lokale groepen" icon="map-pin" color="green" />
                        <x-stat-card value="120" label="ritten" icon="calendar" color="red" />
                    </div>
                </x-styleguide.entry>

                <x-styleguide.entry name="group-statistics" props=":statistics (year => count)"
                    note="Bevat nog Engelse hardcoded copy ('We are growing!', 'groups') — zie kandidaten.">
                    <x-group-statistics :statistics="$statistics" />
                </x-styleguide.entry>

                {{-- Primitieven --}}
                <x-styleguide.entry name="placeholder-pattern" props="id">
                    <x-placeholder-pattern class="w-40 h-24 text-kidical-ink/20" />
                </x-styleguide.entry>

                {{-- Closing CTA: page-owned full-bleed band; safe to show inline --}}
                <x-styleguide.entry name="closing-cta" props="heading, href, label, icon=arrow|heart"
                    note="Normaal in de layout-'closing'-slot, vlak boven de footer.">
                    <x-closing-cta heading="Rijd mee met de volgende Kidical Mass" href="#" label="Bekijk de kalender" />
                </x-styleguide.entry>

                {{-- Markup + note only (not safe to render inline) --}}
                <x-styleguide.entry name="page-hero" props="eyebrow, title, illustration?"
                    note="position: fixed (vastgepind achter de pagina) — niet inline te tonen. Zie bovenkant van elke binnenpagina." />

                <x-styleguide.entry name="partners" props="(geen — DB + route-gated)"
                    note="Rendert alleen op home + about-pagina's en bevraagt de Partner-tabel. Niet los te tonen." />

                <x-styleguide.entry name="scroll-reveal" props="selector, transform=false"
                    note="Alleen een <script> voor scroll-reveal. Geen visuele weergave." />

                <x-styleguide.entry name="contact-form" props="(geen)"
                    note="Interactief formulier-eiland. Gebruik op de contactpagina; hier niet ingebed." />
            </section>

            {{-- ============ NOG TE EXTRAHEREN ============ --}}
            <section id="nog-te-extraheren" class="sg-section flex flex-col gap-6">
                <h2>Nog te extraheren</h2>
                <p class="max-w-2xl">Terugkerende stukken UI in de pagina-templates die nog geen component zijn.
                    Werk deze lijst af door elk te extraheren en naar "Componenten" te verplaatsen.</p>

                @forelse ($candidates as $candidate)
                    <div class="sg-demo p-6 flex flex-col gap-2">
                        <p class="font-semibold text-kidical-blue">{{ $candidate['name'] }}</p>
                        <p class="text-sm"><span class="font-semibold">Waar:</span> {{ $candidate['where'] }}</p>
                        <p class="text-sm"><span class="font-semibold">Voorgestelde props:</span> <code class="text-xs">{{ $candidate['props'] }}</code></p>
                    </div>
                @empty
                    <p class="text-kidical-ink/50">Nog geen kandidaten genoteerd.</p>
                @endforelse
            </section>

            {{-- ============ BUITEN SCOPE ============ --}}
            <section id="buiten-scope" class="sg-section flex flex-col gap-4">
                <h2>Buiten scope</h2>
                <p class="max-w-2xl text-kidical-ink/60">Auth-, instellingen- en Filament-scaffolding horen niet bij de
                    publieke designtaal en worden hier niet getoond:</p>
                <p class="text-sm text-kidical-ink/60"><code>app-logo</code>, <code>app-logo-icon</code>,
                    <code>auth-header</code>, <code>auth-session-status</code>, <code>action-message</code>,
                    <code>desktop-user-menu</code>, <code>stub</code>, <code>settings/*</code>, <code>wire/*</code></p>
            </section>
        </div>
    </div>
</x-layouts::site>
