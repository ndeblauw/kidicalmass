<?php

use App\Livewire\Dashboard\ManageArticles;
use App\Models\Article;
use App\Models\Group;
use App\Models\User;
use Livewire\Livewire;

test('users can see articles from their groups', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $article = Article::factory()->create(['author_id' => $user->id]);
    $article->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageArticles::class)
        ->assertSee($article->title);
});

test('users cannot see articles from other groups', function () {
    $user = User::factory()->create();
    $userGroup = Group::factory()->create();
    $user->groups()->attach($userGroup);

    $otherGroup = Group::factory()->create();
    $article = Article::factory()->create();
    $article->groups()->attach($otherGroup);

    $this->actingAs($user);

    Livewire::test(ManageArticles::class)
        ->assertDontSee($article->title);
});

test('users can create articles', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageArticles::class)
        ->set('title', 'New Article')
        ->set('content', 'Article content here')
        ->call('save')
        ->assertDispatched('article-saved');

    expect(Article::where('title', 'New Article')->exists())->toBeTrue();
    expect(Article::where('title', 'New Article')->first()->groups->contains($group))->toBeTrue();
});

test('users can edit articles from their groups', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $article = Article::factory()->create(['author_id' => $user->id]);
    $article->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageArticles::class)
        ->call('edit', $article->id)
        ->set('title', 'Updated Title')
        ->set('content', 'Updated content')
        ->call('save');

    expect($article->fresh()->title)->toBe('Updated Title');
    expect($article->fresh()->content)->toBe('Updated content');
});

test('users can delete articles from their groups', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $article = Article::factory()->create(['author_id' => $user->id]);
    $article->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageArticles::class)
        ->call('delete', $article->id)
        ->assertDispatched('article-deleted');

    expect(Article::find($article->id))->toBeNull();
});

test('users cannot edit articles from other groups', function () {
    $user = User::factory()->create();
    $userGroup = Group::factory()->create();
    $user->groups()->attach($userGroup);

    $otherGroup = Group::factory()->create();
    $article = Article::factory()->create();
    $article->groups()->attach($otherGroup);

    $this->actingAs($user);

    Livewire::test(ManageArticles::class)
        ->call('edit', $article->id)
        ->assertStatus(404);
});

test('users cannot delete articles from other groups', function () {
    $user = User::factory()->create();
    $userGroup = Group::factory()->create();
    $user->groups()->attach($userGroup);

    $otherGroup = Group::factory()->create();
    $article = Article::factory()->create();
    $article->groups()->attach($otherGroup);

    $this->actingAs($user);

    Livewire::test(ManageArticles::class)
        ->call('delete', $article->id)
        ->assertStatus(404);
});

test('article title is required', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageArticles::class)
        ->set('title', '')
        ->set('content', 'Some content')
        ->call('save')
        ->assertHasErrors(['title']);
});

test('article content is required', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    $this->actingAs($user);

    Livewire::test(ManageArticles::class)
        ->set('title', 'Some title')
        ->set('content', '')
        ->call('save')
        ->assertHasErrors(['content']);
});
