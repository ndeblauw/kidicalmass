<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Group;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $groupIds = $request->user()->groups()->pluck('groups.id');

        $upcomingActivities = Activity::query()
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->take(5)
            ->get();

        $pastActivities = Activity::query()
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->where('begin_date', '<', now())
            ->orderByDesc('begin_date')
            ->take(5)
            ->get();

        return view('dashboard', [
            'upcomingActivities' => $upcomingActivities,
            'pastActivities' => $pastActivities,
        ]);
    }
}
