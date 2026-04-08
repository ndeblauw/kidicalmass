<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Group;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ActivityController extends Controller
{
    public function index(): View
    {
        $activities = Activity::with(['author', 'groups'])
            ->orderBy('begin_date')
            ->paginate(12);

        return view('activities.index', compact('activities'));
    }

    public function create(): View
    {
        $this->authorize('create', Activity::class);

        return view('activities.create', [
            'activity' => new Activity(),
            'groups' => Group::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $this->authorize('create', Activity::class);

        $activity = Activity::create([
            ...$request->safe()->except('groups'),
            'author_id' => $request->user()->id,
        ]);
        $activity->groups()->sync($request->validated('groups', []));

        return redirect()->route('activities.show', $activity)
            ->with('status', 'Activity created.');
    }

    public function show(Activity $activity): View
    {
        $activity->load(['author', 'groups']);

        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity): View
    {
        $this->authorize('update', $activity);

        $activity->load('groups');

        return view('activities.edit', [
            'activity' => $activity,
            'groups' => Group::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $this->authorize('update', $activity);

        $activity->update($request->safe()->except('groups'));
        $activity->groups()->sync($request->validated('groups', []));

        return redirect()->route('activities.show', $activity)
            ->with('status', 'Activity updated.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $this->authorize('delete', $activity);

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('status', 'Activity deleted.');
    }
}
