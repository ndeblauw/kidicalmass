<?php

use App\Models\Group;

test('groups index shows the location picker in the finder control bar, without radius tabs', function () {
    Group::factory()->create(['invisible' => false]);

    $response = $this->get(route('groups.index'));

    $response->assertOk();
    $response->assertSee('location-picker', false);       // shared picker, now in the control bar
    $response->assertSee('grp-region-btn', false);        // region selector present
    $response->assertDontSee('filter-bar', false);        // no full-bleed panel-top bar
    $response->assertDontSee('grp-hero__locate', false);
    $response->assertDontSee('filter-bar__tab', false);   // radius tabs are agenda-only
});
