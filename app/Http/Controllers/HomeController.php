<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;

class HomeController extends Controller
{
    public function __invoke()
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
