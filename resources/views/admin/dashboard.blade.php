<x-ba-admin-layout>
    @push('blueadmin_header')
        @include('BlueAdminLayout::dataTables')
    @endpush

    @php
        $recentSubmissions = \App\Models\ContactForm::unhandled()
            ->with('group')
            ->latest()
            ->limit(10)
            ->get();

        $unpublishedActivities = \App\Models\Activity::drafts()
            ->with(['author', 'media', 'groups'])
            ->orderByDesc('begin_date')
            ->limit(10)
            ->get();
    @endphp

    <div class="px-6 py-10">
        <h1 class="text-2xl font-semibold text-neutral-900">Admin</h1>
        <p class="mt-2 text-sm text-neutral-600">
            Welcome to the Kidical Mass admin. Resources are being migrated here one
            model at a time; see the sidebar for what is currently available.
        </p>
    </div>

    {{-- Recent Contact Submissions --}}
    <div class="mx-6 mb-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-3 sm:px-6 flex justify-between bg-gray-50 border-b border-gray-200">
            <h3 class="text-base font-medium text-gray-900">Recent Contact Submissions</h3>
            <x-ba-admin-button href="{{ route('admin.contactforms.index') }}" class="py-1 bg-rose-500 text-xs">
                View All
            </x-ba-admin-button>
        </div>
        <div class="px-4 py-3 text-xs text-gray-500 border-b border-gray-100">
            Unanswered contact form submissions from across all chapters.
        </div>
        @if($recentSubmissions->isEmpty())
            <p class="px-4 py-4 text-sm text-gray-500">No unanswered submissions. All caught up!</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Chapter</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Received</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($recentSubmissions as $submission)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $submission->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            <a href="mailto:{{ $submission->email }}" class="text-blue-600 hover:text-blue-800">{{ $submission->email }}</a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $submission->group?->name ?: '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">{{ Str::limit($submission->message, 60) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $submission->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('admin.contactforms.show', $submission->getKey()) }}" class="text-blue-600 hover:text-blue-800">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Unpublished Activities --}}
    <div class="mx-6 mb-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-3 sm:px-6 flex justify-between bg-gray-50 border-b border-gray-200">
            <h3 class="text-base font-medium text-gray-900">Unpublished Activities</h3>
            <x-ba-admin-button href="{{ route('admin.activities.index') }}" class="py-1 bg-blue-500 text-xs">
                All Activities
            </x-ba-admin-button>
        </div>
        <div class="px-4 py-3 text-xs text-gray-500 border-b border-gray-100">
            Activities still in draft across all chapters. Complete missing fields and publish when ready.
        </div>
        @if($unpublishedActivities->isEmpty())
            <p class="px-4 py-4 text-sm text-gray-500">All activities are published — nothing needs attention.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Chapter</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Route</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($unpublishedActivities as $activity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $activity->title_nl ?: $activity->title_fr }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $activity->begin_date?->format('Y-m-d') ?: '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $activity->groups->pluck('name')->implode(', ') ?: '—' }}</td>
                        <td class="px-4 py-3 text-center">{!! $activity->hasMainImage() ? '<i class="fad fa-check-square text-green-400"></i>' : '<i class="fal fa-square text-gray-300"></i>' !!}</td>
                        <td class="px-4 py-3 text-center">{!! $activity->hasRoute() ? '<i class="fad fa-check-square text-green-400"></i>' : '<i class="fal fa-square text-gray-300"></i>' !!}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('admin.activities.edit', $activity->getKey()) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-ba-admin-layout>