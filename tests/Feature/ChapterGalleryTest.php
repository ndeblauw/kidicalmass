<?php

use App\Models\Group;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('media');
});

function attachGalleryPhotos(Group $group, int $n): void
{
    for ($i = 0; $i < $n; $i++) {
        $group->addMedia(UploadedFile::fake()->image("photo-{$i}.jpg", 800, 600))
            ->usingName("photo-{$i}")
            ->toMediaCollection('gallery');
    }
}

function showChapter(Group $group)
{
    return test()->get(route('groups.show', ['locale' => 'nl', 'group' => $group->id]));
}

it('renders the gallery band with one tile per non-cover photo', function () {
    $group = Group::factory()->create();
    attachGalleryPhotos($group, 3); // 1 cover + 2 in the band

    $response = showChapter($group)->assertOk();

    $response->assertSee('chapter-gallery__grid', false);
    expect(substr_count($response->getContent(), 'chapter-gallery__tile'))->toBe(2);
});

it('uses the first gallery photo as the cover instead of the fallback', function () {
    $group = Group::factory()->create();
    attachGalleryPhotos($group, 2);

    showChapter($group)
        ->assertOk()
        ->assertDontSee('ride-cinquantenaire-crowd.jpg'); // fallback art is gone
});

it('keeps the fallback cover and omits the band when there are no photos', function () {
    $group = Group::factory()->create();

    showChapter($group)
        ->assertOk()
        ->assertSee('ride-cinquantenaire-crowd.jpg')   // fallback stays
        ->assertDontSee('chapter-gallery__grid', false); // no band
});

it('omits the band when there is only a cover photo', function () {
    $group = Group::factory()->create();
    attachGalleryPhotos($group, 1);

    showChapter($group)
        ->assertOk()
        ->assertDontSee('chapter-gallery__grid', false)
        ->assertDontSee('ride-cinquantenaire-crowd.jpg'); // cover still swapped
});
