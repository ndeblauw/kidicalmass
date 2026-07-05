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
