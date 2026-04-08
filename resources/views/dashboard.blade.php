<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-6">
        <flux:card>
            <flux:heading size="lg">Upcoming activities</flux:heading>
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($upcomingActivities as $activity)
                    <flux:card>
                        <flux:heading>{{ $activity->title_nl }}</flux:heading>
                        <flux:text class="mt-1">{{ $activity->begin_date->format('d/m/Y H:i') }}</flux:text>
                        <flux:text>{{ $activity->location }}</flux:text>
                        <flux:button class="mt-4" variant="primary" size="sm" :href="route('activities.show', $activity)">
                            View activity
                        </flux:button>
                    </flux:card>
                @empty
                    <flux:text>No upcoming activities found for your groups.</flux:text>
                @endforelse
            </div>
        </flux:card>

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

        <flux:card>
            <flux:heading size="lg">Contact details</flux:heading>
            <div class="mt-4">
                @livewire('contact-details-table', ['groups' => $dashboardGroups], key('dashboard-contact-details'))
            </div>
        </flux:card>
    </div>
</x-layouts::app>
