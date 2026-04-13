<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-6 pb-40">
        <div class="flex items-center justify-between">
            <flux:heading size="lg">Dashboard</flux:heading>
            <div class="flex gap-2">
                <flux:button variant="primary" :href="route('home.activities.create')" wire:navigate>
                    Create activity
                </flux:button>
                <flux:button variant="primary" :href="route('home.articles.create')" wire:navigate>
                    Create article
                </flux:button>
            </div>
        </div>

        @php
            $nextActivity = $upcomingActivities->first();
            $remainingActivities = $upcomingActivities->skip(1);
        @endphp

        @if($nextActivity)
            <flux:card class="bg-primary-50 dark:bg-primary-900/20 border-primary-200 dark:border-primary-800">
                <flux:heading size="xl">Next: {{ $nextActivity->title_nl }}</flux:heading>
                <flux:text class="mt-2 text-lg">{{ $nextActivity->begin_date->format('d/m/Y H:i') }}</flux:text>
                <flux:text class="text-base">{{ $nextActivity->location }}</flux:text>
                <div class="mt-4 flex gap-2">
                    <flux:button size="sm" :href="route('activities.show', $nextActivity)">
                        View
                    </flux:button>
                    <flux:button size="sm" variant="primary" :href="route('home.activities.edit', $nextActivity)" wire:navigate>
                        Edit
                    </flux:button>
                </div>
            </flux:card>
        @endif

        @if($remainingActivities->isNotEmpty())
            <flux:card class="bg-zinc-50">
                <flux:heading size="lg">Upcoming activities</flux:heading>
                <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($remainingActivities as $activity)
                        <flux:card >
                            <flux:heading>{{ $activity->title_nl }}</flux:heading>
                            <flux:text class="mt-1">{{ $activity->begin_date->format('d/m/Y H:i') }}</flux:text>
                            <flux:text>{{ $activity->location }}</flux:text>
                            <div class="mt-4 flex gap-2">
                                <flux:button size="sm" :href="route('activities.show', $activity)">
                                    View
                                </flux:button>
                                <flux:button size="sm" variant="primary" :href="route('home.activities.edit', $activity)" wire:navigate>
                                    Edit
                                </flux:button>
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            </flux:card>
        @endif

        @if($pastActivities->isNotEmpty())
            <flux:card>
                <flux:heading size="lg">Recent past activities</flux:heading>
                <div class="mt-4 space-y-2">
                    @forelse ($pastActivities as $activity)
                        <flux:button variant="ghost" class="w-full justify-between" :href="route('activities.show', $activity)">
                            <span>{{ $activity->title_nl }}</span>
                            <span class="text-zinc-500">{{ $activity->begin_date->format('d/m/Y') }}</span>
                        </flux:button>
                    @empty
                        <flux:text>No recent past activities found.</flux:text>
                    @endforelse
                </div>
            </flux:card>
        @endif

        <flux:card class="bg-zinc-50">
            <flux:heading size="lg">Your contacts</flux:heading>
            <div class="mt-4 bg-white rounded-lg p-4 border border-zinc-200">
                @livewire('contact-details-table', ['groups' => auth()->user()->groups], key('dashboard-contact-details'))
            </div>
        </flux:card>

        @if($latestNews->isNotEmpty())
            <flux:card class="bg-zinc-50">
                <flux:heading size="lg">Latest news</flux:heading>
                <div class="mt-4 space-y-3">
                    @foreach($latestNews as $news)
                        <flux:button variant="ghost" class="w-full justify-between" :href="route('articles.show', $news)">
                            <span>{{ $news->title_nl }}</span>
                            <span class="text-zinc-500">{{ $news->created_at->format('d/m/Y') }}</span>
                        </flux:button>
                    @endforeach
                </div>
            </flux:card>
        @endif
    </div>

</x-layouts::app>
