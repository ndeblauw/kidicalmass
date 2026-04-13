<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ActivityController extends Controller
{
    public function create(): View
    {
        return view('home.activities.create', [
            'activity' => new Activity,
            'groups' => Group::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $activity = Activity::create($request->safe()->except('groups', 'image'));
        $activity->groups()->sync($request->validated('groups', []));

        if ($request->hasFile('image')) {
            $activity->addMediaFromRequest('image')->toMediaCollection('main');
        }

        return redirect()->route('home.activity.show', $activity)
            ->with('status', 'Activity created.');
    }

    public function edit(Activity $activity): View
    {
        $activity->load('groups', 'organizer');

        return view('home.activities.edit', [
            'activity' => $activity,
            'groups' => Group::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $activity->update($request->safe()->except('groups', 'image'));
        $activity->groups()->sync($request->validated('groups', []));

        if ($request->hasFile('image')) {
            $activity->clearMediaCollection('main');
            $activity->addMediaFromRequest('image')->toMediaCollection('main');
        }

        return redirect()->route('activities.show', $activity)
            ->with('status', 'Activity updated.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('status', 'Activity deleted.');
    }
}
