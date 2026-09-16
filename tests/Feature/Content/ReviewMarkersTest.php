<?php

use Illuminate\Support\Facades\Blade;

it('renders the wrapped block as-is when the review marker flag is off', function () {
    config(['i18n.show_review_markers' => false]);

    $html = Blade::render('<x-to-be-confirmed>Unconfirmed claim</x-to-be-confirmed>');

    expect($html)->toContain('Unconfirmed claim')
        ->not->toContain(__('common.review_markers.to-be-confirmed'))
        ->not->toContain('data-review-marker');
});

it('renders the to-be-confirmed marker with its label behind the flag', function () {
    config(['i18n.show_review_markers' => true]);

    $html = Blade::render('<x-to-be-confirmed>Unconfirmed claim</x-to-be-confirmed>');

    expect($html)->toContain(__('common.review_markers.to-be-confirmed'))
        ->toContain('Unconfirmed claim')
        ->toContain('data-review-marker="to-be-confirmed"');
});

it('renders the layout-proposal marker with its label behind the flag', function () {
    config(['i18n.show_review_markers' => true]);

    $html = Blade::render('<x-layout-proposal>Proposed block</x-layout-proposal>');

    expect($html)->toContain(__('common.review_markers.layout-proposal'))
        ->toContain('Proposed block')
        ->toContain('data-review-marker="layout-proposal"');
});

it('renders a bare badge for a self-closing marker', function () {
    config(['i18n.show_review_markers' => true]);

    $html = Blade::render('<x-to-be-confirmed />');

    expect($html)->toContain(__('common.review_markers.to-be-confirmed'))
        ->not->toContain('review-marker__body');
});

it('labels the markers in the active locale', function () {
    app()->setLocale('fr');
    config(['i18n.show_review_markers' => true]);

    expect(Blade::render('<x-to-be-confirmed />'))->toContain(__('common.review_markers.to-be-confirmed'));
    expect(Blade::render('<x-layout-proposal />'))->toContain(__('common.review_markers.layout-proposal'));
});

it('provides the review marker labels in both locales', function () {
    foreach (['nl', 'fr'] as $locale) {
        foreach (['common.review_markers.to-be-confirmed', 'common.review_markers.layout-proposal'] as $key) {
            expect(__($key, [], $locale))->not->toBe($key);
        }
    }
});
