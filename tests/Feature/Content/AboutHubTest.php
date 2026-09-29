<?php

use App\Models\Article;

use function Pest\Laravel\get;

it('shows the three newest published articles above the closing band', function () {
    Article::factory()
        ->count(4)
        ->sequence(fn ($seq) => [
            'title_nl' => 'Bericht '.($seq->index + 1),
            'published_at' => now()->subDays($seq->index),
        ])
        ->create();
    Article::factory()->draft()->create(['title_nl' => 'Geheime kladversie']);

    get('/nl/about')
        ->assertOk()
        ->assertSee('data-about-latest-news', false)
        ->assertSeeInOrder(['Bericht 1', 'Bericht 2', 'Bericht 3'])
        ->assertDontSee('Bericht 4')
        ->assertDontSee('Geheime kladversie')
        ->assertSee(localized_route('articles.index'));
});

it('leaves the news block out while nothing is published', function () {
    Article::factory()->draft()->create();

    get('/nl/about')
        ->assertOk()
        ->assertDontSee('data-about-latest-news', false);
});

it('renders the act-exits as link cards', function () {
    $html = get('/fr/a-propos')->assertOk()->getContent();

    expect(substr_count($html, 'data-link-card'))->toBe(4)
        ->and($html)->toContain(__('about.hub.exits.items.1', locale: 'fr'));
});
