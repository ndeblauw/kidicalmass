<?php

use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->withoutVite();
});

it('emits hreflang alternates for the locales the page exists in', function () {
    get('/nl/events')
        ->assertSee('hreflang="nl-BE"', escape: false)
        ->assertSee('hreflang="fr-BE"', escape: false)
        ->assertSee('hreflang="x-default"', escape: false);
});

it('emits no hreflang alternates when there is no page route', function () {
    get('/no-such-page')->assertNotFound()->assertDontSee('hreflang=', escape: false);
});
