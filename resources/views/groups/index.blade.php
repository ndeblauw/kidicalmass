<x-layouts::site title="Groups">
    <div class="mx-auto max-w-5xl space-y-8">
        <header class="space-y-2">
            <h1>Groups</h1>
            <p class="text-xl text-kidical-ink/70">
                {{ $groups->count() }} active {{ Str::plural('group', $groups->count()) }} across Belgium
            </p>
        </header>

        {{-- PAT-8 · chapter map (wiring comes later) --}}
        <x-wire.placeholder
            label="Chapter map"
            note="Belgium map with chapter pins"
            class="min-h-72"
        />

        @if ($groups->isNotEmpty())
            <ul class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($groups as $group)
                    <li>
                        <a
                            href="{{ route('groups.show', $group) }}"
                            class="link-plain group block h-full rounded-xl border border-kidical-ink/10 bg-white p-5 shadow-sm transition-shadow hover:shadow-md"
                        >
                            <h3 class="text-lg text-kidical-blue group-hover:text-kidical-orange transition-colors">{{ $group->name }}</h3>
                            @if ($group->zip)
                                <p class="mt-1 text-sm text-kidical-ink/60">{{ $group->zip }}</p>
                            @endif
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="rounded-full bg-kidical-light-yellow px-3 py-1 text-xs font-semibold text-kidical-ink">{{ $group->activities_count }} activities</span>
                                <span class="rounded-full bg-kidical-light-blue/60 px-3 py-1 text-xs font-semibold text-kidical-ink">{{ $group->articles_count }} articles</span>
                            </div>
                            @if ($group->parent)
                                <p class="mt-3 text-xs text-kidical-ink/50">Part of: {{ $group->parent->name }}</p>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-kidical-ink/70">No groups yet.</p>
        @endif

        {{-- "Start a chapter" CTA --}}
        <x-wire.placeholder
            label="Start a chapter"
            note="“Don't see your city?” — start one with our support"
            class="min-h-28"
        />
    </div>
</x-layouts::site>
