<?php

use App\Models\Activity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('media');
});

it('parses the GPX track points into a lat/lon route', function () {
    $gpx = <<<'XML'
<gpx version="1.1" xmlns="http://www.topografix.com/GPX/1/1">
  <trk><trkseg>
    <trkpt lat="50.8676" lon="4.3735"></trkpt>
    <trkpt lat="50.8500" lon="4.3500"></trkpt>
  </trkseg></trk>
</gpx>
XML;

    $activity = Activity::factory()->create();
    $activity->addMedia(UploadedFile::fake()->createWithContent('route.gpx', $gpx))
        ->toMediaCollection('gpx');

    expect($activity->refresh()->route_coordinates)->toBe([
        [50.8676, 4.3735],
        [50.85, 4.35],
    ]);
});

it('returns an empty route when the activity has no GPX file', function () {
    expect(Activity::factory()->create()->route_coordinates)->toBe([]);
});
