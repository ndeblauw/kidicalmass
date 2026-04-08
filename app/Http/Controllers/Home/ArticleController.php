<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Group;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ArticleController extends Controller
{
    public function create(): View
    {
        $this->authorize('create', Article::class);

        return view('articles.create', [
            'article' => new Article(),
            'groups' => Group::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $this->authorize('create', Article::class);

        $article = Article::create([
            ...$request->safe()->except('groups'),
            'author_id' => $request->user()->id,
        ]);
        $article->groups()->sync($request->validated('groups', []));

        return redirect()->route('articles.show', $article)
            ->with('status', 'Article created.');
    }

    public function edit(Article $article): View
    {
        $this->authorize('update', $article);

        $article->load('groups');

        return view('articles.edit', [
            'article' => $article,
            'groups' => Group::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        $article->update($request->safe()->except('groups'));
        $article->groups()->sync($request->validated('groups', []));

        return redirect()->route('articles.show', $article)
            ->with('status', 'Article updated.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->authorize('delete', $article);

        $article->delete();

        return redirect()->route('articles.index')
            ->with('status', 'Article deleted.');
    }
}
