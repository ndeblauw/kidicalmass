<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with(['author', 'groups'])
            ->latest()
            ->paginate(12);

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article)
    {
        $article->load(['author', 'groups']);

        return view('articles.show', compact('article'));
    }
}
