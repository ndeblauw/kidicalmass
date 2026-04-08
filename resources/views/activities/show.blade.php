<x-layouts::site title="{{ $activity->title_nl }}">
    <div>
        <!-- Back Button -->
        <div class="mb-6">
            <flux:button href="{{ route('activities.index') }}" variant="ghost" icon="arrow-left">
                Back to Activities
            </flux:button>
        </div>

        <!-- Activity Card -->
        <article class="overflow-hidden">
            {{-- @if($activity->getFirstMedia('main'))
                <div class="aspect-[16/9] overflow-hidden">
                    <img src="{{ $activity->getFirstMediaUrl('main') }}"
                         alt="{{ $activity->title_nl }}"
                         class="w-full h-full object-cover">
                </div>
            @endif --}}

            <div class="p-8">
                <!-- Activity Type Badge -->
                <div class="mb-3">
                    <flux:badge color="yellow" variant="solid">{{ $activity->activity_type->label() }}</flux:badge>
                </div>

                <!-- Title -->
                <h1 class="mb-6">{{ $activity->title_nl }}</h1>

                <!-- Activity Metadata -->
                <dl class="activity-meta grid md:grid-cols-2 mb-6">
                    <div class="flex items-center gap-3">
                        <flux:icon.calendar-days class="shrink-0" aria-hidden="true" />
                        <div>
                            <dt><flux:text size="sm">Date &amp; Time</flux:text></dt>
                            <dd>
                                <flux:text>
                                    <time datetime="{{ $activity->begin_date->toIso8601String() }}">
                                        {{ $activity->begin_date->format('F d, Y \a\t H:i') }}
                                    </time>
                                </flux:text>
                            </dd>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <flux:icon.map-pin class="shrink-0" aria-hidden="true" />
                        <div>
                            <dt><flux:text size="sm">Location</flux:text></dt>
                            <dd><flux:text>{{ $activity->location }}</flux:text></dd>
                        </div>
                    </div>

                    @if($activity->end_date && $activity->end_date->ne($activity->begin_date))
                        <div class="flex items-center gap-3">
                            <flux:icon.clock class="shrink-0" aria-hidden="true" />
                            <div>
                                <dt><flux:text size="sm">End Time</flux:text></dt>
                                <dd>
                                    <flux:text>
                                        <time datetime="{{ $activity->end_date->toIso8601String() }}">
                                            {{ $activity->end_date->format('F d, Y \a\t H:i') }}
                                        </time>
                                    </flux:text>
                                </dd>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-3">
                        <flux:icon.user class="shrink-0" aria-hidden="true" />
                        <div>
                            <dt><flux:text size="sm">Organizer</flux:text></dt>
                            <dd><flux:text>{{ $activity->author->name }}</flux:text></dd>
                        </div>
                    </div>
                </dl>

                @if($activity->groups->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach($activity->groups as $group)
                            <flux:badge href="{{ route('groups.show', $group) }}">{{ $group->name }}</flux:badge>
                        @endforeach
                    </div>
                @endif
            </div>

            <flux:separator />

            <!-- Activity Content -->
            <div class="p-8">
                <flux:text>
                    {!! nl2br(e($activity->content_nl)) !!}
                </flux:text>

                <!-- Gallery -->
                @if($activity->getMedia('gallery')->count() > 0)
                    <div class="mt-8">
                        <h2 class="mb-4">Gallery</h2>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($activity->getMedia('gallery') as $media)
                                <div class="aspect-[4/3] overflow-hidden rounded-lg">
                                    <img src="{{ $media->getUrl() }}"
                                         alt=""
                                         class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </article>
    </div>
</x-layouts::site>
