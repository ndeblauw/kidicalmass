<?php

use App\Models\Article;
use App\Models\Group;

use function Pest\Laravel\get;

it('hides drafts from the news feed and 404s their detail page', function () {
    $draft = Article::factory()->draft()->create(['title_nl' => 'Geheime kladversie']);
    $live = Article::factory()->create(['title_nl' => 'Nieuwe groep in Gent']);

    get('/nl/about/news')
        ->assertOk()
        ->assertSee('Nieuwe groep in Gent')
        ->assertDontSee('Geheime kladversie');

    get(route('articles.show', $draft))->assertNotFound();
    get(route('articles.show', $live))->assertOk();
});

it('orders the feed by publish date, newest first', function () {
    Article::factory()->create(['title_nl' => 'Ouder bericht', 'published_at' => now()->subDays(10), 'created_at' => now()]);
    Article::factory()->create(['title_nl' => 'Verser bericht', 'published_at' => now()->subDay(), 'created_at' => now()->subMonth()]);

    get('/nl/about/news')->assertOk()->assertSeeInOrder(['Verser bericht', 'Ouder bericht']);
});

it('renders rich-text content as HTML and legacy plain text with line breaks', function () {
    $rich = Article::factory()->create(['content_nl' => '<p>Een <strong>rijk</strong> bericht.</p>']);
    $plain = Article::factory()->create(['content_nl' => "Regel een.\nRegel twee."]);

    get(route('articles.show', $rich))->assertOk()->assertSee('<strong>rijk</strong>', escape: false);
    get(route('articles.show', $plain))->assertOk()->assertSee("Regel een.<br />\nRegel twee.", escape: false);
});

it('renders a published article without a publish date instead of crashing', function () {
    $article = Article::factory()->create(['title_nl' => 'Bericht zonder datum', 'published_at' => null]);

    get('/nl/about/news')->assertOk()->assertSee('Bericht zonder datum');
    get(route('articles.show', $article))->assertOk();
});

it('hides draft articles from the chapter page and its article count', function () {
    // The chapter page itself no longer renders article titles (news was cut
    // from it, see GroupsTest "group show mixes parent and direct content"),
    // so the leak surfaces in the view data and the article count, not in
    // visible page text.
    $group = Group::factory()->create();
    $live = Article::factory()->create(['title_nl' => 'Live groepsbericht']);
    $draft = Article::factory()->draft()->create(['title_nl' => 'Klad groepsbericht']);
    $live->groups()->attach($group);
    $draft->groups()->attach($group);

    $response = get(route('groups.show', $group))->assertOk();

    $response->assertViewHas('articles', function ($articles) {
        return $articles->pluck('title_nl')->values()->all() === ['Live groepsbericht'];
    });

    $response->assertViewHas('group', fn (Group $viewGroup) => $viewGroup->articles_count === 1);
});
