<?php

use App\Models\Group;
use App\Models\Partner;
use Illuminate\Support\Facades\File;

it('renders a skip link that targets the main landmark', function () {
    $response = $this->get('/nl');

    $response->assertOk();
    $response->assertSee(__('nav.skip_to_content'));
    $response->assertSee('href="#main"', false);
    $response->assertSee('<main id="main"', false);
});

it('exposes the mobile menu toggle state to assistive tech', function () {
    $response = $this->get('/nl');

    $response->assertOk();
    $response->assertSee('aria-expanded="false"', false);
    $response->assertSee('aria-controls="site-mobile-menu"', false);
    $response->assertSee('id="site-mobile-menu"', false);
    $response->assertDontSee('Toggle menu');
});

it('labels the navigation landmarks', function () {
    $response = $this->get('/nl');

    $response->assertOk();
    $response->assertSee('aria-label="'.__('nav.main_menu').'"', false);
});

it('defines a global keyboard focus style and a skip-link style', function () {
    expect(File::get(resource_path('css/app.css')))->toContain(':focus-visible');
    expect(File::get(resource_path('css/chrome.css')))->toContain('.skip-link');
});

it('keeps a continuous heading outline on getting-started', function () {
    // h1 (hero) → h2 (expectations, sr-only) → h3 (cards) → h2 (FAQ); previously
    // the six card h3s followed the h1 directly, with the first h2 only at the FAQ.
    $this->get('/nl/getting-started')
        ->assertOk()
        ->assertSeeInOrder(['</h1>', '</h2>', '</h3>', '</h2>'], false);
});

it('marks the 404 suggestion cards up as a list', function () {
    $this->get('/nl/deze-pagina-bestaat-niet')
        ->assertNotFound()
        ->assertSee('role="list"', false);
});

it('renders the partner showcase logos as a list', function () {
    Partner::factory()->create([
        'name' => 'Pro Velo',
        'group_id' => null,
        'visible' => true,
        'show_logo' => true,
    ]);

    $this->get('/nl')
        ->assertOk()
        ->assertSee('<ul class="partner-strip__logos" role="list">', false);
});

it('renders unavailable chapter downloads as previews, not dead links', function () {
    // The downloads block only renders inside the partners column of the
    // extras band, so the chapter needs a visible partner.
    $group = Group::factory()->create();
    Partner::factory()->create(['group_id' => $group->id, 'visible' => true]);

    $this->get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('chapter-downloads__link', false)
        ->assertDontSee('href="#"', false);
});
