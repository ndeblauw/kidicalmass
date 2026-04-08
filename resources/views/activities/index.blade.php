<x-layouts::site title="Activities">
    <div class="space-y-8">
        <div class="text-center mb-12">
            <h1>
                <x-bike-icon class="w-10 h-10 inline-block mr-2" />
                Activities
            </h1>
            <flux:text>Join us for fun family-friendly cycling events</flux:text>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($activities as $activity)
                <article class="overflow-hidden">
                    @if($activity->getFirstMedia('main'))
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="{{ $activity->getFirstMediaUrl('main', 'card') }}"
                                 alt="{{ $activity->title_nl }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="p-6">
                        <h3 class="mb-3">
                            <a href="{{ route('activities.show', $activity) }}">{{ $activity->title_nl }}</a>
                        </h3>
                        <div class="mb-3">
                            <flux:badge>{{ $activity->activity_type->label() }}</flux:badge>
                        </div>
                        <dl class="space-y-2 mb-4">
                            <div class="flex items-center gap-2">
                                <flux:icon.calendar-days class="shrink-0" aria-hidden="true" />
                                <div>
                                    <dt class="sr-only">Date</dt>
                                    <dd><time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ $activity->begin_date->format('M d, Y \a\t H:i') }}</time></dd>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:icon.map-pin class="shrink-0" aria-hidden="true" />
                                <div>
                                    <dt class="sr-only">Location</dt>
                                    <dd>{{ $activity->location }}</dd>
                                </div>
                            </div>
                        </dl>
                        @if($activity->groups->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($activity->groups as $group)
                                    <flux:badge href="{{ route('groups.show', $group) }}">{{ $group->name }}</flux:badge>
                                @endforeach
                            </div>
                        @endif
                        <flux:text class="mb-4">{{ Str::limit(strip_tags($activity->content_nl), 150) }}</flux:text>
                        <flux:button href="{{ route('activities.show', $activity) }}" variant="primary">View Details</flux:button>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $activities->links() }}
        </div>
    </div>
</x-layouts::site>
