<div class="bg-white rounded-xl shadow-lg p-8 mb-8">
    <h2 class="text-3xl font-bold text-kidical-blue mb-6 text-center">{{ __('common.groups_growth_title') }}</h2>

    @if(count($statistics) > 0)
        <dl class="space-y-3">
            @foreach($statistics as $year => $count)
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-kidical-light-yellow to-white rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex items-center space-x-4">
                        <dt class="text-2xl font-bold text-kidical-blue normal-case tracking-normal">{{ $year }}</dt>
                        <div class="h-8 w-1 bg-kidical-orange" aria-hidden="true"></div>
                        <dd class="flex items-baseline space-x-2">
                            <span class="text-3xl font-extrabold text-kidical-green">{{ $count }}</span>
                            <span class="text-xl text-gray-600">{{ trans_choice('common.groups_growth_groups', $count) }}</span>
                        </dd>
                    </div>

                    {{-- Visual bar representing growth — decorative; the count itself is the data. --}}
                    <div class="flex-1 ml-8 max-w-md" aria-hidden="true">
                        <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-gradient-to-r from-kidical-blue to-kidical-green rounded-full transition-all duration-500"
                                style="width: {{ ($count / max($statistics)) * 100 }}%"
                            ></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </dl>
    @else
        <p class="text-center text-gray-500 py-4">{{ __('common.groups_growth_empty') }}</p>
    @endif
</div>
