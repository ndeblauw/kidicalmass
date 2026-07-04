<?php

use App\Models\Group;
use App\Models\User;

it('shows the branded 404 with routes back into the site', function () {
    $response = $this->get('/nl/deze-pagina-bestaat-niet');

    $response->assertNotFound();
    $response->assertSee('data-error-page="404"', false);
    $response->assertSee(route('activities.index'), false);
    $response->assertSee(route('groups.index'), false);
    $response->assertSee(route('getting-started'), false);
});

it('shows the branded 403 with a login action on member-only pages', function () {
    $group = Group::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('groups.roze-hesjes', $group));

    $response->assertForbidden();
    $response->assertSee('data-error-page="403"', false);
    $response->assertSee(route('login'), false);
});

it('renders the session-expired page with a retry action', function () {
    $html = view('errors.419')->render();

    expect($html)
        ->toContain('data-error-page="419"')
        ->toContain('history.back()');
});

it('renders the standalone 500 and 503 pages without app asset dependencies', function (string $code) {
    $html = view('errors.'.$code)->render();

    expect($html)
        ->toContain('data-error-page="'.$code.'"')
        ->not->toContain('vite');
})->with(['500', '503']);

it('previews every error page on the non-production preview route', function (string $code) {
    $response = $this->get('/preview/errors/'.$code);

    $response->assertStatus((int) $code);
    $response->assertSee('data-error-page="'.$code.'"', false);
})->with(['404', '403', '419', '500', '503']);

it('rejects unknown codes on the preview route', function () {
    $this->get('/preview/errors/418')->assertNotFound();
});
