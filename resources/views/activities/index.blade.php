<x-layouts::site title="Activities">
    <div class="mx-auto max-w-3xl space-y-8">
        <header class="space-y-2">
            <h1>Activities</h1>
            <p class="text-xl text-kidical-ink/70">Find a ride near you</p>
        </header>

        {{-- PAT-12 · filter bar (wiring comes later) --}}
        <x-wire.placeholder
            label="Filter"
            note="Upcoming / past toggle + location filter"
            class="min-h-16"
        />

        @if ($activities->isNotEmpty())
            @php
                $grouped = $activities->getCollection()->groupBy(fn ($activity) => $activity->begin_date->format('Y-m-d'));
            @endphp

            <div class="space-y-8">
                @foreach ($grouped as $date => $dayActivities)
                    <section class="space-y-4">
                        <h2 class="text-2xl text-kidical-ink">{{ \Illuminate\Support\Carbon::parse($date)->format('l j F Y') }}</h2>
                        <div class="grid gap-5 sm:grid-cols-2">
                            @foreach ($dayActivities as $activity)
                                <x-event-card :activity="$activity" />
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <div>{{ $activities->links() }}</div>
        @else
            <p class="text-kidical-ink/70">
                No upcoming rides right now. The season runs from March to November — check back soon!
            </p>
        @endif
    </div>
</x-layouts::site>
