<?php

use App\Http\Controllers\LegacyRedirectController;
use Illuminate\Support\Facades\Route;

/*
 * Legacy Wix URLs → new site, all 301 (docs/wiki/design/26-redirect-map.md, D-7).
 * Old paths are language-neutral; LegacyRedirectController resolves the visitor's
 * locale. Unmapped legacy paths intentionally 404 — never masked to home.
 * Path set verified against the live kidicalmass.be sitemap.xml on 2026-07-07.
 */

// Static pages: legacy path → route name (optionally with #fragment).
foreach ([
    // National & content.
    'le-projet-het-project' => 'about.mission',
    'organisation' => 'about.organisation',
    'what-we-want' => 'about.vision',
    'nos-revendications-onze-aanbevelingen' => 'about.vision',
    'volunteer' => 'volunteer',
    'jobs' => 'volunteer',
    'help-je-n-ai-pas-de-vélo' => 'getting-started#no-bike',
    'activités-vélo-fietsactiviteiten-kids' => 'getting-started',
    'en-image-in-beeld' => 'home',
    'downloads' => 'about',
    // Events — critical: Facebook links land on /agenda. The three edition pages
    // point at the calendar until real events are seeded (D-7 hand-off item 2).
    'agenda' => 'activities.index',
    'event-list' => 'activities.index',
    '2026' => 'activities.index',
    'bxltour2026' => 'activities.index',
    'grande-grote-kidical-mass-2025' => 'activities.index',
    'grande-kidical-2024' => 'activities.index',
    '2023' => 'activities.index',
    // Chapters.
    'all-groups' => 'groups.index',
    'bruxelles' => 'groups.index',
    'wallonie' => 'groups.index',
    '1330' => 'groups.index', // Rixensart — no chapter in the new structure.
    // News, press & shop.
    'my-blog' => 'articles.index',
    'press' => 'about.press',
    'interview-fr' => 'about.press',
    'category/all-products' => 'membership',
] as $legacyPath => $target) {
    Route::get($legacyPath, LegacyRedirectController::class)->defaults('target', $target);
}

// Wildcards: blog posts have no per-slug counterpart yet (blanket → news feed,
// D-7 hand-off item 4); Wix tag pages and shop products are dropped.
Route::get('post/{slug}', LegacyRedirectController::class)->defaults('target', 'articles.index');
Route::get('my-blog/hashtags/{tag}', LegacyRedirectController::class)->defaults('target', 'articles.index');
Route::get('product-page/{product}', LegacyRedirectController::class)->defaults('target', 'membership');

// Chapter postal-code pages — resolved to the matching chapter at request time.
foreach (['1000', '1030', '1040', '1050', '1060', '1070', '1080', '1090', '1120', '1170', '1190', '5000', '7000'] as $zip) {
    Route::get($zip, LegacyRedirectController::class)->defaults('zip', $zip);
}

// Combined Wix pages → the chapter's canonical postal: Koekelberg carries
// 1081-82-83, Woluwe carries 1150-1200 (D-7 hand-off item 3).
foreach ([
    '1081-82-83' => '1081',
    '1081' => '1081',
    '1082' => '1081',
    '1083' => '1081',
    '1150-1200' => '1200',
    '1150' => '1200',
    '1200' => '1200',
] as $legacyPath => $zip) {
    Route::get($legacyPath, LegacyRedirectController::class)->defaults('zip', $zip);
}
