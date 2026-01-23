<?php

namespace App\Http\Controllers;

use App\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['author', 'groups'])
            ->orderBy('begin_date')
            ->paginate(12);

        return view('activities.index', compact('activities'));
    }

    public function show(Activity $activity)
    {
        $activity->load(['author', 'groups']);

        return view('activities.show', compact('activity'));
    }
}
