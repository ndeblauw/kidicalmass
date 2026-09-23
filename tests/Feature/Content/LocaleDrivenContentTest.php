<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config(['i18n.show_review_markers' => true]);
});

it('shows the review markers and French-only content only on the French pages', function () {
    expect(get('/nl/about/organisation')->assertOk()->getContent())
        ->not->toContain('data-review-marker');

    $french = get('/fr/a-propos/comment-nous-fonctionnons')->assertOk()->getContent();

    expect($french)
        ->toContain('data-review-marker="to-be-confirmed"')
        ->toContain('data-review-marker="layout-proposal"')
        ->toContain('Les groupes gardent leur autonomie');
});

it('drives the partner review markers from the translations, not the locale code', function () {
    expect(get('/nl/about/partners')->assertOk()->getContent())
        ->not->toContain('data-review-marker');

    expect(get('/fr/a-propos/partenaires')->assertOk()->getContent())
        ->toContain('data-review-marker="to-be-confirmed"')
        ->toContain('data-review-marker="layout-proposal"');
});

it('shows the one-off donation block only where its copy is filled', function () {
    expect(get('/nl/steun-ons')->assertOk()->getContent())
        ->not->toContain('data-review-marker')
        ->not->toContain('support.donation');

    $french = get('/fr/nous-soutenir')->assertOk()->getContent();

    expect($french)
        ->toContain('data-review-marker="to-be-confirmed"')
        ->toContain(__('support.donation.heading', [], 'fr'));
});

it('renders the site name from translations', function () {
    expect(get('/nl')->assertOk()->getContent())
        ->toContain('<meta property="og:site_name" content="Kidical Mass België">');

    expect(get('/fr')->assertOk()->getContent())
        ->toContain('<meta property="og:site_name" content="Kidical Mass Belgique">');
});

it('renders the privacy authority link and update date from translations', function () {
    expect(get('/nl/privacy')->assertOk()->getContent())
        ->toContain('gegevensbeschermingsautoriteit.be')
        ->toContain('7 juli 2026');

    expect(get('/fr/confidentialite')->assertOk()->getContent())
        ->toContain('autoriteprotectiondonnees.be')
        ->toContain('7 juillet 2026');
});

it('has real French copy for the support callouts, not a Dutch fallback', function () {
    foreach (['home', 'event'] as $variant) {
        foreach (['title', 'body'] as $field) {
            $key = "support.callout.{$variant}.{$field}";

            expect(__($key, [], 'fr'))->not->toBe(__($key, [], 'nl'));
        }
    }
});
