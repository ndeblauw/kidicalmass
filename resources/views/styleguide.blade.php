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
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <h3>Typografie</h3>
                    <div class="sg-demo p-8 flex flex-col gap-3">
                        <h1>H1 — Caprasimo, blauw</h1>
                        <h2>H2 — sectiekop</h2>
                        <h3>H3 — subkop</h3>
                        <h4>H4 — kleine kop</h4>
                        <p>Body — Nunito Sans. Dit is hoe lopende tekst eruitziet op de site.</p>
                        <p><a href="#tokens">Een link met de gele onderlijn-animatie</a></p>
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

                <x-styleguide.entry name="event-card" props="activity, showDate=true, featured=auto">
                    <div class="flex flex-col gap-3 max-w-xl">
                        <x-event-card :activity="$activity" />
                        <x-event-card :activity="$activityB" />
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

                {{-- Kalender --}}
                <x-styleguide.entry name="kal-day-band" props="periodKey, rows=[['item'=>Activity]]">
                    <x-kal-day-band :period-key="$dayPeriodKey" :rows="$dayRows" />
                </x-styleguide.entry>

                <x-styleguide.entry name="kal-month-band" props="periodKey, rides=[Activity]">
                    <x-kal-month-band :period-key="$monthPeriodKey" :rides="$monthRides" />
                </x-styleguide.entry>

                {{-- Primitieven --}}
                <x-styleguide.entry name="bike-icon" props="(attributes)">
                    <x-bike-icon class="w-10 h-10 text-kidical-blue" />
                </x-styleguide.entry>

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

                <x-styleguide.entry name="about-reveal" props="selector, transform=false"
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
