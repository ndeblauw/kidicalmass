<?php

use Illuminate\Support\Facades\File;

test('calendar renders the shared filter bar, not the old kal-filterrow', function () {
    $response = $this->get(route('activities.index'));

    $response->assertOk();
    $response->assertSee('filter-bar', false);
    $response->assertDontSee('kal-filterrow', false);
});

test('no kal-filterrow rules remain in the stylesheets', function () {
    expect(File::get(resource_path('css/pages/calendar.css')))->not->toContain('kal-filterrow');
    expect(File::get(resource_path('css/components/location-picker.css')))->not->toContain('kal-filterrow');
});
