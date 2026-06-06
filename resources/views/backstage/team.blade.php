{{--
    Backstage — volunteer roster (P3, D-1 Decision C). Logged-in only. Answers Morgane's
    strongest unmet need: "I don't know who's active in the group." Each volunteer can opt in
    to show themselves on the public chapter page (group_user.is_public); the full roster here
    is visible to fellow volunteers regardless.
--}}
<x-layouts::backstage title="Mijn team — {{ $group->name }}" :group="$group" :volunteer="$volunteer">

    {{-- Header --}}
    <section class="bg-kidical-red text-white">
        <div class="container mx-auto px-4 py-12 md:py-14 max-w-4xl">
            <p class="text-base font-bold uppercase tracking-[0.12em] text-white/75 mb-3">Jouw team</p>
            <h1 class="text-white -rotate-1 origin-left mb-4">De roze hesjes van {{ $group->name }}</h1>
            <p class="text-xl text-white/90 max-w-2xl">
                Dit is je team. Zo weet je wie er mee rijdt, en hoef je niet te wachten tot je iemand
                toevallig aan de start tegenkomt.
            </p>
        </div>
    </section>

    <section class="bg-white">
        <div class="container mx-auto px-4 py-14 md:py-16 max-w-3xl">

            {{-- Your own card + opt-in toggle --}}
            @if ($volunteer)
                <div x-data="{ public: {{ $volunteer->pivot?->is_public ? 'true' : 'false' }} }"
                     class="flex flex-col sm:flex-row sm:items-center gap-5 bg-kidical-light-blue/40 rounded-card p-6 shadow-card mb-10 ring-1 ring-kidical-blue/15">
                    <span class="flex items-center justify-center shrink-0 size-16 rounded-full bg-kidical-red text-white font-heading text-2xl" aria-hidden="true">{{ $volunteer->initials() }}</span>
                    <div class="flex-1">
                        <strong class="block font-heading text-2xl font-normal text-kidical-ink leading-tight">{{ $volunteer->name }} <span class="text-kidical-ink/45 text-lg">(jij)</span></strong>
                        <p class="text-kidical-ink/70">Roze hesje</p>
                    </div>
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <span class="text-sm font-bold text-kidical-ink/75 text-right leading-tight">Toon mij op de<br>publieke groepspagina</span>
                        <button type="button" role="switch" x-on:click="public = !public" x-bind:aria-checked="public"
                                class="relative w-14 h-8 rounded-full transition shrink-0"
                                x-bind:class="public ? 'bg-kidical-green' : 'bg-kidical-ink/20'">
                            <span class="absolute top-1 left-1 size-6 rounded-full bg-white transition" x-bind:class="public ? 'translate-x-6' : ''"></span>
                        </button>
                    </label>
                </div>
            @endif

            {{-- The rest of the team --}}
            <h2 class="text-2xl mb-5">Wie rijdt er mee ({{ $roster->count() }})</h2>
            <ul role="list" class="grid gap-3 sm:grid-cols-2">
                @foreach ($roster as $member)
                    @continue($volunteer && $member->id === $volunteer->id)
                    <li class="flex items-center gap-4 bg-white rounded-2xl p-4 shadow-card ring-1 ring-kidical-ink/5">
                        <span class="flex items-center justify-center shrink-0 size-12 rounded-full bg-kidical-blue text-white font-bold" aria-hidden="true">{{ $member->initials() }}</span>
                        <div class="min-w-0">
                            <strong class="block text-lg text-kidical-ink leading-tight truncate">{{ $member->name }}</strong>
                            <span class="text-kidical-ink/60 text-sm">{{ $lead && $member->id === $lead->id ? 'Coördinator' : 'Roze hesje' }}</span>
                        </div>
                        @if ($member->pivot?->is_public)
                            <span class="ml-auto inline-flex items-center gap-1 text-xs font-bold text-kidical-green shrink-0" title="Zichtbaar op de publieke groepspagina">
                                <flux:icon name="globe-alt" variant="micro" class="size-4" aria-hidden="true" />
                                Publiek
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <p class="text-kidical-ink/55 text-sm mt-8">
                Alleen ingelogde vrijwilligers zien deze lijst. Wie zelf kiest om publiek te staan,
                verschijnt ook op de openbare groepspagina.
            </p>
        </div>
    </section>

</x-layouts::backstage>
