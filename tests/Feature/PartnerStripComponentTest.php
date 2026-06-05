<?php

use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

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

    // The strip self-gates to showcase routes, so exercise it through a real page.
    get(route('home'))
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

it('shows Brussel Mobiliteit only in the footer, exactly once', function () {
    // Brussel Mobiliteit moved from the strip to the footer funder credit;
    // it must appear exactly once on the page, never duplicated back into the strip.
    $html = get(route('home'))->getContent();

    expect(substr_count($html, 'Brussel Mobiliteit'))->toBe(1);
});
