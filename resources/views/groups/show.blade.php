<x-layouts::site title="{{ $group->name }}">
    <div class="mx-auto max-w-4xl space-y-12">
        <a href="{{ route('groups.index') }}" class="inline-block font-semibold text-kidical-blue hover:underline">← All groups</a>

        <header class="space-y-3">
            <h1>{{ $group->name }}</h1>
            <div class="flex flex-wrap items-center gap-2">
                @if ($group->zip)
                    <span class="rounded-full bg-kidical-light-blue/60 px-3 py-1 text-sm font-semibold text-kidical-ink">{{ $group->zip }}</span>
                @endif
                <span class="rounded-full bg-kidical-light-yellow px-3 py-1 text-sm font-semibold text-kidical-ink">{{ $group->activities_count }} activities</span>
                <span class="rounded-full bg-kidical-light-yellow px-3 py-1 text-sm font-semibold text-kidical-ink">{{ $group->articles_count }} articles</span>
            </div>
            @if ($group->parent)
                <p class="text-sm text-kidical-ink/60">
                    Part of: <a href="{{ route('groups.show', $group->parent) }}" class="font-semibold text-kidical-blue hover:underline">{{ $group->parent->name }}</a>
                </p>
            @endif
        </header>

        @if ($group->children->isNotEmpty())
            <section class="space-y-4">
                <h2 class="text-2xl text-kidical-ink">Subgroups</h2>
                <ul class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($group->children as $child)
                        <li>
                            <a
                                href="{{ route('groups.show', $child) }}"
                                class="link-plain group block h-full rounded-xl border border-kidical-ink/10 bg-white p-5 shadow-sm transition-shadow hover:shadow-md"
                            >
                                <h3 class="text-lg text-kidical-blue group-hover:text-kidical-orange transition-colors">{{ $child->name }}</h3>
                                @if ($child->zip)
                                    <p class="mt-1 text-sm text-kidical-ink/60">{{ $child->zip }}</p>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="space-y-4">
            <h2 class="text-2xl text-kidical-ink">Upcoming rides</h2>
            @if ($activities->isNotEmpty())
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($activities as $activity)
                        <x-event-card :activity="$activity" />
                    @endforeach
                </div>
            @else
                <p class="text-kidical-ink/70">No upcoming rides for {{ $group->name }} right now.</p>
            @endif
        </section>

        {{-- Team + volunteer form (PAT-6 / PAT-11) --}}
        <section class="space-y-4">
            <h2 class="text-2xl text-kidical-ink">Organised by</h2>
            @if ($group->users->isNotEmpty())
                <ul class="flex flex-wrap gap-3">
                    @foreach ($group->users as $member)
                        <li class="rounded-xl border border-kidical-ink/10 bg-white px-5 py-3 shadow-sm">
                            <span class="block font-bold text-kidical-ink">{{ $member->name }}</span>
                            <span class="block text-xs font-semibold uppercase tracking-wide text-kidical-ink/50">Organiser</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-kidical-ink/70">No team listed yet.</p>
            @endif

            <x-wire.placeholder
                label="Volunteer sign-up"
                note="Routed to this chapter's lead (PAT-6)"
                class="min-h-28"
            />
        </section>

        @if ($articles->isNotEmpty())
            <section class="space-y-4">
                <h2 class="text-2xl text-kidical-ink">News</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($articles as $article)
                        <x-article-card :article="$article" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Optional chapter-managed sections (PAT-11 · hide-if-empty) --}}
        <section class="space-y-4">
            <h2 class="text-2xl text-kidical-ink">More</h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <x-wire.placeholder label="Local partners" note="hide-if-empty" class="min-h-24" />
                <x-wire.placeholder label="Press coverage" note="hide-if-empty" class="min-h-24" />
                <x-wire.placeholder label="Downloads" note="hide-if-empty" class="min-h-24" />
            </div>
        </section>
    </div>
</x-layouts::site>
