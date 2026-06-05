<?php

use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

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

it('renders a name chip for a logo-less partner instead of a gap', function () {
    Partner::factory()->create([
        'name' => 'Totally Fake Org That Has No Logo',
        'url' => null,
        'group_id' => null,
        'show_logo' => true,
        'visible' => true,
    ]);

    $this->blade('<x-partners />')
        ->assertSee('partner-strip__chip', false)
        ->assertSee('Totally Fake Org That Has No Logo');
});

it('never renders a stock photo url in the strip', function () {
    Partner::factory()->count(3)->create([
        'group_id' => null,
        'show_logo' => true,
        'visible' => true,
    ]);

    $this->blade('<x-partners />')->assertDontSee('picsum');
});

it('renders Brussel Mobiliteit exactly once', function () {
    Partner::factory()->create([
        'name' => 'Brussel Mobiliteit',
        'group_id' => null,
        'show_logo' => true,
        'visible' => true,
    ]);

    $html = Blade::render('<x-partners />');
    expect(substr_count($html, 'Brussel Mobiliteit'))->toBe(1);
});
