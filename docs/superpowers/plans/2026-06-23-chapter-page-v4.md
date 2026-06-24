# Chapter page v4 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Re-sequence the local chapter page (`groups.show`) from its content-type order into the intent-driven v4 arc — parade as the page's gravity — in two reviewable phases: a low-risk reorder, then layered tweaks.

**Architecture:** Pure server-rendered Blade (`resources/views/groups/show.blade.php`) fed by `GroupController@show`, styled by the page partial `resources/css/pages/chapters.css`. Phase 1 moves existing, working blocks into the new order (carousel relocates to §6) and splits the typed agenda into parade / parades-strip / other-activities zones with plain rendering. Phase 2 layers the visual tweaks (hero trim, §2 split-screen + real stat cards, §4 sky band, colouring preview, press removal, leaner ask) — each independently reviewable.

**Tech Stack:** Laravel 13, Blade, Livewire 4 (`ChapterVolunteerSignup`), Alpine (gallery lightbox + reveal — kept verbatim), Tailwind v4 tokens, Pest 4 feature tests.

**Source of truth:** Build briefing `docs/superpowers/specs/2026-06-23-chapter-page-v4-build-design.md`; skeleton + reasoning `docs/wiki/design/30-skeleton/chapters.md` (Critique v4); locked greybox `docs/superpowers/specs/assets/2026-06-23-chapter-v4-skeleton.png`.

## Global Constraints

- **Hard constraints — keep EXACTLY, reuse markup, do not rebuild:** the §5 masonry photo wall + lightbox (`chapter-gallery*` Alpine block); the team carousel (`chapter-team__carousel`); the inline volunteer signup (`livewire:chapter-volunteer-signup` + its on-demand reveal). Phase 1 only *relocates* the carousel; it does not alter its internals.
- **Press is removed from this page entirely** (Phase 2). It is NOT shown anywhere on `groups.show`; it lives on the channel-wide Press page. Never fabricate press.
- **No invented numbers.** §2 stat cards use only real/derivable data: `groups.started_at` (→ "sinds [jaar]") and a count of past `kidicalmass` activities (→ "N ritten"). No attendance/"gezinnen" figure.
- **Styling lives in partials, never `app.css`.** New CSS → `resources/css/pages/chapters.css` (page-only) or `resources/css/components/*` (reusable). Tokens only — no raw hex/px in components/blade. Enforced by `tests/Feature/CssArchitectureTest.php`.
- **Headings:** raw `<h1>`–`<h6>`, never `flux:heading`. Other `flux:*` components are fine.
- **Copy:** draft NL only; treat as placeholder (ToV polish is a separate pass). **No em-dashes** in site copy.
- **Git (shared working tree — Nico commits concurrently):** stage by explicit path, never `git add -A`; do not push `main`; commit per task.
- **Pint:** run `vendor/bin/pint --dirty --format agent` before each commit that touches PHP.
- **Tests:** `php artisan test --compact --filter=Group` (+ `--filter=CssArchitectureTest`) must stay green at each task boundary.

---

## File Structure

- **Modify** `app/Http/Controllers/GroupController.php` (`show`, lines 90-130) — split activities into rides vs other; add past-ride count + `started_at` exposure.
- **Modify** `resources/views/groups/show.blade.php` — re-sequence sections; relocate carousel; add §2/§3/§4 zone wrappers; Phase 2 markup deltas.
- **Modify** `resources/css/pages/chapters.css` — Phase 2 section styling (split-screen, sky band, colouring preview, stat cards). Already registered; no `app.css` change.
- **Modify** `tests/Feature/GroupsTest.php` — update assertions to the v4 arc; invert the press test.

---

# PHASE 1 — Reorder into the v4 zones (plain rendering)

One reviewable deliverable: the page renders in v4 section order, the parade leads, the carousel sits mid-page, with existing components and plain styling. Ship/review before Phase 2.

### Task 1: Controller — split rides from other activities

**Files:**
- Modify: `app/Http/Controllers/GroupController.php:107-129`
- Test: `tests/Feature/GroupsTest.php`

**Interfaces:**
- Produces (passed to `groups.show`): `$upcomingRides` (Collection<Activity> where `activity_type === KIDICALMASS`, ascending by `begin_date`), `$otherActivities` (Collection<Activity> of all other types, ascending), `$pastRidesCount` (int), in addition to the existing `$group, $articles, $activities, $partners, $pressArticles, $latestRide`.

