<?php

use App\Models\YearStat;

it('persists an admin-entered year and participant count', function () {
    YearStat::create(['year' => 2025, 'participants' => 5500]);

    expect(YearStat::firstWhere('year', 2025)->participants)->toBe(5500);
});
