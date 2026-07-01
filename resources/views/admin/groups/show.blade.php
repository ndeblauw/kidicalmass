<x-ba-admin-layout>
    @push('blueadmin_header')
        @include('BlueAdminLayout::dataTables')
    @endpush

    @php
        $group = $model;
        $sixtyDaysAgo = now()->subDays(60);

        $recentActivities = $group->activities()
            ->with(['media', 'pressArticles', 'author', 'organizer'])
            ->where('begin_date', '<', now())
            ->where('begin_date', '>=', $sixtyDaysAgo)
            ->orderByDesc('begin_date')
            ->get();

        $unpublishedActivities = $group->activities()
            ->with(['author', 'media', 'pressArticles'])
            ->drafts()
            ->orderByDesc('begin_date')
            ->get();

        $upcomingIncompleteActivities = $group->activities()
            ->with(['organizer', 'media'])
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->get();

        $recentSubmissions = $group->contactForms()
            ->unhandled()
            ->latest()
            ->limit(10)
            ->get();
    @endphp

    {{-- Group Overview --}}
    <div class="bg-white shadow overflow-hidden sm:rounded-lg border-t-2 border-sky-500">
        <div class="px-4 py-5 sm:px-6 flex justify-between bg-gray-50">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ $group->name }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Captain Dashboard
                </p>
            </div>
            <div class="my-auto flex gap-x-2">
                <x-ba-admin-button href="{{ route('admin.groups.edit', $group->getKey()) }}" class="py-1 bg-sky-500">
                    Edit Group
                </x-ba-admin-button>
                <x-ba-admin-button href="{{ route('admin.activities.index') }}" class="py-1 bg-blue-500">
                    All Activities
                </x-ba-admin-button>
                <x-ba-delete-button action="{{ $config->getDestroyUrl($group->getKey()) }}" />
            </div>
        </div>

        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Short Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4">{{ $group->shortname }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Postal Code</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4">{{ $group->zip ?: '—' }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Parent Group</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4">{{ $group->parent?->name ?: '—' }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Started</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4">{{ $group->started_at?->format('Y-m-d') ?: '—' }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-5 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Ended</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-4">{{ $group->ended_at?->format('Y-m-d') ?: 'Active' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Member Management --}}
    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-3 sm:px-6 bg-gray-50 border-b border-gray-200">
            <h3 class="text-base font-medium text-gray-900">Member Management</h3>
        </div>
        <div class="px-4 py-3 text-xs text-gray-500 border-b border-gray-100">
            Manage group members and their roles. Click a role radio button to assign or change a member's role.
        </div>
        <div class="p-4">
            @livewire('group-member-manager', ['group' => $group])
        </div>
    </div>

    {{-- Unhandled Contact Submissions --}}
    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-3 sm:px-6 flex justify-between bg-gray-50 border-b border-gray-200">
            <h3 class="text-base font-medium text-gray-900">Recent Contact Submissions</h3>
            <x-ba-admin-button href="{{ route('admin.contactforms.index') }}" class="py-1 bg-rose-500 text-xs">
                View All
            </x-ba-admin-button>
        </div>
        <div class="px-4 py-3 text-xs text-gray-500 border-b border-gray-100">
            Unanswered submissions from your chapter's volunteer signup and contact forms.
        </div>
        @if($recentSubmissions->isEmpty())
            <p class="px-4 py-4 text-sm text-gray-500">No unanswered submissions. All caught up!</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
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
                        <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">{{ Str::limit($submission->message, 80) }}</td>
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
    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-3 sm:px-6 flex justify-between bg-gray-50 border-b border-gray-200">
            <h3 class="text-base font-medium text-gray-900">Unpublished Activities</h3>
            <x-ba-admin-button href="{{ route('admin.activities.create') }}" class="py-1 bg-green-500 text-xs">
                + New Activity
            </x-ba-admin-button>
        </div>
        <div class="px-4 py-3 text-xs text-gray-500 border-b border-gray-100">
            Activities below are still in draft. Complete the missing fields and publish them when ready.
        </div>
        @if($unpublishedActivities->isEmpty())
            <p class="px-4 py-4 text-sm text-gray-500">All activities are published — nothing needs attention.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Needs</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Route</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($unpublishedActivities as $activity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $activity->title_nl ?: $activity->title_fr }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $activity->begin_date?->format('Y-m-d') ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @php $missing = $activity->missingFields(); @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ count($missing) ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ count($missing) ? implode(', ', $missing) : 'None' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">{!! $activity->hasMainImage() ? '<i class="fad fa-check-square text-green-400"></i>' : '<i class="fal fa-square text-gray-300"></i>' !!}</td>
                        <td class="px-4 py-3 text-center">{!! $activity->hasRoute() ? '<i class="fad fa-check-square text-green-400"></i>' : '<i class="fal fa-square text-gray-300"></i>' !!}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $activity->author?->name ?: '—' }}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('admin.activities.edit', $activity->getKey()) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Recent Activities Follow-Up --}}
    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-3 sm:px-6 bg-gray-50 border-b border-gray-200">
            <h3 class="text-base font-medium text-gray-900">Recent Activities — Follow-Up</h3>
        </div>
        <div class="px-4 py-3 text-xs text-gray-500 border-b border-gray-100">
            Recent rides that need photos or press coverage. Add media and link press articles to keep your chapter's history complete.
        </div>
        @if($recentActivities->isEmpty())
            <p class="px-4 py-4 text-sm text-gray-500">No recent activities in the last 60 days.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Activity</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Photos</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Press</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Gallery</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($recentActivities as $activity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $activity->title_nl ?: $activity->title_fr }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $activity->begin_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-center">{!! ($activity->hasMainImage() || $activity->hasGallery()) ? '<i class="fad fa-check-square text-green-400"></i>' : '<i class="fal fa-square text-gray-300"></i>' !!}</td>
                        <td class="px-4 py-3 text-center">{!! $activity->hasPressCoverage() ? '<i class="fad fa-check-square text-green-400"></i>' : '<i class="fal fa-square text-gray-300"></i>' !!}</td>
                        <td class="px-4 py-3 text-center">{!! $activity->hasGallery() ? '<i class="fad fa-check-square text-green-400"></i>' : '<i class="fal fa-square text-gray-300"></i>' !!}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('admin.activities.edit', $activity->getKey()) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Upcoming Incomplete Activities --}}
    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg mb-10">
        <div class="px-4 py-3 sm:px-6 bg-gray-50 border-b border-gray-200">
            <h3 class="text-base font-medium text-gray-900">Upcoming Activities — Missing Information</h3>
        </div>
        <div class="px-4 py-3 text-xs text-gray-500 border-b border-gray-100">
            Upcoming activities missing important information such as a route, location, or organizer. Complete these details before the event.
        </div>
        @if($upcomingIncompleteActivities->isEmpty())
            <p class="px-4 py-4 text-sm text-gray-500">No upcoming activities planned yet.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Activity</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Missing</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Organizer</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($upcomingIncompleteActivities as $activity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $activity->title_nl ?: $activity->title_fr }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $activity->begin_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            @php $missing = $activity->missingFields(); @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ count($missing) ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ count($missing) ? implode(', ', $missing) : 'None' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $activity->location ?: '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $activity->organizer?->name ?: '—' }}</td>
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
