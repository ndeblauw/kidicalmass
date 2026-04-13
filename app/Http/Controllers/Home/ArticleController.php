<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Group;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ArticleController extends Controller
{
    public function create(): View
    {
        return view('articles.create', [
            'article' => new Article(),
            'groups' => Group::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $article = Article::create([
            ...$request->safe()->except('groups', 'image'),
            'author_id' => $request->user()->id,
        ]);
        $article->groups()->sync($request->validated('groups', []));

        if ($request->hasFile('image')) {
            $article->addMediaFromRequest('image')->toMediaCollection('main');
        }

        return redirect()->route('articles.show', $article)
            ->with('status', 'Article created.');
    }

    public function edit(Article $article): View
    {
        $article->load('groups');

        return view('articles.edit', [
            'article' => $article,
            'groups' => Group::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $article->update($request->safe()->except('groups', 'image'));
        $article->groups()->sync($request->validated('groups', []));

        if ($request->hasFile('image')) {
            $article->clearMediaCollection('main');
            $article->addMediaFromRequest('image')->toMediaCollection('main');
        }

        return redirect()->route('articles.show', $article)
            ->with('status', 'Article updated.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('articles.index')
            ->with('status', 'Article deleted.');
    }
}
