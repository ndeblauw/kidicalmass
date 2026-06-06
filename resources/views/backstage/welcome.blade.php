{{--
    Backstage — first-login welcome (P1a, the hero of the onboarding prototype).
    "Klaar voor je eerste rit": sequenced onboarding, not a browsed library. Every block
    answers an interview pain (role confusion, vests, the lost start-of-ride speech, "who
    leads my chapter"). Spec: docs/superpowers/specs/2026-06-06-pink-vest-onboarding-prototype-design.md
--}}
<x-layouts::backstage title="Welkom" :group="$group" :volunteer="$volunteer">

    {{-- HERO — celebratory blue band --}}
    <section class="bg-kidical-blue text-white">
        <div class="container mx-auto px-4 py-16 md:py-20 max-w-4xl">
            <p class="text-base font-bold uppercase tracking-[0.12em] text-white/70 mb-4">Je account staat klaar</p>
            <h1 class="text-white -rotate-2 origin-left mb-6">Welkom, {{ \Illuminate\Support\Str::before($volunteer->name, ' ') }}! 👋</h1>
            <p class="text-2xl md:text-3xl font-bold leading-snug text-white/95 max-w-2xl">
                Je bent een roze hesje bij Kidical Mass {{ $group->name }}.
            </p>
            <p class="text-xl text-white/80 mt-4 max-w-2xl">
                Hier staat alles wat je nodig hebt voor je eerste rit. Neem even de tijd, daarna vind
                je het altijd terug in je materiaalbibliotheek.
            </p>
        </div>
    </section>

    {{-- WAT DOET EEN ROZE HESJE --}}
    <section class="bg-white">
        <div class="container mx-auto px-4 py-14 md:py-16 max-w-5xl">
            <h2 class="mb-3">Wat doet een roze hesje?</h2>
            <p class="text-kidical-ink/70 max-w-2xl mb-10">
                Kort gezegd: jij houdt de groep samen en zorgt dat iedereen veilig en vrolijk aankomt.
            </p>
            <div class="grid gap-6 md:grid-cols-2">
                <x-feature-card icon="users" title="Je rijdt mee met de groep" color="red">
                    Je fietst naast de kinderen en houdt ze samen. Geen kopwerk, gewoon meerijden en
                    mee opletten.
                </x-feature-card>
                <x-feature-card icon="sparkles" title="Je brengt rust en goeie energie" color="orange">
                    Een vrolijke, kalme aanwezigheid op de weg doet meer dan je denkt. Dat ben jij.
                </x-feature-card>
                <x-feature-card icon="eye" title="Goed zichtbaar zijn is genoeg" color="blue">
                    Een fluo hesje en een glimlach. Meer heb je niet nodig om het verschil te maken.
                </x-feature-card>
                <x-feature-card icon="academic-cap" title="Geen verkeersopleiding nodig" color="green">
                    Dat leer je vanzelf, samen met het team. Je staat er nooit alleen voor.
                </x-feature-card>
            </div>
        </div>
    </section>

    {{-- JE EERSTE RIT, STAP VOOR STAP — light-blue band, vertical stepper --}}
    <section class="bg-kidical-light-blue">
        <div class="container mx-auto px-4 py-14 md:py-16 max-w-3xl">
            <h2 class="mb-3">Je eerste rit, stap voor stap</h2>
            <p class="text-kidical-ink/70 mb-10">Zo verloopt een Kidical Mass. Niks om je zorgen over te maken.</p>

            <ol role="list" class="space-y-5">
                @foreach ([
                    ['Voor de start', 'De hesjes zitten in een gemeenschappelijke tas en worden ter plaatse uitgedeeld. Je hoeft zelf niks mee te brengen.'],
                    ['Onderweg', 'Vooraan rijdt een kapitein, achteraan een sluiter. Jij rijdt mee in de groep en houdt mee alles samen.'],
                    ['Het tempo', 'We rijden op kindertempo, ongeveer 8 à 9 km per uur. Rustig aan, het is geen koers.'],
                    ['Aan de kruispunten', 'We zetten ze samen veilig af zodat de groep kan passeren, en sluiten daarna weer aan.'],
                ] as $i => $step)
                    <li class="flex gap-5 items-start bg-white rounded-card shadow-card p-6">
                        <span class="flex items-center justify-center shrink-0 size-12 rounded-full bg-kidical-blue text-white font-heading text-2xl leading-none">{{ $i + 1 }}</span>
                        <div>
                            <strong class="block font-heading text-2xl font-normal text-kidical-ink leading-tight mb-1">{{ $step[0] }}</strong>
                            <p class="text-lg text-kidical-ink/75 leading-relaxed">{{ $step[1] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- WIE LEIDT JOUW AFDELING + DE STARTSPEECH --}}
    <section class="bg-white">
        <div class="container mx-auto px-4 py-14 md:py-16 max-w-5xl grid gap-8 md:grid-cols-2 items-start">

            {{-- Lead --}}
            <div>
                <h2 class="mb-6">Wie leidt jouw afdeling</h2>
                <div class="flex items-center gap-5 bg-kidical-light-yellow/60 rounded-card p-7">
                    <span class="flex items-center justify-center shrink-0 size-20 rounded-full bg-kidical-red text-white font-heading text-3xl" aria-hidden="true">{{ $lead?->initials() }}</span>
                    <div>
                        <strong class="block font-heading text-2xl font-normal text-kidical-ink leading-tight">{{ $lead?->name }}</strong>
                        <p class="text-kidical-ink/70">Coördinator in {{ $group->name }}</p>
                        <p class="text-kidical-ink/70 mt-2">Vragen? Bij {{ \Illuminate\Support\Str::before($lead?->name ?? '', ' ') }} kan je altijd terecht.</p>
                    </div>
                </div>
            </div>

            {{-- Start-of-ride speech (the Jorge fix: findable + copy-paste) --}}
            <div x-data="{ copied: false }">
                <h2 class="mb-6">De startspeech</h2>
                <div class="bg-kidical-ink rounded-card p-7 text-white/90">
                    <p class="text-white/70 mb-4">
                        Elke rit begint met een kort woordje: welkom, waarom we rijden, en de afspraken
                        rond veiligheid. Hier staat ze, klaar om te gebruiken.
                    </p>
                    <blockquote class="text-lg leading-relaxed border-l-4 border-kidical-yellow pl-4 italic" x-ref="speech">
                        "Welkom allemaal! Leuk dat jullie er zijn. Wij zijn Kidical Mass {{ $group->name }},
                        en we fietsen samen omdat we willen dat kinderen veilig en vrolijk door hun eigen
                        buurt kunnen rijden. We blijven samen, we rijden op kindertempo, en we volgen de
                        roze hesjes. Bij elk kruispunt wachten we tot iedereen mee is. Klaar? Dan rijden we!"
                    </blockquote>
                    <button type="button"
                        class="mt-5 inline-flex items-center gap-2 bg-kidical-yellow text-kidical-ink font-bold rounded-full px-5 py-2.5 hover:brightness-105"
                        x-on:click="navigator.clipboard.writeText($refs.speech.innerText); copied = true; setTimeout(() => copied = false, 2000)">
                        <flux:icon name="clipboard-document" variant="solid" class="size-5" aria-hidden="true" />
                        <span x-text="copied ? 'Gekopieerd!' : 'Kopieer de startspeech'">Kopieer de startspeech</span>
                    </button>
                </div>
            </div>

        </div>
    </section>

    {{-- ONZE AFSPRAKEN — light-yellow band --}}
    <section class="bg-kidical-light-yellow">
        <div class="container mx-auto px-4 py-14 md:py-16 max-w-4xl">
            <h2 class="mb-3">Onze afspraken</h2>
            <p class="text-kidical-ink/70 max-w-2xl mb-8">
                Waar we samen voor staan. Kort en simpel, want zo werkt het ook.
            </p>
            <ul role="list" class="flex flex-wrap gap-3 mb-8">
                @foreach ([
                    'Veiligheid voorop', 'Iedereen welkom', 'Vriendelijkheid (#kindnessisking)',
                    'Positieve actie, met de glimlach', 'Samen organiseren',
                ] as $principle)
                    <li class="inline-flex items-center gap-2 bg-white rounded-full pl-3 pr-4 py-2 shadow-card font-bold text-kidical-ink">
                        <flux:icon name="heart" variant="solid" class="size-4 text-kidical-red" aria-hidden="true" />
                        {{ $principle }}
                    </li>
                @endforeach
            </ul>
            <a href="#" class="font-bold">Lees onze afspraken (PDF) →</a>
        </div>
    </section>

    {{-- VOLGENDE + CTA --}}
    <section class="bg-white">
        <div class="container mx-auto px-4 py-14 md:py-16 max-w-4xl">
            <h2 class="mb-6">Wat komt eraan</h2>
            <ul role="list" class="space-y-3 mb-12">
                @forelse ($upcoming as $activity)
                    <li class="flex items-center gap-4 bg-kidical-light-blue/40 rounded-2xl p-5">
                        <flux:icon name="{{ $activity->activity_type->value === 'kidicalmass' ? 'flag' : 'calendar-days' }}" variant="solid" class="size-7 text-kidical-blue shrink-0" aria-hidden="true" />
                        <div>
                            <strong class="block text-lg text-kidical-ink">{{ $activity->title_nl }}</strong>
                            <span class="text-kidical-ink/65">
                                <time datetime="{{ $activity->begin_date->toIso8601String() }}">{{ ucfirst($activity->begin_date->translatedFormat('l j F')) }} · {{ $activity->begin_date->format('H\u i') }}</time>
                                · {{ $activity->location }}
                            </span>
                        </div>
                    </li>
                @empty
                    <li class="text-kidical-ink/60">Binnenkort plannen we de volgende rit.</li>
                @endforelse
            </ul>

            <div class="flex flex-wrap gap-4">
                <x-cta-button :href="route('backstage.home', $group)" variant="blue">Naar je materiaal</x-cta-button>
                <a href="{{ route('backstage.team', $group) }}" class="inline-flex items-center font-bold text-kidical-blue">Bekijk je team →</a>
            </div>
        </div>
    </section>

</x-layouts::backstage>
