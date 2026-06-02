<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(string $locale): View
    {
        $articles = Article::with(['author', 'groups'])
            ->latest()
            ->paginate(12);

        return view('articles.index', compact('articles'));
    }

    public function show(string $locale, Article $article): View
    {
        $article->load(['author', 'groups']);

        return view('articles.show', compact('article'));
    }
}
