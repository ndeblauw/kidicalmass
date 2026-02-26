<div>
    @if($items->isNotEmpty())
        <section class="bg-kidical-light-yellow -mx-4 px-4 py-12 md:-mx-0 md:px-8 md:rounded-2xl mb-12">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-kidical-blue">In the Press</h2>
                <a href="{{ route('press.index') }}" class="text-kidical-green hover:text-kidical-orange transition-colors font-semibold">
                    All Press →
                </a>
            </div>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($items as $item)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow border-t-4 border-kidical-orange">
                        @if($item->getFirstMedia('attachment'))
                            <div class="aspect-[4/3] overflow-hidden">
                                <img src="{{ $item->getFirstMediaUrl('attachment', 'card') }}"
                                     alt="{{ $item->title }}"
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                        @endif
                        <div class="p-5">
                            @if($item->highlighted)
                                <span class="inline-block px-2 py-1 bg-kidical-yellow text-kidical-blue text-xs font-bold rounded-full mb-2">
                                    ★ Featured
                                </span>
                            @endif
                            <h3 class="text-lg font-bold text-kidical-blue mb-2">{{ $item->title }}</h3>
                            <div class="flex items-center text-sm text-gray-600 mb-2 gap-2 flex-wrap">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $item->media_type->badgeClasses() }}">
                                    {{ $item->media_type->label() }}
                                </span>
                                <span class="font-semibold">{{ $item->outlet }}</span>
                                <span>•</span>
                                <span>{{ $item->publication_date->format('M d, Y') }}</span>
                            </div>
                            @if($item->description)
                                <p class="text-gray-600 text-sm mb-3">{{ Str::limit(strip_tags($item->description), 100) }}</p>
                            @endif
                            @if($item->url)
                                <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-block px-3 py-1.5 bg-kidical-green text-white rounded-lg hover:bg-kidical-orange transition-colors font-semibold text-sm">
                                    Read →
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
