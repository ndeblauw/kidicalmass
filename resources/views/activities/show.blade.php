<x-layouts::site title="{{ $activity->title_nl }}">
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('activities.index') }}" class="inline-flex items-center text-kidical-blue hover:text-kidical-orange transition-colors font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Activities
            </a>
        </div>

        <!-- Activity Card -->
        <article class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($activity->getFirstMedia('main'))
                <div class="aspect-[16/9] overflow-hidden">
                    <img src="{{ $activity->getFirstMediaUrl('main') }}" 
                         alt="{{ $activity->title_nl }}" 
                         class="w-full h-full object-cover">
                </div>
            @endif
            <div class="bg-gradient-to-r from-kidical-green to-kidical-blue p-8">
                <div class="mb-3">
                    <span class="px-3 py-1 {{ $activity->activity_type->badgeClasses() }} text-sm font-semibold rounded-full">
                        {{ $activity->activity_type->label() }}
                    </span>
                </div>
                <h1 class="text-4xl font-bold text-white mb-6">
                    <x-bike-icon class="w-10 h-10 inline-block mr-2" />
                    {{ $activity->title_nl }}
                </h1>
                
                <!-- Activity Details -->
                <div class="grid md:grid-cols-2 gap-4 bg-white/20 backdrop-blur-sm rounded-lg p-4">
                    <div class="flex items-center space-x-3 text-white">
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <div class="text-sm font-semibold opacity-90">Date & Time</div>
                            <div class="font-bold">{{ $activity->begin_date->format('F d, Y \a\t H:i') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 text-white">
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <div class="text-sm font-semibold opacity-90">Location</div>
                            <div class="font-bold">{{ $activity->location }}</div>
                        </div>
                    </div>
                    @if($activity->end_date && $activity->end_date->ne($activity->begin_date))
                        <div class="flex items-center space-x-3 text-white">
                            <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <div class="text-sm font-semibold opacity-90">End Time</div>
                                <div class="font-bold">{{ $activity->end_date->format('F d, Y \a\t H:i') }}</div>
                            </div>
                        </div>
                    @endif
                    <div class="flex items-center space-x-3 text-white">
                        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                        </svg>
                        <div>
                            <div class="text-sm font-semibold opacity-90">Organizer</div>
                            <div class="font-bold">{{ $activity->author->name }}</div>
                        </div>
                    </div>
                </div>

                @if($activity->groups->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach($activity->groups as $group)
                            <a href="{{ route('groups.show', $group) }}" class="px-3 py-1 bg-white text-kidical-blue rounded-full text-sm font-semibold hover:bg-kidical-yellow transition-colors">
                                {{ $group->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Activity Content -->
            <div class="p-8">
                <div class="prose prose-lg max-w-none text-gray-800">
                    {!! nl2br(e($activity->content_nl)) !!}
                </div>

                <!-- Gallery Images -->
                @if($activity->getMedia('gallery')->count() > 0)
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-kidical-blue mb-4">Gallery</h2>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($activity->getMedia('gallery') as $media)
                                <div class="aspect-[4/3] overflow-hidden rounded-lg">
                                    <img src="{{ $media->getUrl() }}" 
                                         alt="{{ $activity->title_nl }}" 
                                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </article>
    </div>
</x-layouts::site>