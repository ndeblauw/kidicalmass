<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateHomeGroupRequest;
use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function show(Request $request, Group $group): View
    {
        $this->ensureMembership($request, $group);

        $group->load(['users' => fn ($query) => $query->orderBy('name')]);

        $upcomingActivities = Activity::query()
            ->whereHas('groups', fn ($query) => $query->where('groups.id', $group->id))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->get();

        $pastActivities = Activity::query()
            ->whereHas('groups', fn ($query) => $query->where('groups.id', $group->id))
            ->where('begin_date', '<', now())
            ->orderByDesc('begin_date')
            ->get();

        $upcomingActivity = $upcomingActivities->first();

        $newsItems = Article::query()
            ->whereHas('groups', fn ($query) => $query->where('groups.id', $group->id))
            ->latest()
            ->take(5)
            ->get();

        return view('home.groups.show', [
            'group' => $group,
            'upcomingActivity' => $upcomingActivity,
            'upcomingActivities' => $upcomingActivities,
            'pastActivities' => $pastActivities,
            'newsItems' => $newsItems,
        ]);
    }

    public function edit(Request $request, Group $group): View
    {
        $this->ensureMembership($request, $group);

        return view('home.groups.edit', compact('group'));
    }

    public function update(UpdateHomeGroupRequest $request, Group $group): RedirectResponse
    {
        $this->ensureMembership($request, $group);

        $group->update($request->validated());

        return redirect()
            ->route('home.groups.show', $group)
            ->with('status', 'Group details updated.');
    }

    private function ensureMembership(Request $request, Group $group): void
    {
        abort_unless(
            $request->user()->groups()->whereKey($group->getKey())->exists(),
            403
        );
    }
}
