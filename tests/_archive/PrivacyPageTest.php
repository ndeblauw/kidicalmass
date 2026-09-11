<?php

use function Pest\Laravel\get;

it('serves the privacy page with the GDPR essentials', function () {
    get('/nl/privacy')
        ->assertOk()
        // controller identity + contact route for data-subject requests
        ->assertSee(config('kidicalmass.contact.email'))
        // right to complain to the Belgian supervisory authority
        ->assertSee('gegevensbeschermingsautoriteit.be')
        // first-party cookies are named
        ->assertSee(config('session.cookie'))
        ->assertSee(config('location.cookie'))
        // retention promise is stated
        ->assertSee('12 maanden');
});

it('names the processors it shares data with', function () {
    get('/nl/privacy')
        ->assertOk()
        // hosting + backups (EEA)
        ->assertSee('Hetzner')
        ->assertSee('Akamai')
        // transactional + newsletter mail
        ->assertSee('Postmark')
        ->assertSee('MailerLite')
        // cookieless analytics
        ->assertSee('Fathom Analytics');
});

it('explains why there is no cookie banner', function () {
    get('/nl/privacy')
        ->assertOk()
        ->assertSee('cookiebanner');
});
