<?php

test('groups index shows the filter bar in the panel, without radius tabs', function () {
    $response = $this->get(route('groups.index'));

    $response->assertOk();
    $response->assertSee('filter-bar', false);
    $response->assertDontSee('grp-hero__locate', false);
    $response->assertDontSee('filter-bar__tab', false); // radius tabs are agenda-only
});
