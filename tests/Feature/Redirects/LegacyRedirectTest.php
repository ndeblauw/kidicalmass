<?php

use App\Models\Group;

use function Pest\Laravel\get;

/**
 * Legacy Wix URLs must 301 to the new site (docs/wiki/design/26-redirect-map.md,
 * closes D-7). The dataset samples every section of the map; postal-code pages
 * resolve to the matching chapter at request time.
 */
it('301s a legacy Wix path to its new page', function (string $old, string $new) {
    get($old)->assertMovedPermanently()->assertRedirect($new);
})->with([
    'agenda (every Facebook link)' => ['/agenda', '/nl/events'],
    'event list' => ['/event-list', '/nl/events'],
    'season page' => ['/2026', '/nl/events'],
    'bxl tour 2026' => ['/bxltour2026', '/nl/events'],
    'grande kidical 2025' => ['/grande-grote-kidical-mass-2025', '/nl/events'],
    'project page' => ['/le-projet-het-project', '/nl/about/mission'],
    'organisation' => ['/organisation', '/nl/about/organisation'],
    'demands merged into vision' => ['/what-we-want', '/nl/about/vision'],
    'recommendations merged into vision' => ['/nos-revendications-onze-aanbevelingen', '/nl/about/vision'],
    'volunteer' => ['/volunteer', '/nl/help-out'],
    'jobs folded into help-out' => ['/jobs', '/nl/help-out'],
    'no-bike FAQ anchor' => ['/help-je-n-ai-pas-de-vélo', '/nl/getting-started#no-bike'],
    'kids bike activities' => ['/activités-vélo-fietsactiviteiten-kids', '/nl/getting-started'],
    'gallery dropped to home' => ['/en-image-in-beeld', '/nl'],
    'downloads to about hub' => ['/downloads', '/nl/about'],
    'blog index' => ['/my-blog', '/nl/about/news'],
    'blog post (blanket until posts migrate)' => ['/post/kidical-mass-bike-brussels', '/nl/about/news'],
    'blog tag page' => ['/my-blog/hashtags/party', '/nl/about/news'],
    'press' => ['/press', '/nl/about/press'],
    'interview absorbed into press' => ['/interview-fr', '/nl/about/press'],
    'shop product' => ['/product-page/t-shirt-kidical-msss', '/nl/steun-ons'],
    'shop category' => ['/category/all-products', '/nl/steun-ons'],
    'chapters overview' => ['/all-groups', '/nl/chapters'],
    'bruxelles cluster' => ['/bruxelles', '/nl/chapters'],
    'wallonie region' => ['/wallonie', '/nl/chapters'],
    'retired commune (Rixensart)' => ['/1330', '/nl/chapters'],
]);

it('301s a postal-code page to the matching chapter', function () {
    $group = Group::factory()->create(['zip' => '1080']);

    get('/1080')->assertMovedPermanently()
        ->assertRedirect(route('groups.show', ['locale' => 'nl', 'group' => $group]));
});

it('301s a combined postal page to its canonical chapter', function () {
    $koekelberg = Group::factory()->create(['zip' => '1081']);
    $woluwe = Group::factory()->create(['zip' => '1200']);

    get('/1081-82-83')->assertMovedPermanently()
        ->assertRedirect(route('groups.show', ['locale' => 'nl', 'group' => $koekelberg]));
    get('/1150-1200')->assertMovedPermanently()
        ->assertRedirect(route('groups.show', ['locale' => 'nl', 'group' => $woluwe]));
});

it('falls back to the chapters overview when no chapter matches the postal code', function () {
    get('/1080')->assertMovedPermanently()->assertRedirect('/nl/chapters');
});

it('404s unmapped legacy paths instead of masking them to home', function () {
    get('/some-forgotten-wix-page')->assertNotFound();
});
