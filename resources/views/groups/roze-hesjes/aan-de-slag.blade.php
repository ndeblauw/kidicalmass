<x-roze-hub :group="$group" active="aan-de-slag" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    @endphp

    {{-- 5 · VOOR JE EERSTE RIT — permanent onboarding (always here, so the welcome block's
         info stays findable after it expires). The startspeech is NOT here — it is kapitein
         material (a besloten tile in Jouw materiaal). --}}
    <section id="voor-je-eerste-rit" class="roze-onboarding scroll-mt-24">
        <h2 class="roze-hub-title">Voor je eerste rit</h2>
        <p class="roze-hub-lead">Alles wat je nodig hebt voor je eerste keer meerijden. Het is makkelijker dan je denkt.</p>

        <h3 class="roze-onboarding__sub">Wat doet een roze hesje?</h3>
        <div class="roze-onboarding__cards">
            <x-roze-card heading="h4" icon="users" title="Je rijdt mee met de groep" color="red">
                Je fietst naast de kinderen en houdt ze samen. Geen kopwerk, gewoon meerijden en mee opletten.
            </x-roze-card>
            <x-roze-card heading="h4" icon="sparkles" title="Je brengt rust en goeie energie" color="orange">
                Een vrolijke, kalme aanwezigheid op de weg betekent veel. Dat ben jij.
            </x-roze-card>
            <x-roze-card heading="h4" icon="eye" title="Goed zichtbaar zijn is genoeg" color="blue">
                Een fluo hesje en een glimlach. Meer heb je niet nodig om het verschil te maken.
            </x-roze-card>
            <x-roze-card heading="h4" icon="academic-cap" title="Geen verkeersopleiding nodig" color="green">
                Dat leer je vanzelf, samen met het team. Je staat er nooit alleen voor.
            </x-roze-card>
        </div>

        {{-- Begeleidingsvideo — embedded inline (privacy-friendly nocookie host), with a
             Dutch caption so the page frames it (the YouTube overlay is English). --}}
        <figure class="roze-video-figure">
            <div class="roze-video">
                <iframe
                    src="https://www.youtube-nocookie.com/embed/i9YQxJ-ChNM"
                    title="Veilig begeleiden als roze hesje"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                ></iframe>
            </div>
            <figcaption class="roze-video__caption">Zo ziet een rit eruit: rustig, samen en op kindertempo.</figcaption>
        </figure>

        <h3 class="roze-onboarding__sub">Je eerste rit, stap voor stap</h3>
        <ol role="list" class="roze-steps">
            @foreach ([
                ['Voor de start', 'De hesjes zitten in een gemeenschappelijke tas en worden ter plaatse uitgedeeld. Je hoeft zelf niks mee te brengen.'],
                ['Onderweg', 'Vooraan rijdt een kapitein, achteraan een sluiter. Jij rijdt mee in de groep en houdt mee alles samen.'],
                ['Het tempo', 'We rijden op kindertempo, ongeveer 8 à 9 km per uur. Rustig aan, het is geen koers.'],
                ['Aan de kruispunten', 'We zetten ze samen veilig af zodat de groep kan passeren, en sluiten daarna weer aan.'],
            ] as $i => $step)
                <li class="roze-step">
                    <span class="roze-step__num">{{ $i + 1 }}</span>
                    <div>
                        <strong class="roze-step__title roze-card-title">{{ $step[0] }}</strong>
                        <p class="roze-step__body">{{ $step[1] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- WHATSAPP-DOORGANG — deliberate hand-off to the chatter, kept apart from the page so
         "stand van zaken" and "gesprek" don't try to be each other. FAUX href until a per-group
         whatsapp URL exists (Nico #37). --}}
    <section class="chapter-body roze-whatsapp">
        <div class="roze-whatsapp__inner">
            <div>
                <h2 class="roze-whatsapp__title roze-card-title">De WhatsApp-groep van {{ $gemeente }}</h2>
                <p class="roze-whatsapp__body">Voor het dagelijkse gepraat, snelle vragen en "wie kan er zondag mee".</p>
            </div>
            {{-- No live URL yet (Nico #37): an honest "binnenkort" control, not a button to nowhere. --}}
            <span class="roze-whatsapp__btn roze-whatsapp__btn--soon">Naar WhatsApp <small>(binnenkort)</small></span>
        </div>
    </section>
</x-roze-hub>
