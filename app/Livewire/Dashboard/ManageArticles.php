<?php

namespace App\Livewire\Dashboard;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManageArticles extends Component
{
    public $title = '';

    public $content = '';

    public $editingArticleId = null;

    public $showForm = false;

    public function mount()
    {
        //
    }

    public function render()
    {
        $userGroups = Auth::user()->groups()->pluck('groups.id');

        $articles = Article::whereHas('groups', function ($query) use ($userGroups) {
            $query->whereIn('groups.id', $userGroups);
        })->with(['groups', 'author'])->latest()->get();

        return view('livewire.dashboard.manage-articles', [
            'articles' => $articles,
        ]);
    }

    public function toggleForm()
    {
        $this->showForm = ! $this->showForm;
        if (! $this->showForm) {
            $this->reset(['title', 'content', 'editingArticleId']);
        }
    }

    public function create()
    {
        $this->reset(['title', 'content', 'editingArticleId']);
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $userGroups = Auth::user()->groups()->pluck('groups.id');

        if ($this->editingArticleId) {
            $article = Article::whereHas('groups', function ($query) use ($userGroups) {
                $query->whereIn('groups.id', $userGroups);
            })->findOrFail($this->editingArticleId);

            $article->update([
                'title' => $this->title,
                'content' => $this->content,
            ]);
        } else {
            $article = Article::create([
                'title' => $this->title,
                'content' => $this->content,
                'author_id' => Auth::id(),
            ]);

            // Attach to user's groups
            $article->groups()->attach($userGroups);
        }

        $this->reset(['title', 'content', 'editingArticleId', 'showForm']);
        $this->dispatch('article-saved');
    }

    public function edit($articleId)
    {
        $userGroups = Auth::user()->groups()->pluck('groups.id');

        $article = Article::whereHas('groups', function ($query) use ($userGroups) {
            $query->whereIn('groups.id', $userGroups);
        })->findOrFail($articleId);

        $this->editingArticleId = $article->id;
        $this->title = $article->title;
        $this->content = $article->content;
        $this->showForm = true;
    }

    public function delete($articleId)
    {
        $userGroups = Auth::user()->groups()->pluck('groups.id');

        $article = Article::whereHas('groups', function ($query) use ($userGroups) {
            $query->whereIn('groups.id', $userGroups);
        })->findOrFail($articleId);

        $article->delete();

        $this->dispatch('article-deleted');
    }

    public function cancel()
    {
        $this->reset(['title', 'content', 'editingArticleId', 'showForm']);
    }
}
