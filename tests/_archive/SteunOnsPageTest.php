<?php

// tests/Feature/SteunOnsPageTest.php
// Locks in the 2026-06-25 conversion redesign of /steun-ons:
//  - the ask is surfaced in the hero (high-intent visitors don't have to scroll),
//  - the stat deck shows live, dynamic proof numbers (groups + rides + participants),
//  - proof + load live under ONE story section (no separate headings),
//  - the page ends on a single donate ask (the duplicate white card is gone).

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use App\Models\YearStat;

beforeEach(function () {
    $this->response = $this->get('/nl/steun-ons')->assertOk();
});

it('surfaces the support ask in the hero', function () {
    // The CTA lives inside the hero band's controls slot, above the fold.
    $this->response->assertSee('page-hero__controls', false);
    $this->response->assertSeeInOrder([
        'page-hero__controls',
        __('support.ask_cta'),
        'growfunding.be',
    ], false);
});

it('shows the three live proof numbers when the data backs them', function () {
    Group::factory()->count(26)->create(['invisible' => false]);
    YearStat::factory()->create(['year' => 2025, 'participants' => 5500]);
    Activity::factory()->count(3)->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => '2025-05-01 14:00',
        'is_published' => true,
        'author_id' => User::factory(),
    ]);

    $response = $this->get('/nl/steun-ons')->assertOk();

    // Live local-groups count, NL-formatted participant headline, and the rides
    // count for the reference year — each with its dynamic label.
    $response->assertSee('26', false);
    $response->assertSee('5.500', false);
    $response->assertSee(__('support.stat_participants', ['year' => 2025]), false);
    $response->assertSee('3', false);
    $response->assertSee(__('support.stat_rides', ['year' => 2025]), false);
});

it('hides metrics with no honest source rather than showing a zero', function () {
    // Empty DB: only the always-present groups card renders (count 0); the rides
    // and participant cards are omitted entirely — no misleading "0 ritten".
    $this->response->assertDontSee(__('support.stat_rides', ['year' => now()->subYear()->year]), false);
    $this->response->assertDontSee('fietsten mee', false);
});

it('merges proof and load into one story section, dropping false claims', function () {
    // One convincing title carries both the scale and the load.
    $this->response->assertSee(__('support.story_title'), false);
    // The team's work reads as a flowing second paragraph.
    $this->response->assertSee('subsidies zoeken', false);
    // The untrue claims are gone everywhere on the page.
    $this->response->assertDontSee('zonder betaalde staf', false);
    $this->response->assertDontSee('niet door subsidies', false);
});

it('shows what support makes possible as a green-check checklist', function () {
    $this->response->assertSee(__('support.funds_title'), false);
    // Reuses the shared titled-list-block "get" variant (green checks).
    $this->response->assertSee('titled-list-block--get', false);
});

it('closes on a single donate ask, not two competing ones', function () {
    // The closing band carries the €3 ask + the free-to-ride reassurance.
    $this->response->assertSee(__('support.ask_title'), false);
    $this->response->assertSee('meefietsen blijft altijd gratis', false);
    // The duplicate white ask card is gone (one ask, not two).
    $this->response->assertDontSee('steun-ask__card', false);
    // And no competing ride closing-cta.
    $this->response->assertDontSee('Zin gekregen om mee te rijden?', false);
    $this->response->assertDontSee('Vind een rit', false);
});
