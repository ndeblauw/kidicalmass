{{--
    Design-choices prototype — /design-choices-perskaart (non-production only,
    unlinked). Normalize/typeset pass on the perscontact card: the label
    becomes a semantic <h2> and all texts move to one size tier. 2x2 matrix
    (kopstijl x tekstmaat) rendered at sidebar width with the real info-card
    tokens. Throwaway; verdwijnt zodra de keuze geland is.
--}}
@php
    $variants = [
        [
            'id' => 'A1',
            'kop' => 'heading',
            'tekst' => 'body',
            'title' => 'Kop in koptekst-letter · alles op Body',
            'note' => 'Pitch, e-mail, notitie en link allemaal op één maat (text-xl). De e-mail onderscheidt zich alleen nog met gewicht en kleur.',
            'rec' => true,
        ],
        [
            'id' => 'B1',
            'kop' => 'label',
            'tekst' => 'body',
            'title' => 'Label-look (semantisch h2) · alles op Body',
            'note' => 'Zelfde tekstmaten als A1, maar de kop houdt de stille uppercase label-look van de kaart.',
            'rec' => false,
        ],
        [
            'id' => 'A2',
            'kop' => 'heading',
            'tekst' => 'meta',
            'title' => 'Kop in koptekst-letter · compacte Meta-teksten',
            'note' => 'Kaartteksten en link op text-sm, de e-mail blijft groot als focuspunt. Compactere zijkolom.',
            'rec' => false,
        ],
        [
            'id' => 'B2',
            'kop' => 'label',
            'tekst' => 'meta',
            'title' => 'Label-look · compacte Meta-teksten',
            'note' => 'Visueel het dichtst bij vandaag: alleen de kop wordt semantisch h2 en de link onder de kaart zakt mee naar text-sm.',
            'rec' => false,
        ],
    ];
@endphp

<x-layouts::site title="Design-keuzes — Perskaart">

    {{-- Internal non-prod prototype: bespoke demo CSS only. Never ships. --}}
    <style>
        .dcpk { max-width: 26rem; }
        .dcpk .dcpk-kop { margin: 0; }
        .v-kop-heading .dcpk-kop {
            font-size: var(--text-2xl);
            line-height: 1.2;
        }
        .v-kop-label .dcpk-kop {
            font-family: var(--font-sans);
            font-size: var(--text-xs);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%);
        }
        .dcpk p { margin: 0; }
        .v-tekst-body .dcpk-pitch { font-size: var(--text-xl); }
        .v-tekst-body .dcpk-note {
            font-size: var(--text-xl);
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 30%);
        }
        .v-tekst-meta .dcpk-pitch,
        .v-tekst-meta .dcpk-note {
            font-size: var(--text-sm);
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 30%);
        }
        .v-tekst-meta .dcpk-more { font-size: var(--text-sm); }
    </style>

    <div class="flex flex-col gap-12 pb-16">

        <header class="flex flex-col gap-4">
            <p class="text-sm font-semibold uppercase tracking-widest text-kidical-ink/50">Intern · niet zichtbaar in productie</p>
            <h1>Perskaart · typeset keuzes</h1>
            <p class="max-w-2xl">De kop wordt in alle vier de varianten een echte h2; het verschil zit in
                hoe die eruitziet en op welke maat de teksten staan. De keuze geldt ook voor de
                manifestkaart op Wat we vragen (zelfde component). Zeg gewoon A1, B1, A2 of B2 in de chat.</p>
        </header>

        <div class="grid gap-8 md:grid-cols-2">
            @foreach ($variants as $v)
                <section class="overflow-hidden rounded-xl border border-kidical-ink/10 bg-white shadow-sm">
                    <p class="m-0 flex flex-wrap items-baseline gap-2 border-b border-kidical-ink/10 bg-kidical-ink/5 px-4 py-2">
                        <span class="font-heading font-bold text-kidical-ink">{{ $v['id'] }}</span>
                        <span class="text-sm text-kidical-ink/70">{{ $v['title'] }}</span>
                        @if ($v['rec'])
                            <span class="rounded-pill bg-kidical-green px-2 py-0.5 text-xs font-bold text-white">aanbevolen</span>
                        @endif
                    </p>
                    <div class="dcpk v-kop-{{ $v['kop'] }} v-tekst-{{ $v['tekst'] }} flex flex-col gap-4 p-8">
                        <div class="info-card">
                            <h2 class="dcpk-kop">{{ __('about.press_contact_label') }}</h2>
                            <p class="dcpk-pitch">{{ __('about.press_contact_body') }}</p>
                            <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                            <p class="dcpk-note">{{ __('about.press_contact_note') }}</p>
                        </div>
                        <p class="dcpk-more m-0"><a href="{{ route('about.mission') }}" class="more-link">{{ __('about.press_background_link') }}</a></p>
                    </div>
                    <p class="m-0 border-t border-kidical-ink/10 px-4 py-3 text-sm text-kidical-ink/70">{{ $v['note'] }}</p>
                </section>
            @endforeach
        </div>

    </div>
</x-layouts::site>
