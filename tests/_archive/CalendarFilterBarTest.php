<?php

use App\Livewire\RideCalendar;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

test('calendar renders the shared filter bar, not the old kal-filterrow', function () {
    $response = $this->get(route('activities.index'));

    $response->assertOk();
    $response->assertSee('filter-bar', false);
    $response->assertDontSee('kal-filterrow', false);
});

test('calendar announces result updates through a status region', function () {
    Livewire::test(RideCalendar::class)
        ->assertSeeHtml('role="status"');
});

test('radius tabs expose their pressed state to assistive tech', function () {
    Livewire::withCookie('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    Livewire::test(RideCalendar::class, ['radius' => 'regio'])
        ->assertSeeHtml('aria-pressed="true"')
        ->assertSeeHtml('aria-pressed="false"');
});

test('no kal-filterrow rules remain in the stylesheets', function () {
    expect(File::get(resource_path('css/pages/calendar.css')))->not->toContain('kal-filterrow');
    expect(File::get(resource_path('css/components/location-picker.css')))->not->toContain('kal-filterrow');
});
