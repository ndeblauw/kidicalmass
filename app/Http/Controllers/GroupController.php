<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use App\Support\Location\Proximity;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(string $locale): View
    {
        $groups = Group::visible()
            ->with(['parent', 'children'])
            ->withCount(['articles', 'activities'])
            ->get();

        $activityCount = Activity::whereYear('begin_date', now()->year)->count();

        $location = CurrentLocation::resolve();
        $nearby = collect();

        if ($location) {
            $coordsByZip = PostalCode::whereIn('zip', $groups->pluck('zip')->filter()->unique())
                ->get()->keyBy('zip');

            $partition = Proximity::partitionByRadius(
                $groups,
                ['lat' => $location['lat'], 'lng' => $location['lng']],
                (float) config('location.nearby_radius_km'),
                fn ($group) => $group->zip && $coordsByZip->has($group->zip)
                    ? ['lat' => $coordsByZip[$group->zip]->latitude, 'lng' => $coordsByZip[$group->zip]->longitude]
                    : null,
            );

            $nearby = $partition['nearby'];
        }

        $myGroups = auth()->check()
            ? auth()->user()->groups()->where('invisible', false)->get()
            : collect();

        return view('groups.index', compact('groups', 'activityCount', 'location', 'nearby', 'myGroups'));
    }

    public function show(string $locale, Group $group): View
    {
        $group->load(['parent', 'children', 'users'])->loadCount(['articles', 'activities']);

        $groupIds = collect([$group->id]);
        $currentParent = $group->parent;
        while ($currentParent) {
            $groupIds->push($currentParent->id);
            $currentParent = $currentParent->parent;
        }

        $articles = Article::query()
            ->with('author')
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->latest()
            ->get();

        $activities = Activity::query()
            ->with(['author', 'groups'])
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->get();

        return view('groups.show', compact('group', 'articles', 'activities'));
    }
}
