<?php

it('shows the branded 404 with routes back into the site', function () {
    $response = $this->get('/nl/deze-pagina-bestaat-niet');

    $response->assertNotFound();
    $response->assertSee('data-error-page="404"', false);
    $response->assertSee(route('activities.index'), false);
    $response->assertSee(route('groups.index'), false);
    $response->assertSee(route('getting-started'), false);
});
