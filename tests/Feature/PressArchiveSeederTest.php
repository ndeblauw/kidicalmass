<?php

use App\Models\PressArticle;
use Database\Seeders\PressArchiveSeeder;

it('seeds the historic press archive once, idempotently', function () {
    $this->seed(PressArchiveSeeder::class);
    $count = PressArticle::count();

    expect($count)->toBeGreaterThanOrEqual(18);

    // Running twice must not duplicate.
    $this->seed(PressArchiveSeeder::class);
    expect(PressArticle::count())->toBe($count);

    // Spot-check one entry per era.
    expect(PressArticle::where('outlet', 'RTBF')->whereYear('published_at', 2025)->exists())->toBeTrue()
        ->and(PressArticle::where('outlet', 'Het Nieuwsblad')->whereYear('published_at', 2020)->exists())->toBeTrue()
        ->and(PressArticle::where('outlet', 'Persbericht')->count())->toBe(2);
});
