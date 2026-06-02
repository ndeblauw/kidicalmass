<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** @param string $locale Supplied by the {locale} route prefix (set via SetLocale middleware); kept first for route-model binding order. */
    public function __invoke(string $locale): View
    {
        $latestArticles = Article::with(['author', 'groups'])
            ->latest()
            ->take(6)
            ->get();

        $upcomingActivities = Activity::with(['author', 'groups'])
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->take(6)
            ->get();

        $groups = Group::withCount(['articles', 'activities'])
            ->whereNull('parent_id')
            ->get();

        return view('home', compact('latestArticles', 'upcomingActivities', 'groups'));
    }
}