- [ ] **Step 1: Write the failing test**

```php
test('controller buckets upcoming rides and other activities separately', function () {
    $author = User::factory()->create();
    $group = Group::create(['shortname' => 'sb', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()->subYears(3)]);

    $ride = Activity::create(['title_nl' => 'Parade juni', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'kidicalmass', 'begin_date' => now()->addWeek(), 'duration_minutes' => 60, 'location' => 'Place Colignon', 'author_id' => $author->id]);
    $workshop = Activity::create(['title_nl' => 'Sleutelworkshop', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'workshop', 'begin_date' => now()->addDays(3), 'duration_minutes' => 90, 'location' => 'Werkplaats', 'author_id' => $author->id]);
    $pastRide = Activity::create(['title_nl' => 'Parade mei', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'kidicalmass', 'begin_date' => now()->subMonth(), 'duration_minutes' => 60, 'location' => 'Place Colignon', 'author_id' => $author->id]);
    $ride->groups()->attach($group);
    $workshop->groups()->attach($group);
    $pastRide->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertViewHas('upcomingRides', fn ($r) => $r->count() === 1 && $r->first()->is($ride))
        ->assertViewHas('otherActivities', fn ($o) => $o->count() === 1 && $o->first()->is($workshop))
        ->assertViewHas('pastRidesCount', 1);
});
```

- [ ] **Step 2: Run it and confirm it fails**

Run: `php artisan test --compact --filter='buckets upcoming rides'`
Expected: FAIL — view has no `upcomingRides` key.

- [ ] **Step 3: Implement the split in the controller**

In `app/Http/Controllers/GroupController.php`, after the existing `$activities = ...->get();` block (ends line 112), add:

```php
$upcomingRides = $activities->where('activity_type', ActivityType::KIDICALMASS)->values();
$otherActivities = $activities->where('activity_type', '!=', ActivityType::KIDICALMASS)->values();

$pastRidesCount = Activity::query()
    ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
    ->where('activity_type', ActivityType::KIDICALMASS)
    ->where('begin_date', '<', now())
    ->count();
```

Then extend the final `compact(...)` (line 129):

```php
return view('groups.show', compact(
    'group', 'articles', 'activities', 'partners', 'pressArticles', 'latestRide',
    'upcomingRides', 'otherActivities', 'pastRidesCount',
));
```

