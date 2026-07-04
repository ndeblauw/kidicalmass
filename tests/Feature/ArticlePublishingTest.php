<?php

use App\Models\Article;
use App\Models\Group;
use App\Models\User;

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

it('orders the feed by publish date, newest first, with the newest in the feature slot', function () {
    Article::factory()->create(['title_nl' => 'Ouder bericht', 'published_at' => now()->subDays(10), 'created_at' => now()]);
    Article::factory()->create(['title_nl' => 'Verser bericht', 'published_at' => now()->subDay(), 'created_at' => now()->subMonth()]);

    $html = get('/nl/about/news')
        ->assertOk()
        ->assertSeeInOrder(['Verser bericht', 'Ouder bericht'])
        ->getContent();

    // The newest article fills the feature slot (data-article-feature seam);
    // the older one stays in the grid, and the feature never repeats there.
    preg_match('/<article[^>]*data-article-feature[\s\S]*?<\/article>/', $html, $feature);
    expect($feature[0] ?? '')->toContain('Verser bericht')->not->toContain('Ouder bericht');
    expect(substr_count($html, 'Verser bericht'))->toBe(1);
});

it('renders deeper feed pages as a plain grid without the feature slot', function () {
    Article::factory()->count(13)->create();

    get('/nl/about/news?page=2')
        ->assertOk()
        ->assertDontSee('data-article-feature', escape: false);
});

it('renders rich-text content as HTML and legacy plain text with line breaks', function () {
    $rich = Article::factory()->create(['content_nl' => '<p>Een <strong>rijk</strong> bericht.</p>']);
    $plain = Article::factory()->create(['content_nl' => "Regel een.\nRegel twee."]);

    get(route('articles.show', $rich))->assertOk()->assertSee('<strong>rijk</strong>', escape: false);
    get(route('articles.show', $plain))->assertOk()->assertSee("Regel een.<br />\nRegel twee.", escape: false);
});

it('renders article chrome in Dutch, never English', function () {
    $article = Article::factory()->create(['published_at' => '2026-03-05 12:00:00']);

    get(route('articles.show', $article))
        ->assertOk()
        ->assertSee('5 maart 2026')
        ->assertDontSee('March')
        ->assertDontSee('Back to articles');

    // A lone article renders as the feature, which spells the month out.
    get('/nl/about/news')->assertOk()->assertSee('5 maart 2026');
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

it('links neighbouring published articles under Meer nieuws, skipping drafts', function () {
    $oldest = Article::factory()->create(['title_nl' => 'Oudste bericht', 'published_at' => now()->subDays(3)]);
    $middle = Article::factory()->create(['title_nl' => 'Middelste bericht', 'published_at' => now()->subDays(2)]);
    Article::factory()->draft()->create(['title_nl' => 'Kladversie ertussen', 'published_at' => now()->subDay()]);
    $newest = Article::factory()->create(['title_nl' => 'Nieuwste bericht', 'published_at' => now()]);

    get(route('articles.show', $middle))
        ->assertOk()
        ->assertSee(__('about.news_more_title'))
        ->assertSee(route('articles.show', $oldest))
        ->assertSee(route('articles.show', $newest))
        ->assertDontSee('Kladversie ertussen');

    // The newest article has no newer neighbour: its rail (the
    // data-article-neighbours seam) lists exactly one link, the older one.
    $html = get(route('articles.show', $newest))->assertOk()->getContent();
    preg_match('/<nav[^>]*data-article-neighbours[\s\S]*?<\/nav>/', $html, $rail);
    expect($rail[0] ?? '')->toContain(route('articles.show', $middle));
    expect(substr_count($rail[0] ?? '', '<li>'))->toBe(1);
});

it('shows the group on feed cards, linked to its chapter page, instead of the author', function () {
    $chapter = Group::factory()->create(['name' => 'Kidical Mass Testegem', 'invisible' => false]);
    $feature = Article::factory()->create(['published_at' => now()]);
    $gridArticle = Article::factory()
        ->for(User::factory()->create(['name' => 'Zeldzame Schrijfnaam']), 'author')
        ->create(['published_at' => now()->subDay()]);
    $feature->groups()->attach($chapter);
    $gridArticle->groups()->attach($chapter);

    $html = get('/nl/about/news')->assertOk()->getContent();

    // Both the feature slot and a grid card carry the chapter chip as a link.
    expect(substr_count($html, 'Kidical Mass Testegem'))->toBe(2)
        ->and(substr_count($html, route('groups.show', $chapter)))->toBe(2)
        ->and($html)->not->toContain('Zeldzame Schrijfnaam');
});

it('labels national news Heel België and never links invisible region nodes', function () {
    // Invisible groups (Belgium, regions) are grouping data whose chapter page
    // 404s, so their chips must render as plain text; the country root reads
    // as "national" news.
    $national = Group::factory()->create(['name' => 'Belgium', 'invisible' => true, 'parent_id' => null]);
    $region = Group::factory()->withParent($national)->create(['name' => 'Regio Testland', 'invisible' => true]);
    Article::factory()->create(['published_at' => now()])->groups()->attach($national);
    $regionArticle = Article::factory()->create(['published_at' => now()->subDay()]);
    $regionArticle->groups()->attach($region);

    get('/nl/about/news')
        ->assertOk()
        ->assertSee(__('about.news_national'))
        ->assertSee('Regio Testland')
        ->assertDontSee(route('groups.show', $national))
        ->assertDontSee(route('groups.show', $region));

    get(route('articles.show', $regionArticle))
        ->assertOk()
        ->assertSee('Regio Testland')
        ->assertDontSee(route('groups.show', $region));
});

it('renders the branded paginator once the feed exceeds one page', function () {
    Article::factory()->count(13)->create();

    get('/nl/about/news')
        ->assertOk()
        ->assertSee('data-pagination', false)
        ->assertSee(__('common.pagination_next'))
        ->assertSee('/nl/about/news?page=2');

    get('/nl/about/news?page=2')
        ->assertOk()
        ->assertSee(__('common.pagination_previous'));
});
