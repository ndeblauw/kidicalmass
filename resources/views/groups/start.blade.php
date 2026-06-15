{{--
    Een lokale groep starten (P-?? — new, planned 2026-06-15)
    The canonical "start a local group" page. Replaces the mailto:bike@ coda on Help
    out + the CTA on Chapters (D-12 "email black hole"). Strategy: dissolve three
    barriers — "te groot een klus" (de deal), "wie steunt mij?" (je staat er niet
    alleen voor + the warm form), "is er animo?" (er is animo / proof) — while being
    honest about the commitment so the team gets fewer, higher-intent leads.
    One intent form, two comfort paths (praten met een trekker / klaar voor contact).
    Out of nav, reached contextually. Plan: docs/wiki/design/30-skeleton/start-een-groep.md
--}}
<x-layouts::site title="Een lokale groep starten">

    <x-page-hero eyebrow="Een lokale groep starten" title="Breng Kidical Mass naar jouw buurt" illustration="img/illustrations/cyclist-peace-sign.svg">

        <x-slot:lead>
            <p>Je hebt geen vereniging nodig en je hoeft geen fietsexpert te zijn. Een klein kernteam,
            een vertrekpunt en wat goesting volstaan om te beginnen. De rest doen we samen.</p>
        </x-slot:lead>

        <x-slot:controls>
            <x-cta-button href="#start" variant="blue">Ik wil starten</x-cta-button>
        </x-slot:controls>

        {{-- DE DEAL — dissolves "te groot een klus voor mij" --}}
        <section class="sg-deal">
            <h2 class="sg-deal__title">Je hoeft dit niet alleen te dragen</h2>
            <div class="sg-deal__cols">
                <x-titled-list-block title="Wat jij brengt">
                    <li>Een kernteam van twee of drie mensen</li>
                    <li>Kennis van je eigen buurt</li>
                    <li>Een vertrekpunt en een route-idee</li>
                    <li>Energie en goesting</li>
                </x-titled-list-block>

                <x-titled-list-block title="Wat wij dragen">
                    <li>Het merk en al het materiaal, van flyers tot hesjes</li>
                    <li>Opleiding rond veilige begeleiding en routeplanning</li>
                    <li>Nationale zichtbaarheid en communicatie</li>
                    <li>Coaching en een vast aanspreekpunt</li>
                    <li>Contacten met gemeenten, partners en fietsbrigades</li>
                    <li>Subsidieaanvragen voor de hele organisatie</li>
                </x-titled-list-block>
            </div>
        </section>

        {{-- WAT HET ÉCHT VRAAGT — the honest filter (fewer, higher-intent leads) --}}
        <section class="sg-asks">
            <h2 class="sg-asks__title">Wat het écht vraagt</h2>
            <p class="sg-asks__lead">Eerlijk is eerlijk: een groep dragen is een engagement over een
            heel seizoen. Dit verwachten we van een lokale trekker.</p>
            <ul class="sg-asks__list" role="list">
                <li>Een paar ritten per jaar mee plannen en begeleiden</li>
                <li>Eén afgevaardigde naar de vier jaarlijkse Kidical-meetings</li>
                <li>Je scharen achter ons huishoudelijk reglement rond veiligheid en goede vibes</li>
                <li>Genoeg begeleiders verzamelen: minstens één roze hesje per tien deelnemers</li>
            </ul>
        </section>

        {{-- ER IS ANIMO — proof, dissolves "is er wel animo hier?" --}}
        <section class="sg-proof">
            <h2 class="sg-proof__title">Er is animo</h2>
            <p class="sg-proof__lead">Kidical Mass groeit door heel België. Samen reden we in 2024 meer
            dan 60 parades bij elkaar, en het netwerk telt intussen {{ $groupCount }} lokale groepen.
            Jouw stad kan de volgende zijn.</p>
        </section>

        {{-- JE STAAT ER NIET ALLEEN VOOR — dissolves "wie steunt mij?" --}}
        <section class="sg-support">
            <h2 class="sg-support__title">Je staat er niet alleen voor</h2>
            <ul class="sg-support__list" role="list">
                <li>Een coördinatieduo dat je coacht en motiveert</li>
                <li>Een materiaalbibliotheek: charters, draaiboeken, posters en flyers</li>
                <li>Training voor jou en je begeleiders bij de start van het seizoen</li>
                <li>En, als je wil, een trekker die het al deed om mee te sparren</li>
            </ul>
        </section>

        {{-- INTENT-FORM — the climax. One form, two comfort paths. --}}
        <section class="sg-form" id="start">
            <h2 class="sg-form__title">Zin om te beginnen?</h2>
            <p class="sg-form__lead">Laat hieronder van je horen. Je kiest zelf hoe je eerste stap
            eruitziet: eerst praten met iemand die het al deed, of meteen contact met het team.</p>
            <livewire:start-group-enquiry />
        </section>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Benieuwd welke steden al meefietsen?"
            :href="route('groups.index')" label="Bekijk alle groepen" icon="arrow" />
    </x-slot:closing>

</x-layouts::site>
