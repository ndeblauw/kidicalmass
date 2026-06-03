# Chapter Page — "local group's home" Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Rebuild the chapter detail view (`groups.show`, `/nl/chapters/{group}`) in NL on the ride/show kit as a warm "home for the local group", replacing the EN, off-kit, flat-stack page.

**Architecture:** View-only rebuild. The `GroupController@show` already passes `$group` (with `users`, `parent`, `children`, counts), `$activities` (upcoming, this group + parent regions, ordered) and `$articles` (this group + parent regions, latest) — **no controller change**. Character fields that have no schema home (identity line, team photo/role, cover, cadence, lead email, subscription) are handled per the **Build decisions** in `docs/wiki/design/30-skeleton/chapters.md` § Chapter Page: templated default identity line, shared fallback photo, initials avatars (no role), cadence dropped, NL-only (no FR toggle), region/parent node deferred to a minimal children list, faux "vrienden"+"downloads" preview, **faked** (Alpine, non-persisting) "mis geen rit" opt-in, press hide-if-empty (never faked).

**Tech Stack:** Laravel 12 · Blade · Livewire 4 (existing `ChapterVolunteerSignup`) · Alpine (faked opt-in) · Tailwind v4 + `app.css` `@layer` (appearance lives in CSS, templates carry structure only) · Pest 4.

**Constraints (project):** Public-site frontend rules — raw `<h1>`–`<h6>` (never `flux:heading`); templates keep only layout utilities (`grid/flex/gap-*/p-*/m-*/max-w-*/aspect-*/object-*`), strip `bg-*/text-{color}/font-*/rounded-*/shadow-*`; appearance in `app.css`. NL copy follows `docs/tone-of-voice.md`; **no em-dashes**. Shared working tree — **no commits** unless Frederik asks; never `git add -A`.

---

## File Structure

