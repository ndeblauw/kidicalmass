<?php

use App\Models\User;
use Illuminate\Support\Facades\Blade;

use function Pest\Laravel\actingAs;

test('the account menu renders its trigger and the logout item', function () {
    actingAs(User::factory()->create());

    $html = Blade::render('<x-account-menu />');

    expect($html)
        ->toContain('account-nav-btn')
        ->toContain('Uitloggen');
});
