{{--
    Backstage — material-library home (P1b). The standing landing a volunteer returns to.
    D-1 layer 2: documents, video, posters, the speech, what's coming up, the team.
    Directly answers Jorge ("findable, copy/paste-ready") + Morgane ("can't dig it out of WhatsApp").
--}}
<x-layouts::backstage title="Backstage {{ $group->name }}" :group="$group" :volunteer="$volunteer">

    {{-- Header --}}
    <section class="bg-kidical-blue text-white">
        <div class="container mx-auto px-4 py-12 md:py-14 max-w-5xl">
            <p class="text-base font-bold uppercase tracking-[0.12em] text-white/70 mb-3">Backstage · {{ $group->name }}</p>
            <h1 class="text-white -rotate-1 origin-left mb-4">Alles op één plek</h1>
            <p class="text-xl text-white/85 max-w-2xl">
                Het materiaal voor de roze hesjes van {{ $group->name }}. Geen zoektocht meer in oude
                berichten. Hier staat het, en hier blijft het staan.
            </p>
            <p class="mt-5">
                <a href="{{ route('backstage.welcome', $group) }}" class="inline-flex items-center gap-2 text-white font-bold bg-white/15 hover:bg-white/25 rounded-full px-4 py-2 no-underline bg-none">
                    <flux:icon name="sparkles" variant="solid" class="size-5" aria-hidden="true" />
                    Net begonnen? Lees je welkomstgids
                </a>
            </p>
        </div>
    </section>

    {{-- Material library --}}
    <section class="bg-white">
        <div class="container mx-auto px-4 py-14 md:py-16 max-w-5xl">
            <h2 class="mb-8">Materiaal</h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $materials = [
                        ['document-text', 'red', 'Afsprakencharter', 'Onze afspraken voor organisatoren en hesjes.', 'PDF', '#'],
                        ['map', 'blue', 'Zo organiseer je een rit', 'Route, gemeentecontact en promo, stap voor stap.', 'Gids', '#'],
                        ['play-circle', 'orange', 'Veilig begeleiden', 'Korte video over meefietsen als roze hesje.', 'Video', 'https://www.youtube.com/watch?v=i9YQxJ-ChNM'],
                        ['arrow-down-tray', 'green', 'Posters & promo', 'Affiches en flyers om in je buurt op te hangen.', 'Download', '#'],
                        ['megaphone', 'violet', 'De startspeech', 'Het woordje voor de start, klaar om voor te lezen.', 'Tekst', route('backstage.welcome', $group)],
                        ['users', 'coral', 'Jouw team', 'Wie rijdt er mee in '.$group->name.'?', 'Roster', route('backstage.team', $group)],
                    ];
                @endphp

                @foreach ($materials as [$icon, $color, $title, $desc, $tag, $href])
                    @php
                        $chip = match ($color) {
                            'blue' => 'bg-kidical-blue', 'orange' => 'bg-kidical-orange',
                            'green' => 'bg-kidical-green', 'violet' => 'bg-kidical-violet',
                            'coral' => 'bg-kidical-coral', default => 'bg-kidical-red',
                        };
                        $external = \Illuminate\Support\Str::startsWith($href, 'http');
                    @endphp
                    <a href="{{ $href }}" @if ($external) target="_blank" rel="noopener" @endif
                       class="group flex flex-col gap-4 bg-white rounded-card p-7 shadow-card no-underline bg-none transition hover:-translate-y-1 hover:shadow-lg ring-1 ring-kidical-ink/5">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center justify-center shrink-0 size-14 -rotate-3 rounded-chip {{ $chip }}">
                                <flux:icon name="{{ $icon }}" variant="solid" class="size-7 text-white" aria-hidden="true" />
                            </span>
                            <span class="text-xs font-bold uppercase tracking-wide text-kidical-ink/45">{{ $tag }}</span>
                        </div>
                        <div>
                            <strong class="block font-heading text-xl font-normal text-kidical-ink leading-tight mb-1">{{ $title }}</strong>
                            <p class="text-kidical-ink/70 leading-snug">{{ $desc }}</p>
                        </div>
                        <span class="mt-auto inline-flex items-center gap-1 font-bold text-kidical-blue">
                            Openen
                            <flux:icon name="arrow-right" variant="micro" class="size-4 transition group-hover:translate-x-0.5" aria-hidden="true" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Wat komt eraan + team preview --}}
    <section class="bg-kidical-light-yellow">
        <div class="container mx-auto px-4 py-14 md:py-16 max-w-5xl grid gap-10 md:grid-cols-5">

            <div class="md:col-span-3">
                <h2 class="mb-6">Wat komt eraan</h2>
                <ul role="list" class="space-y-3">
                    @forelse ($upcoming as $activity)
                        <li class="flex items-center gap-4 bg-white rounded-2xl p-5 shadow-card">
                            <span class="flex items-center justify-center shrink-0 size-12 rounded-full {{ $activity->activity_type->value === 'kidicalmass' ? 'bg-kidical-green/15' : 'bg-kidical-blue/15' }}">
                                <flux:icon name="{{ $activity->activity_type->value === 'kidicalmass' ? 'flag' : 'calendar-days' }}" variant="solid" class="size-6 {{ $activity->activity_type->value === 'kidicalmass' ? 'text-kidical-green' : 'text-kidical-blue' }}" aria-hidden="true" />
                            </span>
                            <div>
                                <strong class="block text-lg text-kidical-ink leading-tight">{{ $activity->title_nl }}</strong>
                                <span class="text-kidical-ink/65">
                                    <time datetime="{{ $activity->begin_date->toIso8601String() }}">{{ ucfirst($activity->begin_date->translatedFormat('l j F')) }} · {{ $activity->begin_date->format('H\u i') }}</time>
                                </span>
                            </div>
                        </li>
                    @empty
                        <li class="text-kidical-ink/60">Binnenkort plannen we de volgende rit.</li>
                    @endforelse
                </ul>
            </div>

            <div class="md:col-span-2">
                <h2 class="mb-6">Jouw team</h2>
                <div class="bg-white rounded-card p-7 shadow-card">
                    <div class="flex -space-x-3 mb-4">
                        @foreach ($roster->take(6) as $member)
                            <span class="flex items-center justify-center size-11 rounded-full bg-kidical-red text-white font-bold text-sm ring-2 ring-white" aria-hidden="true">{{ $member->initials() }}</span>
                        @endforeach
                    </div>
                    <p class="text-kidical-ink/75 mb-5">{{ $roster->count() }} roze hesjes rijden mee in {{ $group->name }}.</p>
                    <a href="{{ route('backstage.team', $group) }}" class="font-bold text-kidical-blue">Bekijk je team →</a>
                </div>
            </div>

        </div>
    </section>

</x-layouts::backstage>