- **Modify** `resources/views/groups/show.blade.php` — full rewrite to the warm-arc NL structure (the whole deliverable's markup).
- **Modify** `resources/css/app.css` — append a `chapter-*` namespace block (hero photo band, next-ride hero card, team faces, notify form, extras, closing band).
- **Modify** `tests/Feature/GroupsTest.php` — replace the EN show-page expectations with NL "home" expectations; add hero / empty-state / no-badges / faux-opt-in assertions.
- **No change** `app/Http/Controllers/GroupController.php`, `app/Livewire/ChapterVolunteerSignup.php`, `resources/views/livewire/chapter-volunteer-signup.blade.php` — reused as-is.
- **Pipeline (after build):** `docs/wiki/design/30-skeleton/00-page-registry.md` (P-11 row + Roll-up) + `docs/wiki/log.md`.

---

### Task 1: Failing feature tests for the NL "home"

**Files:**
- Test: `tests/Feature/GroupsTest.php`

- [ ] **Step 1: Add the tests.** Append these to `tests/Feature/GroupsTest.php` (top `use` for `Activity`, `User` already present). They assert the new structure and the removal of the old metadata.

```php
test('chapter home leads with the next ride in NL, not metadata', function () {
    $group = Group::create(['shortname' => 'sb', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    $next = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Schaarbeek',
        'location' => 'Place Colignon',
        'begin_date' => now()->addWeek(),
        'activity_type' => 'kidicalmass',
    ]);
    $group->activities()->attach($next);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Volgende rit')
        ->assertSee('Place Colignon')
        ->assertSee('Naar de fietstocht')
        // old metadata is gone
        ->assertDontSee('activities</')
        ->assertDontSee('Part of:')
        ->assertDontSee('Organised by')
        ->assertDontSee('Subgroups');
});

test('chapter home shows a designed empty state when no upcoming ride', function () {
    $group = Group::create(['shortname' => 'nm', 'name' => 'Kidical Mass Namur', 'zip' => '5000', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Nog geen rit gepland')
        ->assertSee('Hou me op de hoogte'); // faked opt-in present
});

test('chapter home shows team names without role labels or count badges', function () {
    $group = Group::create(['shortname' => 'sb2', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    $sofie = User::factory()->create(['name' => 'Sofie Maes']);
    $group->users()->attach($sofie);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Wie dit trekt')
        ->assertSee('Sofie Maes')
        ->assertDontSee('Organiser');
});

test('chapter home hides the news block when there is no news', function () {
    $group = Group::create(['shortname' => 'sb3', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Uit de buurt');
});
```

- [ ] **Step 2: Run, expect failure.** `php artisan test --compact --filter="chapter home"` — Expected: FAIL (current view is EN; "Volgende rit" etc. absent, "Organised by"/"Part of:" present).

---

### Task 2: Rewrite the view to the warm arc (NL, kit)

**Files:**
- Modify: `resources/views/groups/show.blade.php` (full replace)

- [ ] **Step 1: Replace the file** with the structure below. Sections in order: breadcrumb+identity header → ride photo → next-ride hero (or designed empty state) → team+join → news (hide-if-empty) → faux extras → closing beat → minimal children list (parents don't break). Identity line is templated; `$gemeente` strips the "Kidical Mass " prefix; region label maps the parent name. Faux friends/downloads are clearly commented demo arrays. Both opt-ins are Alpine, non-persisting. The existing `<livewire:chapter-volunteer-signup>` and `<x-article-card>` are reused. (Full markup in the executed file — every class is either a layout utility or a `chapter-*`/`grp-*`/`link-plain` hook styled in `app.css`.)

- [ ] **Step 2: Run the Task 1 tests.** `php artisan test --compact --filter="chapter home"` — Expected: PASS (structure present, old strings gone).

- [ ] **Step 3: Run the pre-existing show test.** `php artisan test --compact --filter="group show mixes parent and direct content"` — Expected: PASS (controller untouched; it asserts `viewHas('articles'|'activities')` ordering, which still holds).

---

### Task 3: Append the `chapter-*` CSS to app.css

**Files:**
- Modify: `resources/css/app.css` (append a new block, mirroring the existing `.index-hero` / `.activity-hero*` idioms)

- [ ] **Step 1: Add styles** for: `.chapter-head` (+ `__crumb`/`__region`/`__intro`), `.chapter-photo`(+`__img`, `aspect`/`object-cover`, rounded in CSS), `.chapter-section__title`, `.chapter-next`(+`__card` solid-blue hero card, `__date`/`__title`/`__loc`/`__reassure`/`__cta` yellow button, `--empty` variant, `__more`), `.chapter-notify`(+`__input`/`__btn`/`__done`), `.chapter-team`(+`__faces`/`__face`/`__avatar` circular initials/`__name`/`__pitch*`/`__welcome`), `.chapter-news__grid`, `.chapter-extras`(+`__block`/`__friends`/`__downloads`), `.chapter-close`(+`__title`/`__back` light band). Use the brand tokens already in `app.css` (kidical blue/yellow/sky/ink). Add `[x-cloak]{display:none}` if not already global.

- [ ] **Step 2: Build assets.** `npm run build` — Expected: clean build, no Vite manifest error.

---

### Task 4: Verify render (desktop + mobile) + full quality gate

**Files:** none (verification)

- [ ] **Step 1: Screenshot** a filled group and an empty group at 1440 and 390 wide (Playwright, `ignoreHTTPSErrors`, `.cjs`) at `https://kidicalmass.test/nl/chapters/{id}`. Confirm: ride photo renders, next-ride hero is the visual climax, faces are circular initials, faux opt-in flips to "Bedankt", empty group is short+warm (no husk).
- [ ] **Step 2: Em-dash scan.** `grep -n "—" resources/views/groups/show.blade.php` — Expected: no output.
- [ ] **Step 3: Pint.** `vendor/bin/pint --dirty --format agent` — Expected: clean (only if PHP changed; tests are PHP).
- [ ] **Step 4: Full suite.** `php artisan test --compact` — Expected: all green.

---

### Task 5: Pipeline update (honesty gate)

**Files:**
- Modify: `docs/wiki/design/30-skeleton/00-page-registry.md` (P-11 row + Roll-up)
- Modify: `docs/wiki/log.md`

- [ ] **Step 1: Bump P-11.** `UX 🟠 → 🟢` (re-plan is Frederik-approved via the interview), `UI 🔴 → 🟠` (NL kit surface pass built, render-verified). **`Wire` stays 🟠** — Wire 🟢 is gated on Frederik's own critique + refine pass (honesty gate; not yet done). `Assets 🔴 → 🟠` (shared fallback photo in; per-group cover still pending). Rewrite the gap note to: built NL on the kit, warm-arc home, faked opt-in + faux extras, J2 form live; remaining = backend spec (intro/cover/role/lead-email/subscription), Frederik critique. Reconcile the Roll-up (move P-11 from "pulled out for re-think" into "surface pass done, Wire/UI 🟠, Frederik critique pending").
- [ ] **Step 2: Log.** Append a `## [YYYY-MM-DD] build | Chapter page (P-11) — NL "home" build off the re-plan` entry.
- [ ] **Step 3: Verify dashboard.** `php artisan tinker --execute '$r=app(App\Support\Build\BuildStatus::class)->report(); ...'` — Expected: P-11 parses with intended stages, `warnings` + `drift` empty.

---

## Self-Review

- **Spec coverage:** header/identity ✓ (T2), ride photo ✓ (T2/T3), next-ride hero + empty state ✓ (T1/T2), team faces no-role ✓ (T1/T2), J2 form reused ✓ (T2), news hide-if-empty + region fallback ✓ (controller + T1/T2), faux extras + press hidden ✓ (T2), faked opt-in ✓ (T1/T2), parent node minimal ✓ (T2), cadence dropped ✓ (absent), NL/FR toggle deferred ✓ (absent). All Build-decision items covered.
- **No commits** — working tree only; Frederik decides on commit. Plan-file is the superpowers artifact.
- **Types/names:** `groups.show`, `groups.index`, `activities.show`, `activities.index`, `volunteer` (confirmed in `routes/web.php`); `ChapterVolunteerSignup` mounts with `:group`.
