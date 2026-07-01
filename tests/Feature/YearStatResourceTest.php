<?php

// tests/Feature/YearStatResourceTest.php
// The per-year participant figure on /steun-ons is curated by hand, so it must be
// editable in the admin. The panel itself is access-tested in FilamentAdminTest
// (and the testing env denies the panel to all, since User is not a FilamentUser),
// so here we lock the two things that keep the admin -> page path working:
//  - the resource is registered (its route exists), and
//  - the model accepts the admin-entered fields (mass assignment), which is what
//    the proof deck then reads back.

use App\Models\YearStat;
use Illuminate\Support\Facades\Route;

it('registers the year stats resource in the admin panel', function () {
    expect(Route::has('admin.yearstats.index'))->toBeTrue();
});

it('persists an admin-entered year and participant count', function () {
    YearStat::create(['year' => 2025, 'participants' => 5500]);

    expect(YearStat::firstWhere('year', 2025)->participants)->toBe(5500);
});
