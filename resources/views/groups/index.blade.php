<x-layouts::site title="Groups">
    <div class="space-y-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-kidical-blue mb-4">Local Groups</h1>
            <p class="text-lg text-gray-700">Find a Kidical Mass group near you</p>
        </div>

        <!-- Group Statistics Component -->
        <x-group-statistics />

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($groups as $group)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow border-t-4 border-kidical-blue">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-kidical-blue mb-3 hover:text-kidical-orange transition-colors">
                            <a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>
                        </h3>
                        <div class="flex items-center text-sm text-gray-600 mb-3">
                            <svg class="w-4 h-4 mr-1 text-kidical-blue" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">{{ $group->zip }}</span>
                        </div>
                        @if($group->parent)
                            <div class="text-sm text-gray-600 mb-3">
                                <span class="text-gray-500">Part of:</span> 
                                <a href="{{ route('groups.show', $group->parent) }}" class="text-kidical-green hover:text-kidical-orange transition-colors font-semibold">
                                    {{ $group->parent->name }}
                                </a>
                            </div>
                        @endif
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-3 py-1 bg-kidical-light-yellow rounded-full text-kidical-blue text-sm font-semibold">
                                {{ $group->articles_count }} articles
                            </span>
                            <span class="px-3 py-1 bg-kidical-light-yellow rounded-full text-kidical-blue text-sm font-semibold">
                                {{ $group->activities_count }} activities
                            </span>
                        </div>
                        @if($group->children->isNotEmpty())
                            <div class="text-sm text-gray-600 mb-4">
                                <svg class="w-4 h-4 inline-block mr-1 text-kidical-blue" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                </svg>
                                <span class="font-semibold">{{ $group->children->count() }} subgroups</span>
                            </div>
                        @endif
                        <a href="{{ route('groups.show', $group) }}" class="inline-block px-4 py-2 bg-kidical-blue text-white rounded-lg hover:bg-kidical-green transition-colors font-semibold text-sm">
                            View Group →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts::site>
