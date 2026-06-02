<x-layouts::site title="Kidical Mass Belgium">
    <div class="space-y-20">
        {{-- Hero · dual CTA --}}
        <section class="home-hero px-4 py-16 text-center md:py-24">
            <div class="mx-auto max-w-3xl space-y-6">
                <h1>Kids on bikes. Together.</h1>
                <p class="mx-auto max-w-2xl text-xl text-white/90">
                    Every month, hundreds of children ride through Belgian streets — safely, together, with music. Free for everyone.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-3 pt-2">
                    <a href="{{ route('activities.index') }}" class="link-plain inline-flex items-center rounded-full bg-kidical-yellow px-6 py-3 font-bold text-kidical-ink shadow-sm transition-colors hover:bg-white">
                        Find a ride →
                    </a>
                    <a href="{{ route('getting-started') }}" class="font-bold text-white hover:underline">New here? Start here →</a>
                </div>
            </div>
        </section>

        {{-- Upcoming rides (next 3) --}}
        <section id="upcoming" class="space-y-6 scroll-mt-24">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-kidical-ink">Upcoming rides</h2>
                <a href="{{ route('activities.index') }}" class="shrink-0 font-bold text-kidical-blue hover:underline">See all →</a>
            </div>
            @if ($upcomingActivities->isNotEmpty())
                <div class="grid gap-5 md:grid-cols-3">
                    @foreach ($upcomingActivities->take(3) as $activity)
                        <x-event-card :activity="$activity" />
                    @endforeach
                </div>
            @else
                <p class="text-kidical-ink/70">No rides right now — the season runs from March to November.</p>
            @endif
        </section>

        {{-- Chapter reach --}}
        <section class="space-y-6">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-kidical-ink">Active across Belgium</h2>
                <a href="{{ route('groups.index') }}" class="shrink-0 font-bold text-kidical-blue hover:underline">See all groups →</a>
            </div>
            <x-wire.placeholder label="Chapter map" note="National reach — map wiring comes later" class="min-h-64" />
            @if ($groups->isNotEmpty())
                <ul class="flex flex-wrap gap-2">
                    @foreach ($groups as $group)
                        <li>
                            <a href="{{ route('groups.show', $group) }}" class="link-plain inline-block rounded-full border border-kidical-ink/15 bg-white px-4 py-1.5 text-sm font-semibold text-kidical-blue transition-colors hover:border-kidical-blue">
                                {{ $group->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Stats bar (PAT-4 · dynamic) --}}
        <section class="grid gap-5 sm:grid-cols-2">
            <div class="rounded-xl bg-kidical-light-blue/50 p-8 text-center">
                <div class="font-heading text-5xl font-extrabold text-kidical-blue">{{ $groups->count() }}</div>
                <div class="mt-1 font-semibold text-kidical-ink/70">active {{ Str::plural('group', $groups->count()) }}</div>
            </div>
            <div class="rounded-xl bg-kidical-light-yellow p-8 text-center">
                <div class="font-heading text-5xl font-extrabold text-kidical-orange">{{ $upcomingActivities->count() }}</div>
                <div class="mt-1 font-semibold text-kidical-ink/70">upcoming {{ Str::plural('ride', $upcomingActivities->count()) }}</div>
            </div>
        </section>

        {{-- Volunteer nudge --}}
        <section class="rounded-xl border border-kidical-ink/10 bg-white p-6 text-center shadow-sm">
            <p class="text-lg">
                Want to help make rides happen?
                <a href="{{ route('volunteer') }}" class="font-bold text-kidical-blue hover:underline">Help out →</a>
            </p>
        </section>

        {{-- News preview (latest 2) · hidden when empty --}}
        @if ($latestArticles->isNotEmpty())
            <section class="space-y-6">
                <div class="flex items-baseline justify-between gap-4">
                    <h2 class="text-kidical-ink">News</h2>
                    <a href="{{ route('articles.index') }}" class="shrink-0 font-bold text-kidical-blue hover:underline">See all →</a>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    @foreach ($latestArticles->take(2) as $article)
                        <x-article-card :article="$article" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Support (PAT-10 contextual block) --}}
        <x-support-callout variant="home" />
    </div>
</x-layouts::site>
