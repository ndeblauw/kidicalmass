<div class="bg-white rounded-xl shadow-lg p-8 mb-8">
    <h2 class="text-3xl font-bold text-kidical-blue mb-6 text-center">We are growing!</h2>
    
    @if(count($statistics) > 0)
        <div class="space-y-3">
            @foreach($statistics as $year => $count)
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-kidical-light-yellow to-white rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex items-center space-x-4">
                        <span class="text-2xl font-bold text-kidical-blue">{{ $year }}</span>
                        <div class="h-8 w-1 bg-kidical-orange"></div>
                        <div class="flex items-baseline space-x-2">
                            <span class="text-3xl font-extrabold text-kidical-green">{{ $count }}</span>
                            <span class="text-xl text-gray-600">{{ $count === 1 ? 'group' : 'groups' }}</span>
                        </div>
                    </div>
                    
                    <!-- Visual bar representing growth -->
                    <div class="flex-1 ml-8 max-w-md">
                        <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                            <div 
                                class="h-full bg-gradient-to-r from-kidical-blue to-kidical-green rounded-full transition-all duration-500"
                                style="width: {{ ($count / max($statistics)) * 100 }}%"
                            ></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-center text-gray-500 py-4">No group statistics available yet.</p>
    @endif
</div>
