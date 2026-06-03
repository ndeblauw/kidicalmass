<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
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

        return view('groups.index', compact('groups', 'activityCount'));
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
            ->with('author')
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->get();

        return view('groups.show', compact('group', 'articles', 'activities'));
    }
}
