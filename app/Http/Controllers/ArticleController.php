<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(string $locale): View
    {
        $articles = Article::with(['author', 'groups'])
            ->published()
            ->orderByDesc('published_at')
            ->paginate(12);

        /* Featured-first feed: on page 1 the newest article renders as the
           wide feature and must not repeat in the grid; deeper pages are a
           plain grid. */
        $feature = $articles->onFirstPage() ? $articles->getCollection()->first() : null;
        $gridArticles = $feature ? $articles->getCollection()->slice(1) : $articles->getCollection();

        return view('articles.index', compact('articles', 'feature', 'gridArticles'));
    }

    public function show(string $locale, Article $article): View
    {
        abort_unless($article->is_published, 404);

        $article->load(['author', 'groups']);

        return view('articles.show', compact('article'));
    }
}
