<x-layouts::site title="Activities">
    <div class="space-y-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-kidical-blue mb-4">
                <x-bike-icon class="w-10 h-10 inline-block mr-2" />
                Activities
            </h1>
            <p class="text-lg text-gray-700">Join us for fun family-friendly cycling events</p>
        </div>
        
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($activities as $activity)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow border-l-4 border-kidical-green">
                    @if($activity->getFirstMedia('main'))
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="{{ $activity->getFirstMediaUrl('main', 'card') }}" 
                                 alt="{{ $activity->title_nl }}" 
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                    @endif
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-kidical-blue mb-3 hover:text-kidical-orange transition-colors">
                            <a href="{{ route('activities.show', $activity) }}">{{ $activity->title_nl }}</a>
                        </h3>
                        <div class="mb-3">
                            <span class="px-2 py-1 {{ $activity->activity_type->badgeClasses() }} text-xs font-semibold rounded-full">
                                {{ $activity->activity_type->label() }}
                            </span>
                        </div>
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-700">
                                <svg class="w-4 h-4 mr-2 text-kidical-green flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-semibold">{{ $activity->begin_date->format('M d, Y \a\t H:i') }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-700">
                                <svg class="w-4 h-4 mr-2 text-kidical-green flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $activity->location }}</span>
                            </div>
                        </div>
                        @if($activity->groups->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($activity->groups as $group)
                                    <span class="px-2 py-1 bg-kidical-light-yellow text-kidical-blue text-xs font-semibold rounded-full">
                                        {{ $group->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        <p class="text-gray-700 mb-4">{{ Str::limit(strip_tags($activity->content_nl), 150) }}</p>
                        <a href="{{ route('activities.show', $activity) }}" class="inline-block px-4 py-2 bg-kidical-orange text-white rounded-lg hover:bg-kidical-blue transition-colors font-semibold text-sm">
                            View Details →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $activities->links() }}
        </div>
    </div>
</x-layouts::site>