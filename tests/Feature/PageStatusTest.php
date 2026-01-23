<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders main pages with status 200', function () {
    // Test main pages
    $this->get('/')->assertOk();
    $this->get('/groups')->assertOk();
    $this->get('/articles')->assertOk();
    $this->get('/activities')->assertOk();
    $this->get('/login')->assertOk();
    $this->get('/register')->assertOk();
});

it('renders auth pages with status 200', function () {
    $this->get('/login')->assertOk();
    $this->get('/register')->assertOk();
    $this->get('/forgot-password')->assertOk();
});