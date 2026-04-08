<x-layouts::app :title="$group->name">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ $group->name }}</flux:heading>
            <flux:button variant="primary" :href="route('home.groups.edit', $group)" wire:navigate>
                Edit group
            </flux:button>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <flux:card>
                <flux:heading size="lg">Members</flux:heading>
                <div class="mt-4">
                    @livewire('contact-details-table', ['group' => $group], key('group-contact-details-'.$group->id))
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg">Next upcoming activity</flux:heading>
                <div class="mt-4">
                    @if ($upcomingActivity)
                        <flux:heading>{{ $upcomingActivity->title_nl }}</flux:heading>
                        <flux:text class="mt-2">{{ $upcomingActivity->begin_date->format('d/m/Y H:i') }}</flux:text>
                        <flux:text>{{ $upcomingActivity->location }}</flux:text>
                        <flux:text class="mt-2">{{ Str::limit(strip_tags($upcomingActivity->content_nl), 200) }}</flux:text>
                        <flux:button class="mt-4" variant="primary" size="sm" :href="route('activities.show', $upcomingActivity)">
                            Open activity
                        </flux:button>
                    @else
                        <flux:text>No upcoming activities for this group.</flux:text>
                    @endif
                </div>
            </flux:card>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <flux:card>
                <flux:heading size="lg">Upcoming activities</flux:heading>
                <div class="mt-4 space-y-2">
                    @forelse ($upcomingActivities as $activity)
                        <flux:button variant="ghost" class="w-full justify-between" :href="route('activities.show', $activity)">
                            <span>{{ $activity->title_nl }}</span>
                            <span class="text-zinc-500">{{ $activity->begin_date->format('d/m/Y') }}</span>
                        </flux:button>
                    @empty
                        <flux:text>No upcoming activities found.</flux:text>
                    @endforelse
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg">Past activities</flux:heading>
                <div class="mt-4 space-y-2">
                    @forelse ($pastActivities as $activity)
                        <flux:button variant="ghost" class="w-full justify-between" :href="route('activities.show', $activity)">
                            <span>{{ $activity->title_nl }}</span>
                            <span class="text-zinc-500">{{ $activity->begin_date->format('d/m/Y') }}</span>
                        </flux:button>
                    @empty
                        <flux:text>No past activities found.</flux:text>
                    @endforelse
                </div>
            </flux:card>
        </div>

        <flux:card>
            <flux:heading size="lg">Latest news</flux:heading>
            <div class="mt-4 space-y-2">
                @forelse ($newsItems as $article)
                    <flux:button variant="ghost" class="w-full justify-between" :href="route('articles.show', $article)">
                        <span>{{ $article->title_nl }}</span>
                        <span class="text-zinc-500">{{ $article->created_at->format('d/m/Y') }}</span>
                    </flux:button>
                @empty
                    <flux:text>No news found for this group.</flux:text>
                @endforelse
            </div>
        </flux:card>
    </div>
</x-layouts::app>
