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

        return view('articles.index', compact('articles'));
    }

    public function show(string $locale, Article $article): View
    {
        abort_unless($article->is_published, 404);

        $article->load(['author', 'groups']);

        return view('articles.show', compact('article'));
    }
}