(`ActivityType` is already imported — it's used at line 123.)

- [ ] **Step 4: Run it and confirm it passes**

Run: `php artisan test --compact --filter='buckets upcoming rides'`
Expected: PASS.

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/GroupController.php tests/Feature/GroupsTest.php
git commit -m "feat(chapter): bucket upcoming rides, other activities, past-ride count for v4 arc"
```

---

### Task 2: Blade — re-sequence into the v4 order, relocate the carousel

**Files:**
- Modify: `resources/views/groups/show.blade.php`
- Test: `tests/Feature/GroupsTest.php`

**Interfaces:**
- Consumes: `$upcomingRides`, `$otherActivities` (Task 1); existing `$group`, `$activities`, `$latestRide`, `$partners`, `$pressArticles`, `$team` (built in-view), the kept `chapter-gallery`, `chapter-team__carousel`, and `chapter-join` blocks.

**Target section order (top → bottom):**

```
§1 hero            — <header class="chapter-head ...">                      (unchanged, in place)
§2 next parade     — first $upcomingRides item (plain card markup)         + empty-ride state when none
§3 alle parades    — remaining $upcomingRides as a strip (plain rows)       + "alle ritten (ook voorbije) →"
§4 ook in gemeente — $otherActivities (plain rows, existing x-ride-day)
§5 gallery         — existing chapter-gallery block                         (unchanged, in place)
§6 wie zijn wij    — the chapter-team__carousel block, RELOCATED here       (markup unchanged)
§7 help mee        — chapter-join block, kept in <x-slot:closing>           (carousel removed from the slot)
§8 affiches+vrienden — existing chapter-extras block (press/partners/downloads), repositioned as the quiet tail
   closing handback
```

Phase 1 keeps plain rendering: §2/§3/§4 use the existing day-grouped `<x-ride-day>` rows the agenda already uses (so accent CSS vars still emit). The featured split-screen, sky band, and stat cards are Phase 2.

- [ ] **Step 1: Update the agenda test to the new lead heading (write the failing assertion)**

Replace the body of `test('chapter home leads with the next ride in NL, not metadata', ...)` (lines 270-294) assertions block with:

```php
    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('De volgende parade')     // §2 heading — parade leads the page
        ->assertSee('Place Colignon')         // the ride's venue
        ->assertDontSee('Part of:')
        ->assertDontSee('Organised by')
        ->assertDontSee('Subgroups');
```

- [ ] **Step 2: Run the affected tests and confirm the heading assertion fails**

Run: `php artisan test --compact --filter='leads with the next ride'`
Expected: FAIL — "De volgende parade" not found (page still says "Op de agenda").

- [ ] **Step 3: Add the §2/§3/§4 zone markup**

Replace the current single agenda `<section class="chapter-body chapter-agenda">…</section>` (lines ~116-149) with three sections. Reuse the existing `@php $agendaByDay = …` grouping per zone. Insert:

```blade
{{-- 2 · DE VOLGENDE PARADE — the soonest kidicalmass ride leads. Phase 1: plain card.
     Phase 2 turns this into the split-screen (left parade / right stat cards). --}}
<section class="chapter-body chapter-parade">
    <h2 class="chapter-section__title">De volgende parade in {{ $gemeente }}</h2>
    @if ($upcomingRides->isNotEmpty())
        @php $nextRide = $upcomingRides->first(); @endphp
        <x-ride-day
            :period-key="$nextRide->begin_date->format('Y-m-d')"
            :commune="$gemeente"
            :rows="[['item' => $nextRide]]" />
        {{-- built-in subscribe CTA (Phase 1: reuse the existing opt-in component) --}}
        <x-newsletter-optin :group="$group" :show-join="false" class="chapter-parade__optin" />
    @else
        <div class="chapter-next__card chapter-next__card--empty">
            <p class="chapter-next__empty-lead">Nog geen fietstocht gepland.</p>
            <p class="chapter-next__empty-body">We laten het je weten zodra {{ $gemeente }} vertrekt. Schrijf je hieronder in.</p>
            <x-newsletter-optin :group="$group" :show-join="false" class="chapter-parade__optin" />
        </div>
    @endif
</section>

{{-- 3 · ALLE PARADES — the remaining upcoming rides as a compact strip, paired under §2. --}}
@if ($upcomingRides->count() > 1)
    <section class="chapter-body chapter-parades-strip">
        <h2 class="chapter-section__title">Alle parades</h2>
        <div class="chapter-agenda__list">
            @foreach ($upcomingRides->slice(1)->groupBy(fn ($a) => $a->begin_date->format('Y-m-d')) as $periodKey => $dayRides)
                <x-ride-day :period-key="$periodKey" :commune="$gemeente" :rows="$dayRides->map(fn ($a) => ['item' => $a])->values()->all()" />
            @endforeach
        </div>
        <a href="{{ $allActivitiesUrl }}" class="link-plain chapter-parades-strip__all">Alle ritten (ook voorbije) →</a>
    </section>
@endif

{{-- 4 · OOK IN GEMEENTE — workshops, filmavonden, meetings. Phase 1: plain rows.
     Phase 2: the big "sky" band of activity cards. --}}
@if ($otherActivities->isNotEmpty())
    <section class="chapter-body chapter-other">
        <h2 class="chapter-section__title">Ook in {{ $gemeente }}</h2>
        <div class="chapter-agenda__list">
            @foreach ($otherActivities->groupBy(fn ($a) => $a->begin_date->format('Y-m-d')) as $periodKey => $dayActs)
                <x-ride-day :period-key="$periodKey" :commune="$gemeente" :rows="$dayActs->map(fn ($a) => ['item' => $a])->values()->all()" />
            @endforeach
        </div>
    </section>
@endif
```

Delete the now-redundant standalone `chapter-optin` section (the old §3a band, lines ~151-158) — the subscribe CTA now lives inside §2.

- [ ] **Step 4: Relocate the carousel to §6**

Cut the entire carousel block — from `<div class="chapter-team__carousel" …>` to its matching `</div>` (the `@if ($team->isNotEmpty())` wrapper, lines ~448-497) — out of `<x-slot:closing>`. Paste it immediately AFTER the `@if ($hasRideGallery) … @endif` gallery section (so it renders as §6, after the photo wall, before the closing slot), wrapped in a section:

```blade
{{-- 6 · WIE ZIJN WIJ — the team carousel, relocated here (was in the closing band).
     Markup unchanged. Faces meet the newcomer BEFORE the recruitment ask in §7. --}}
@if ($team->isNotEmpty())
    <section class="chapter-body chapter-team">
        {{-- (paste the existing chapter-team__carousel block verbatim here) --}}
    </section>
@endif
```

Leave the `chapter-join` ("help mee") block in `<x-slot:closing>` as §7, and the `chapter-seam-illo` with it. The closing slot now holds only §7.

- [ ] **Step 5: Reposition the extras block as §8**

Move the `@if ($hasExtras) <section class="chapter-body chapter-body--tail">…</section> @endif` block (lines ~341-429) so it sits AFTER §6 and BEFORE the `<x-slot:closing>` (i.e. it becomes the last white band before the yellow closing). No markup change inside — press/partners/downloads stay as-is in Phase 1 (press removal is Task 8).

- [ ] **Step 6: Run the Group + Css suites and confirm green**

Run: `php artisan test --compact --filter=Group && php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS. Verify especially: `leads with the next ride` (now "De volgende parade"), `team carousel shows member cards` (still finds "Wij zwaaien je welkom" + `chapter-team__card` mid-page), `empty state when no upcoming ride` ("Nog geen fietstocht gepland" + "Mis geen rit"), workshop/meeting tests (now render under §4, accents still emit).

- [ ] **Step 7: Visual smoke + commit**

Render Schaarbeek (`/nl/chapters/3`), Anderlecht (workshop), Brussel-Stad (meeting), and a just-started group; confirm the order is hero → parade → parades → ook in → gallery → carousel → extras → help mee, nothing broken.

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/groups/show.blade.php tests/Feature/GroupsTest.php
git commit -m "refactor(chapter): re-sequence show.blade into v4 order, relocate carousel to mid-page"
```

**>>> PHASE 1 REVIEW GATE — stop, review the reordered page with Frederik before Phase 2. <<<**

---

# PHASE 2 — Tweaks (each independently reviewable)

Layer the section-level changes onto the reordered page. Order is flexible; each task is its own commit and review.

### Task 3: Hero — mission line only

**Files:** Modify `resources/views/groups/show.blade.php` (the `chapter-head` header); Test `tests/Feature/GroupsTest.php`.

The hero must show only the name + one warm what/why line + the photo. No micro-proof, no next-parade line, no press.

- [ ] **Step 1: Write the failing test**

```php
test('chapter hero is mission intro only — no stats, no press', function () {
    $group = Group::create(['shortname' => 'sb', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()->subYears(3)]);
    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Wij fietsen samen met kinderen door Schaarbeek')
        ->assertDontSee('ritten sinds')   // micro-proof is NOT in the hero
        ->assertDontSee('In de pers');    // no press trust line in the hero
});
```

- [ ] **Step 2: Run it; confirm pass/fail honestly**

Run: `php artisan test --compact --filter='hero is mission intro only'`
Expected: FAIL if any stat/press text is in the hero; if the hero is already clean, add the mission line first, then it passes.

- [ ] **Step 3: Set the hero copy**

In `<header class="chapter-head …">`, under the `<h1 class="page-hero__title">`, ensure a single intro paragraph and nothing else:

```blade
<p class="page-hero__lead">Wij fietsen samen met kinderen door {{ $gemeente }} — veilig, vrolijk, op kindertempo.</p>
```

Remove any zip/eyebrow stat or press line from the header if present. (NB: replace the en-dash in the copy with a comma during the ToV pass — no em/en-dashes in final copy.)

- [ ] **Step 4: Run it; confirm pass.** Run: `php artisan test --compact --filter='hero is mission intro only'` → PASS.

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/groups/show.blade.php tests/Feature/GroupsTest.php
git commit -m "feat(chapter): hero is mission-line only (v4)"
```

---

### Task 4: §2 split-screen + real stat cards

**Files:** Modify `resources/views/groups/show.blade.php` (§2); Modify `resources/css/pages/chapters.css`; Test `tests/Feature/GroupsTest.php`.

**Interfaces:** Consumes `$upcomingRides`, `$pastRidesCount`, `$group->started_at`.

Left column = the parade (the `<x-ride-day>` featured row + the subscribe CTA). Right column = two stat cards: "sinds {year}" and "{N} ritten". No photo, no map.

- [ ] **Step 1: Write the failing test**

```php
test('chapter parade split shows real stat cards (sinds + ritten), no fake numbers', function () {
    $author = User::factory()->create();
    $group = Group::create(['shortname' => 'sb', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()->setDate(2023, 1, 1)]);
    foreach ([now()->subMonths(2), now()->subMonth()] as $when) {
        $r = Activity::create(['title_nl' => 'Parade', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'kidicalmass', 'begin_date' => $when, 'duration_minutes' => 60, 'location' => 'Place Colignon', 'author_id' => $author->id]);
        $r->groups()->attach($group);
    }
    $next = Activity::create(['title_nl' => 'Parade', 'title_fr' => 'x', 'content_nl' => 'x', 'content_fr' => 'x', 'activity_type' => 'kidicalmass', 'begin_date' => now()->addWeek(), 'duration_minutes' => 60, 'location' => 'Place Colignon', 'author_id' => $author->id]);
    $next->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('sinds 2023')
        ->assertSee('2 ritten')          // two past rides counted
        ->assertDontSee('gezinnen');     // no invented attendance figure
});
```

- [ ] **Step 2: Run it; confirm it fails.** Run: `php artisan test --compact --filter='real stat cards'` → FAIL (no stat markup).

- [ ] **Step 3: Add the split markup to §2**

Wrap the §2 body in a two-column layout; left holds the existing featured `<x-ride-day>` + the `<x-newsletter-optin>`; add the right column:

```blade
<div class="chapter-parade__split">
    <div class="chapter-parade__main">
        {{-- existing featured <x-ride-day> + <x-newsletter-optin> from Phase 1 --}}
    </div>
    <aside class="chapter-parade__proof">
        <div class="chapter-stat">
            <span class="chapter-stat__num">sinds {{ $group->started_at?->format('Y') ?? '2023' }}</span>
        </div>
        <div class="chapter-stat">
            <span class="chapter-stat__num">{{ $pastRidesCount }} {{ $pastRidesCount === 1 ? 'rit' : 'ritten' }}</span>
            <span class="chapter-stat__label">samen gefietst</span>
        </div>
    </aside>
</div>
```

- [ ] **Step 4: Style the split in the page partial**

In `resources/css/pages/chapters.css`, add `.chapter-parade__split` (responsive two-column: parade ~1.2fr, proof ~1fr; stacks on mobile), `.chapter-parade__proof`, `.chapter-stat` (token-backed surface — `var(--color-*)`, `var(--radius-*)`, no raw hex/px). Keep within the existing `@layer`.

- [ ] **Step 5: Run tests + CssArchitecture**

Run: `php artisan test --compact --filter='real stat cards' && php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS (CssArchitecture confirms no raw hex/px slipped into blade/components).

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/groups/show.blade.php resources/css/pages/chapters.css tests/Feature/GroupsTest.php
git commit -m "feat(chapter): §2 parade split-screen with real sinds/ritten stat cards"
```

---

### Task 5: §4 — big "sky" band of activity cards

**Files:** Modify `resources/views/groups/show.blade.php` (§4); Modify `resources/css/pages/chapters.css`.

Promote "Ook in {gemeente}" to its own full-bleed sky band; render `$otherActivities` as activity cards (not the plain day rows). Keep it clearly lighter than the parade but with real presence.

- [ ] **Step 1: Wrap §4 in the band + card grid**

```blade
<section class="chapter-body chapter-other chapter-other--sky">
    <h2 class="chapter-section__title">Ook in {{ $gemeente }}</h2>
    <ul class="chapter-other__grid" role="list">
        @foreach ($otherActivities as $activity)
            <li class="chapter-other__card">
                <span class="chapter-other__type">{{ $activity->activity_type->label() }}</span>
                <h3 class="chapter-other__title">{{ $activity->title_nl }}</h3>
                <time datetime="{{ $activity->begin_date->toDateString() }}">{{ $activity->begin_date->isoFormat('ddd D MMM') }}</time>
                @if ($activity->location)<p class="chapter-other__loc">{{ $activity->location }}</p>@endif
            </li>
        @endforeach
    </ul>
</section>
```

(Confirm `ActivityType` has a `label()` accessor; if not, use a `match` in-view or add one. Check `app/Enums/ActivityType.php` first.)

- [ ] **Step 2: Update the workshop/meeting tests for the card markup**

The existing `--ride-accent` assertions in `labels a workshop` / `accents a meeting` (lines 339, 360) target the old row component. Update them to assert the card content instead:

```php
->assertSee('Fietscheck en sleutelworkshop')   // workshop test
->assertSee('Ook in')                           // it lives in the §4 band now
->assertDontSee('Naar de fietstocht');
```

(and the analogous edit for the meeting test — assert `Vrijwilligersmeeting` + `Ook in`, drop the `--ride-accent` assertion.)

- [ ] **Step 3: Style the sky band**

Add `.chapter-other--sky` (full-bleed band using the existing band mechanism + `var(--color-kidical-sky)` tokens) and `.chapter-other__grid` / `__card` in `chapter-pages.css`, tokens only. Match the existing full-bleed band pattern already used on the page (check how `chapter-team-band` / closing bands go full width to avoid the horizontal-scroll bug fixed in commit a526452).

- [ ] **Step 4: Run Group + Css suites → PASS.** `php artisan test --compact --filter=Group && php artisan test --compact --filter=CssArchitectureTest`

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/groups/show.blade.php resources/css/pages/chapters.css tests/Feature/GroupsTest.php
git commit -m "feat(chapter): §4 'ook in gemeente' sky band of activity cards"
```

---

### Task 6: §5 — colouring download with preview thumbnail

**Files:** Modify `resources/views/groups/show.blade.php` (near §5); Modify `resources/css/pages/chapters.css`.

Keep the colouring download (faux for now), shown as a pretty link with a preview thumbnail beside the §5 gallery.

- [ ] **Step 1: Add the colouring download block** after the gallery section:

```blade
<aside class="chapter-colouring">
    <img src="{{ asset('img/downloads/kleurplaat-preview.png') }}" alt="Voorbeeld van de kleurplaat" class="chapter-colouring__preview">
    <div>
        <h3 class="chapter-colouring__title">Kleurplaat voor onderweg</h3>
        <a href="#" class="cta-button cta-button--secondary chapter-colouring__link">Download (PDF)</a>
    </div>
    {{-- FAUX: real per-group download source pending Nico (#37). --}}
</aside>
```

- [ ] **Step 2: Style `.chapter-colouring*`** in `chapter-pages.css` (thumbnail + label row, token-backed). If `img/downloads/kleurplaat-preview.png` does not exist, add a placeholder asset or point at an existing illustration so the `<img>` resolves.

- [ ] **Step 3: Smoke-render Schaarbeek**, confirm the preview shows and the link is obvious.

- [ ] **Step 4: Pint (if PHP touched) + commit**

```bash
git add resources/views/groups/show.blade.php resources/css/pages/chapters.css
git commit -m "feat(chapter): §5 colouring download with preview thumbnail"
```

---

### Task 7: §8 — remove press, keep affiches + "met dank aan"

**Files:** Modify `resources/views/groups/show.blade.php` (§8 extras); Test `tests/Feature/GroupsTest.php`.

Press is removed from the chapter page entirely. Keep partners (renamed heading "Met dank aan") + downloads/affiches.

- [ ] **Step 1: Invert the press test (do not delete it — repurpose, per project rule)**

Replace the body of `test('chapter page shows press articles linked to the group', …)` (lines 386-403). Rename and invert:

```php
test('chapter page no longer shows press — it moved to the channel Press page', function () {
    $group = Group::create(['shortname' => 'mol', 'name' => 'Kidical Mass Mol', 'zip' => '2400', 'invisible' => false, 'started_at' => now()]);
    $article = PressArticle::factory()->create(['title_nl' => 'Gezinnen fietsen door Mol', 'title_fr' => 'x', 'outlet' => 'Het Nieuwsblad', 'url' => null, 'published_at' => now()->subMonths(2)]);
    $article->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('In de pers')
        ->assertDontSee('Het Nieuwsblad')
        ->assertDontSee('Gezinnen fietsen door Mol');
});
```

- [ ] **Step 2: Update the partner test heading**

In `test('chapter page shows visible local partners', …)` change `->assertSee('Vrienden van de groep')` to `->assertSee('Met dank aan')`.

- [ ] **Step 3: Run; confirm both fail** (press still renders; heading still "Vrienden van de groep").

Run: `php artisan test --compact --filter='no longer shows press' && php artisan test --compact --filter='visible local partners'`
Expected: FAIL.

- [ ] **Step 4: Edit §8 markup**

In the `chapter-extras` block: delete the entire `@if ($pressArticles->isNotEmpty()) … @endif` press sub-block. Rename the partners heading from "Vrienden van de groep" to "Met dank aan". Keep the downloads (affiches) sub-block. Update `$hasExtras` so it no longer considers `$pressArticles` (extras show when partners OR downloads exist).

- [ ] **Step 5: Run; confirm pass.** Run: `php artisan test --compact --filter=Group` → PASS.

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/groups/show.blade.php tests/Feature/GroupsTest.php
git commit -m "feat(chapter): remove press from chapter page, rename partners to 'met dank aan'"
```

---

### Task 8: §7 — leaner help-mee ask

**Files:** Modify `resources/views/groups/show.blade.php` (`chapter-join` in the closing slot).

Faces already moved to §6 (Phase 1). Ensure §7 is a lean recruitment ask: headline + one warm line naming the team + the on-demand reveal button (the kept Livewire form). Trim any duplicated faces/role chrome from the closing band.

- [ ] **Step 1:** Confirm the closing slot now contains only the `chapter-join` block (carousel already relocated). Tighten the pitch copy to reference the team by context; keep the `<x-cta-button … x-on:click="open = true">` reveal and `<livewire:chapter-volunteer-signup :group="$group" />` untouched.

- [ ] **Step 2: Smoke-test the reveal** — load `/nl/chapters/3?intent=volunteer`, confirm the form auto-opens; load without the param, confirm it is collapsed behind the button.

- [ ] **Step 3: Commit**

```bash
git add resources/views/groups/show.blade.php
git commit -m "feat(chapter): §7 leaner help-mee ask (faces now live in §6)"
```

---

### Task 9: Final verification + pipeline

**Files:** Modify `docs/wiki/design/30-skeleton/00-page-registry.md` (P-11 row), `docs/wiki/log.md`.

- [ ] **Step 1: Full gate**

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact --filter=Group
php artisan test --compact --filter=CssArchitectureTest
npm run build
```
Expected: all green; build clean.

- [ ] **Step 2: Full-page visual check** on four groups — Schaarbeek (filled), Anderlecht (workshop, no ride → §2 empty state), Brussel-Stad (meeting), and a just-started group (sparse). Confirm the v4 arc and that hide-if-empty zones vanish cleanly.

- [ ] **Step 3: Update the build pipeline** for P-11 per the `/pipeline` flow — bump the row stages (Wire stays 🟠 until Frederik's own critique pass), trim resolved Top-gaps, reconcile the roll-up, and append a `## [YYYY-MM-DD] build | …` entry to `docs/wiki/log.md`. Verify the `/build` dashboard parses with no warnings/drift.

- [ ] **Step 4: Commit the docs**

```bash
git add docs/wiki/design/30-skeleton/00-page-registry.md docs/wiki/log.md
git commit -m "docs(chapter): record P-11 v4 rebuild in registry + log"
```

---

## Self-Review notes (carried for the executor)

- **Spec coverage:** hero trim (Task 3), §2 split + real stat cards (Task 4), §3 strip (Task 2), §4 sky band (Task 5), §5 gallery kept + colouring preview (Task 6), §6 carousel relocated (Task 2), §7 leaner ask (Task 8), §8 press removed + sponsors (Task 7), two-phase build (Phase 1 / Phase 2 split) — all present.
- **Kept-verbatim guarantee:** the gallery Alpine block, the carousel internals, and the Livewire form are never edited — only relocated/wrapped (Tasks 2, 8).
- **No invented numbers:** stat cards are `started_at` + counted past rides only (Task 4); attendance explicitly excluded.
- **Press:** fully removed and the test inverted, not deleted (Task 7).
- **Open data deps for Nico (not blockers):** `group_user.role` + photos (faces still faux-roled), per-group subscription model (subscribe CTA still client-side), download categorisation + real colouring source, per-group lead email (#37). These stay faked-but-visible, clearly commented.
