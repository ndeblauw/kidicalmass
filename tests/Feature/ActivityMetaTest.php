<?php

use App\Models\Activity;

test('metaDescription strips tags, squishes and truncates content_nl', function () {
    $activity = new Activity;
    $activity->content_nl = '<p>Fietsen  is   fijn.</p> '.str_repeat('<strong>Samen op pad.</strong> ', 30);

    expect($activity->metaDescription())
        ->not->toContain('<')
        ->not->toContain('  ')
        ->toStartWith('Fietsen is fijn.');
    expect(mb_strlen($activity->metaDescription()))->toBeLessThanOrEqual(158);
});

test('ogImageUrl returns the og conversion url when a main image exists', function () {
    $activity = Activity::factory()->withMedia()->create();

    expect($activity->ogImageUrl())->toContain('-og.jpg');
});

test('ogImageUrl is null without a main image', function () {
    $activity = Activity::factory()->create();

    expect($activity->ogImageUrl())->toBeNull();
});
