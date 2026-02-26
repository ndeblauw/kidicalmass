<x-layouts::site title="Press">
    <div class="space-y-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-kidical-blue mb-4">Press</h1>
            <p class="text-lg text-gray-700">Media coverage and press appearances of Kidical Mass Belgium</p>
        </div>

        <!-- Filter by media type -->
        <div class="flex flex-wrap gap-2 justify-center mb-8">
            <a href="{{ route('press.index') }}"
               class="px-4 py-2 rounded-full text-sm font-semibold transition-colors {{ !request('type') ? 'bg-kidical-blue text-white' : 'bg-gray-100 text-gray-700 hover:bg-kidical-light-yellow' }}">
                All
            </a>
            @foreach($types as $value => $label)
                <a href="{{ route('press.index', ['type' => $value]) }}"
                   class="px-4 py-2 rounded-full text-sm font-semibold transition-colors {{ request('type') === $value ? 'bg-kidical-blue text-white' : 'bg-gray-100 text-gray-700 hover:bg-kidical-light-yellow' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($press as $item)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow border-t-4 border-kidical-orange">
                    @if($item->getFirstMedia('attachment'))
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="{{ $item->getFirstMediaUrl('attachment', 'card') }}"
                                 alt="{{ $item->title }}"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                    @endif
                    <div class="p-6">
                        @if($item->highlighted)
                            <span class="inline-block px-2 py-1 bg-kidical-yellow text-kidical-blue text-xs font-bold rounded-full mb-2">
                                ★ Featured
                            </span>
                        @endif
                        <h3 class="text-xl font-bold text-kidical-blue mb-2">{{ $item->title }}</h3>
                        <div class="flex items-center text-sm text-gray-600 mb-3 gap-2 flex-wrap">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $item->media_type->badgeClasses() }}">
                                {{ $item->media_type->label() }}
                            </span>
                            <span class="font-semibold">{{ $item->outlet }}</span>
                            <span>•</span>
                            <span>{{ $item->publication_date->format('M d, Y') }}</span>
                        </div>
                        @if($item->description)
                            <p class="text-gray-700 mb-4">{{ Str::limit(strip_tags($item->description), 150) }}</p>
                        @endif
                        @if($item->groups->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($item->groups as $group)
                                    <span class="px-2 py-1 bg-kidical-light-yellow text-kidical-blue text-xs font-semibold rounded-full">
                                        {{ $group->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        @if($item->url)
                            <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-block px-4 py-2 bg-kidical-green text-white rounded-lg hover:bg-kidical-orange transition-colors font-semibold text-sm">
                                Read Article →
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-gray-500">
                    <p class="text-lg">No press items found.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $press->links() }}
        </div>
    </div>
</x-layouts::site>
