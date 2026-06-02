<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders main pages with status 200', function () {
    $this->get('/')->assertRedirect('/nl');
    $this->get('/nl')->assertOk();
    $this->get('/nl/chapters')->assertOk();
    $this->get('/nl/about/news')->assertOk();
    $this->get('/nl/events')->assertOk();
    $this->get('/login')->assertOk();
    $this->get('/register')->assertOk();
});

it('renders auth pages with status 200', function () {
    $this->get('/login')->assertOk();
    $this->get('/register')->assertOk();
    $this->get('/forgot-password')->assertOk();
});
