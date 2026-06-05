<?php

use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('attaches the real logo file to a partner whose slug has a logo', function () {
    expect(file_exists(public_path('img/partners/logos/raw/pro-velo.svg')))->toBeTrue();

    $partner = Partner::factory()->create([
        'name' => 'Pro Velo',
        'group_id' => null,
        'show_logo' => true,
        'visible' => true,
    ]);

    expect($partner->getFirstMediaUrl('logo'))->not->toBe('');
    expect($partner->getFirstMediaUrl('logo'))->not->toContain('picsum');
});

it('attaches no logo when no matching file exists', function () {
    $partner = Partner::factory()->create([
        'name' => 'Totally Fake Org That Has No Logo',
        'group_id' => null,
        'show_logo' => true,
        'visible' => true,
    ]);

    expect($partner->getFirstMediaUrl('logo'))->toBe('');
});
