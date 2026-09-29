<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Support\AboutStats;
use Illuminate\View\View;

class AboutController extends Controller
{
    /** @param string $locale Supplied by the {locale} route prefix (set via SetLocale middleware). */
    public function __invoke(string $locale, AboutStats $stats): View
    {
        return view('about.index', [
            'stats' => $stats->highlights(),
            'latestArticles' => Article::with('groups')
                ->published()
                ->orderByDesc('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
