# Roze-hesje Living-Hub Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Re-centre the existing roze-hesje page on *living state* — what changed since last visit, rides taking shape, photos, new members — so a hesje has a reason to come back, while the static content (roster, onboarding, materiaal) recedes to naslag below.

**Architecture:** Build on the page that already ships (`resources/views/groups/roze-hesjes.blade.php` + `GroupController::rozeHesjes` + `resources/css/pages/chapters-roze-hesjes.css`). This pass reorders the page, adds three living slots (a "wat is nieuw" strook, a foto-galerij, and draft rides surfaced in the agenda with a read-only preview page), and adds a real new-member marker on the roster. Backend-gated pieces (photos upload, draft lifecycle, WhatsApp URL, a real change feed) are built as clearly-commented frontend shells with faux data — exactly the pattern the page already uses for materials — and carried as a spec for Nico ([#37](https://github.com/ndeblauw/kidicalmass/issues/37)).

**Tech Stack:** Laravel 12, Livewire/Flux, Blade, Pest 4, Tailwind v4 (token-backed role-based CSS partials), Alpine.js.

**Source briefing:** `docs/wiki/design/30-skeleton/chapters-roze-hesjes.md` → "Tweede iteratie (2026-06-15) — de levende hub".

---

## Reality check: what is real vs faux this pass

The executor MUST keep this split honest, mirroring how the materiaal tiles are already faked.

| Feature | This pass | Why |
|---|---|---|
| Hub reorder (living up top, naslag below) | **Real** | Pure composition move. |
| New-member "Nieuw" marker on roster | **Real** | `group_user.created_at` exists (`withTimestamps()`). |
| Roster role label from pivot | **Real** | `group_user.role` column exists (`pinkvest`/`captain`). |
| "Wat is nieuw" strook | **Faux feed** | No change-event system yet. |
| Draft rides in agenda + preview page | **Faux drafts** | `Activity` has no draft/lifecycle state. |
| Foto-galerij + upload | **Faux shell** | `Group` is not `HasMedia`; no group gallery. |
| WhatsApp-doorgang | **Faux link** | No `groups.whatsapp` column. |

**Faux rule:** every fabricated block carries a `{{-- FAUX … --}}` comment naming the backend dependency, identical to the existing materiaal section. No invented Eloquent fields.

**Current built section order** (verified): `roze-head` → `roze-welcome-section` (`@if $showWelcome`) → `chapter-agenda` → `roze-roster-band` → `roze-onboarding` (`#voor-je-eerste-rit`) → `roze-materials-section` (`#jouw-materiaal`). No historiek/closing exist.

**Target section order:** `roze-head` → `roze-welcome-section` → **`roze-whatsup` (wat is nieuw)** → `chapter-agenda` (+ draft sub-block) → **`roze-gallery` (foto's)** → `roze-roster-band` (+ nieuw-marker) → `roze-onboarding` → `roze-materials-section` → **`roze-whatsapp` (doorgang)**.

**Scope note:** Tasks 1–3 + 6 are the high-value frontend-now core (real reorder, real marker, faux feed). Tasks 4–5 (draft preview, foto's) are the heaviest and most backend-gated; they are independently committable and can be deferred if Frederik wants to descope until Nico ships the models.

---

## File Structure

| File | Action | Responsibility |
|------|--------|----------------|
| `resources/views/groups/roze-hesjes.blade.php` | Modify | Reorder sections; add wat-is-nieuw, foto's, agenda draft sub-block, whatsapp doorgang; nieuw-marker in roster. |
| `app/Http/Controllers/GroupController.php` | Modify | `rozeHesjes()`: pass `$newMemberCutoff`; add `ridePreview()` for the draft preview page. |
| `routes/web.php` | Modify | Register `groups.ride-preview` (membership-gated). |
| `resources/views/groups/ride-preview.blade.php` | Create | Read-only draft-ride preview with one lightweight status line. |
| `resources/css/pages/chapters-roze-hesjes.css` | Modify | Appearance for the new blocks. Tokens only. |
| `tests/Feature/RozeHesjesLivingHubTest.php` | Create | Feature tests for every task below. |
| `docs/wiki/design/30-skeleton/00-page-registry.md` + `docs/wiki/log.md` | Modify (final) | Pipeline status + log entry. |

**Test helper:** create a local `rozeMember()` helper in the new test file (don't depend on the prior file's helpers):

```php
use App\Models\Group;
use App\Models\User;
use function Pest\Laravel\actingAs;

function rozeChapterWithMember(): array
{
    $group = Group::factory()->create(['name' => 'Kidical Mass Mortsel']);
    $member = User::factory()->create(['name' => 'Saar Vermeulen']);
    $group->users()->attach($member);

    return [$group, $member];
}
```

(If `Group::factory()` needs required fields, mirror how `tests/Feature/RozeHesjesTest.php` builds its chapter — check that file before Task 1.)

---

## Task 1: Reorder the hub (living up top, naslag below)

Pure composition. Move the three naslag sections (roster, onboarding, materiaal) below the spots where the living slots will land. No appearance change yet.

**Files:**
- Modify: `resources/views/groups/roze-hesjes.blade.php`
- Test: `tests/Feature/RozeHesjesLivingHubTest.php`

- [ ] **Step 1: Write the failing order test**

```php
test('roze hub orders living content above the naslag sections', function () {
    [$group, $member] = rozeChapterWithMember();

    actingAs($member)
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertSeeInOrder([
            'Op de agenda in Mortsel',      // agenda (living) first
            'De roze hesjes van Mortsel',   // roster (naslag) after it
            'Voor je eerste rit',           // onboarding (naslag)
            'Jouw materiaal',               // materiaal (naslag) last
        ]);
});
```

- [ ] **Step 2: Run it to confirm current order already satisfies this**

Run: `php artisan test --compact --filter="orders living content"`
Expected: PASS (the built order already has agenda before roster). This test is the **guard** that the reorder in later steps does not regress the relative order. Keep it.

- [ ] **Step 3: Reserve the living slots with anchors**

In `resources/views/groups/roze-hesjes.blade.php`, immediately **after** the `@if ($showWelcome) … @endif` welcome block and **before** the `chapter-agenda` section, add an empty anchor comment (the wat-is-nieuw strook lands here in Task 3):

```blade
    {{-- LIVING SLOT A: "Wat is nieuw" strook — added in Task 3 (faux feed). --}}
```

Immediately **after** the `chapter-agenda` section's closing `</section>` and **before** the `roze-roster-band`, add:

```blade
    {{-- LIVING SLOT B: Foto-galerij — added in Task 5 (faux shell). --}}
```

- [ ] **Step 4: Run the order test again**

Run: `php artisan test --compact --filter="orders living content"`
Expected: PASS (anchors are comments; order unchanged).

- [ ] **Step 5: Commit**

```bash
git add resources/views/groups/roze-hesjes.blade.php tests/Feature/RozeHesjesLivingHubTest.php
git commit -m "refactor(roze-hesjes): reserve living-content slots, guard section order"
```

---

## Task 2: New-member marker + real roster role (REAL data)

Use the real `group_user.created_at` for a time-boxed "Nieuw" marker and the real `group_user.role` for the role label. Reuse the existing `ROZE_WELCOME_WEEKS = 2` window so "nieuw" means the same thing everywhere.

**Files:**
- Modify: `app/Http/Controllers/GroupController.php`
- Modify: `resources/views/groups/roze-hesjes.blade.php`
- Test: `tests/Feature/RozeHesjesLivingHubTest.php`

- [ ] **Step 1: Write the failing tests**

```php
test('roster marks a member who joined within the window as new', function () {
    [$group, $member] = rozeChapterWithMember();              // attached now → recent
    $old = User::factory()->create(['name' => 'Wim Oud']);
    $group->users()->attach($old);
    $group->users()->updateExistingPivot($old->id, ['created_at' => now()->subWeeks(4)]);

    $html = actingAs($member)->get(route('groups.roze-hesjes', $group))->assertOk()->getContent();

    // The recent member carries a "Nieuw" badge; the old one does not.
    expect($html)->toContain('Saar Vermeulen')->toContain('Nieuw');
    $afterOld = \Illuminate\Support\Str::after($html, 'Wim Oud');
    expect(\Illuminate\Support\Str::before($afterOld, '</li>'))->not->toContain('Nieuw');
});

test('roster shows the real pivot role label', function () {
    [$group, $member] = rozeChapterWithMember();
    $captain = User::factory()->create(['name' => 'Katrien Kapitein']);
    $group->users()->attach($captain, ['role' => 'captain']);

    actingAs($member)
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertSee('Kapitein');
});
```

- [ ] **Step 2: Run to confirm they fail**

Run: `php artisan test --compact --filter="marks a member who joined|real pivot role"`
Expected: FAIL — no "Nieuw" badge; role currently comes from a lead heuristic, not the pivot.

- [ ] **Step 3: Pass the cutoff from the controller**

In `app/Http/Controllers/GroupController.php`, inside `rozeHesjes()`, after `$roster = $group->users->sortBy('name')->values();`, add:

```php
        // A member is "nieuw" for the same window the welcome block uses (their first weeks).
        // Real data: group_user.created_at exists via withTimestamps().
        $newMemberCutoff = now()->subWeeks(self::ROZE_WELCOME_WEEKS);
```

Then add `$newMemberCutoff` to the `compact(...)` passed to the view:

```php
        return view('groups.roze-hesjes', compact('group', 'activities', 'roster', 'lead', 'showWelcome', 'newMemberCutoff'));
```

- [ ] **Step 4: Render marker + real role in the roster loop**

In `resources/views/groups/roze-hesjes.blade.php`, replace the roster `<li>` body. The role maps from the real pivot; the marker uses the pivot timestamp.

```blade
                    <li class="roze-roster__member">
                        <span class="roze-roster__avatar" aria-hidden="true">{{ $member->initials() }}</span>
                        <div class="min-w-0">
                            <strong class="roze-roster__name">{{ $member->name }}</strong>
                            <span class="roze-roster__role">{{ $member->pivot->role === 'captain' ? 'Kapitein' : 'Roze hesje' }}</span>
                        </div>
                        @if ($member->pivot->created_at && $member->pivot->created_at->greaterThan($newMemberCutoff))
                            <span class="roze-roster__new">Nieuw</span>
                        @endif
                    </li>
```

- [ ] **Step 5: Run the tests — expect PASS**

Run: `php artisan test --compact --filter="marks a member who joined|real pivot role"`
Expected: PASS (2). Recent member badged, old member not; captain shows "Kapitein".

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/GroupController.php resources/views/groups/roze-hesjes.blade.php tests/Feature/RozeHesjesLivingHubTest.php
git commit -m "feat(roze-hesjes): time-boxed new-member marker + real pivot role on roster"
```

---

## Task 3: "Wat is nieuw" strook (faux feed)

A calm strook directly under the welcome block: what changed since last visit. Faux until a real change feed exists. Items mirror the three living sources: photos, a new member, a ride moving.

**Files:**
- Modify: `resources/views/groups/roze-hesjes.blade.php`
- Test: `tests/Feature/RozeHesjesLivingHubTest.php`

- [ ] **Step 1: Write the failing test**

```php
test('roze hub shows a wat-is-nieuw strook', function () {
    [$group, $member] = rozeChapterWithMember();

    actingAs($member)
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertSee('Sinds je laatste bezoek')
        ->assertSeeInOrder(['Sinds je laatste bezoek', 'Op de agenda in Mortsel']); // strook before agenda
});
```

- [ ] **Step 2: Run to confirm it fails**

Run: `php artisan test --compact --filter="wat-is-nieuw strook"`
Expected: FAIL — "Sinds je laatste bezoek" not rendered.

- [ ] **Step 3: Replace LIVING SLOT A with the strook**

Swap the `{{-- LIVING SLOT A … --}}` comment from Task 1 for this. Copy follows tone-of-voice (warm, concrete, no em-dashes):

```blade
    {{-- LIVING SLOT A · WAT IS NIEUW — the reason to come back: what changed since last visit.
         FAUX feed until a real change-event stream exists (photos added / member joined /
         ride status moved). Backend dep: Nico #37. --}}
    @php
        $feed = [
            ['icon' => 'photo', 'text' => "Drie nieuwe foto's van de rit van vorige zondag.", 'href' => '#fotos'],
            ['icon' => 'user-plus', 'text' => 'Saar rijdt nu mee als roze hesje. Zeg eens hallo.', 'href' => '#de-roze-hesjes'],
            ['icon' => 'map', 'text' => 'De rit van 12 juli krijgt vorm: de route is gekozen.', 'href' => '#op-de-agenda'],
        ];
    @endphp
    <section class="chapter-body roze-whatsup">
        <h2 class="chapter-section__title">Sinds je laatste bezoek</h2>
        <ul role="list" class="roze-whatsup__list">
            @foreach ($feed as $item)
                <li class="roze-whatsup__item">
                    <span class="roze-whatsup__icon" aria-hidden="true">
                        <flux:icon name="{{ $item['icon'] }}" variant="solid" class="size-5" />
                    </span>
                    <a href="{{ $item['href'] }}" class="roze-whatsup__text link-plain">{{ $item['text'] }}</a>
                </li>
            @endforeach
        </ul>
    </section>
```

Add the matching anchor ids to the existing sections so the feed links resolve: on `chapter-agenda` add `id="op-de-agenda"`, on `roze-roster-band` add `id="de-roze-hesjes"`. (The `#fotos` anchor is added in Task 5.)

- [ ] **Step 4: Run the test — expect PASS**

Run: `php artisan test --compact --filter="wat-is-nieuw strook"`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add resources/views/groups/roze-hesjes.blade.php tests/Feature/RozeHesjesLivingHubTest.php
git commit -m "feat(roze-hesjes): wat-is-nieuw strook (faux change feed)"
```

---

## Task 4: Draft rides in the agenda + read-only preview page (faux)

The strategic piece: a hesje can *see* a ride taking shape. Below the real agenda, a clearly-marked "in voorbereiding" sub-block links to a read-only preview page carrying one lightweight status line ("wat moet er nog gebeuren"). Faux until `Activity` gains lifecycle state. Membership-gated like the main page; read-only (no claim).

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Http/Controllers/GroupController.php`
- Create: `resources/views/groups/ride-preview.blade.php`
- Modify: `resources/views/groups/roze-hesjes.blade.php`
- Test: `tests/Feature/RozeHesjesLivingHubTest.php`

- [ ] **Step 1: Write the failing tests**

```php
test('agenda shows an in-voorbereiding draft block linking to a preview', function () {
    [$group, $member] = rozeChapterWithMember();

    actingAs($member)
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertSee('In voorbereiding')
        ->assertSee(route('groups.ride-preview', $group), false);
});

test('ride preview is membership-gated and shows one status line, marked not-yet-final', function () {
    [$group, $member] = rozeChapterWithMember();
    $outsider = User::factory()->create();

    actingAs($member)
        ->get(route('groups.ride-preview', $group))
        ->assertOk()
        ->assertSee('Nog niet vast')             // draftness is explicit
        ->assertSee('Wat moet er nog gebeuren')  // the single status line
        ->assertSee('de communicatiekaart');     // faux next-step content

    actingAs($outsider)
        ->get(route('groups.ride-preview', $group))
        ->assertForbidden();                     // read-only, but still hesje-only
});
```

- [ ] **Step 2: Run to confirm they fail**

Run: `php artisan test --compact --filter="in-voorbereiding draft block|ride preview is membership-gated"`
Expected: FAIL — `Route [groups.ride-preview] not defined`.

- [ ] **Step 3: Register the route**

In `routes/web.php`, immediately after the `groups.roze-hesjes` route inside the `{locale}` prefix group:

```php
// Read-only preview of a chapter ride that is still in preparation (draft). Membership-gated,
// like the roze page. FAUX exemplar until Activity gains a draft/lifecycle state (Nico #37).
Route::get('chapters/{group}/rit-in-voorbereiding', [GroupController::class, 'ridePreview'])
    ->middleware(BackstageDemoAccess::class)
    ->name('groups.ride-preview');
```

- [ ] **Step 4: Add the controller method**

In `app/Http/Controllers/GroupController.php`, after `rozeHesjes()`:

```php
/**
 * Read-only preview of a ride still in preparation. A hesje may look over the captains'
 * shoulder (this is the onboarding ladder: kijken → meedoen → kapitein) but cannot act.
 * FAUX exemplar — no Activity lifecycle state exists yet (Nico #37).
 */
public function ridePreview(string $locale, Group $group): View
{
    $user = request()->user();
    abort_unless($user !== null && $group->users->contains('id', $user->id), 403);

    return view('groups.ride-preview', compact('group'));
}
```

(`Group`, `View`, and `BackstageDemoAccess` are already imported/used by `rozeHesjes()`.)

- [ ] **Step 5: Create the preview view**

`resources/views/groups/ride-preview.blade.php`. One status line, draftness explicit, warm not task-boardy. Composition utilities in the template; appearance in the partial (Task 6).

```blade
<x-layouts::site title="Rit in voorbereiding — Kidical Mass {{ $group->name }}">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    @endphp

    {{-- FAUX exemplar draft ride. Replace with a real draft Activity once lifecycle state
         lands (Nico #37). The status line is read-only for hesjes, the captains' working line. --}}
    <section class="chapter-body roze-preview">
        <a href="{{ route('groups.roze-hesjes', $group) }}" class="roze-preview__back link-plain">← Terug naar {{ $gemeente }}</a>

        <p class="roze-preview__flag">Nog niet vast</p>
        <h1>Een rit door {{ $gemeente }}</h1>
        <p class="roze-preview__when">Mogelijk <time datetime="2026-07-12">zondag 12 juli</time>, datum nog te bevestigen.</p>

        <div class="roze-preview__status">
            <strong class="roze-preview__status-title">Wat moet er nog gebeuren</strong>
            <p class="roze-preview__status-body">De route is gekozen, maar de communicatiekaart is nog niet ingevuld. Zodra die klaar is, kondigen de kapiteins de rit aan.</p>
        </div>

        <p class="roze-preview__foot">Je kijkt hier mee terwijl een kapitein deze rit voorbereidt. Benieuwd hoe dat werkt? Vraag het gerust in de groep.</p>
    </section>
</x-layouts::site>
```

- [ ] **Step 6: Add the draft sub-block to the agenda**

In `resources/views/groups/roze-hesjes.blade.php`, inside the `chapter-agenda` section, after the `@if ($activities->isNotEmpty()) … @endif` agenda list block (before `</section>`), add:

```blade
        {{-- IN VOORBEREIDING — drafts a hesje may peek at (read-only). FAUX single exemplar
             until Activity has lifecycle state (Nico #37). Onboarding-by-visibility. --}}
        <div class="roze-drafts">
            <p class="roze-drafts__label">In voorbereiding</p>
            <a href="{{ route('groups.ride-preview', $group) }}" class="roze-draft link-plain">
                <span class="roze-draft__flag" aria-hidden="true">Nog niet vast</span>
                <span class="roze-draft__title">Een rit door {{ $gemeente }} — mogelijk 12 juli</span>
                <span class="roze-draft__hint">Bekijk hoe deze rit vorm krijgt →</span>
            </a>
        </div>
```

- [ ] **Step 7: Run the tests — expect PASS**

Run: `php artisan test --compact --filter="in-voorbereiding draft block|ride preview is membership-gated"`
Expected: PASS (2). Agenda shows the draft block + preview link; preview is hesje-only and renders the single status line.

- [ ] **Step 8: Commit**

```bash
git add routes/web.php app/Http/Controllers/GroupController.php resources/views/groups/ride-preview.blade.php resources/views/groups/roze-hesjes.blade.php tests/Feature/RozeHesjesLivingHubTest.php
git commit -m "feat(roze-hesjes): draft rides in agenda + read-only preview page (faux)"
```

---

## Task 5: Foto-galerij shell + WhatsApp-doorgang (faux)

The return-reason (photos) and the deliberate hand-off to the chatter (WhatsApp). Both faux: `Group` is not `HasMedia` and has no `whatsapp` column. Build critique-able shells.

**Files:**
- Modify: `resources/views/groups/roze-hesjes.blade.php`
- Test: `tests/Feature/RozeHesjesLivingHubTest.php`

- [ ] **Step 1: Write the failing tests**

```php
test('roze hub shows a foto gallery slot above the roster', function () {
    [$group, $member] = rozeChapterWithMember();

    // Assert on an apostrophe-free fragment: the literal "'" in the template stays as "'",
    // but assertSee()'s default escaping would look for "&#039;" and miss it.
    actingAs($member)
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertSee('van het chapter')
        ->assertSeeInOrder(['van het chapter', 'De roze hesjes van Mortsel']);
});

test('roze hub shows a whatsapp doorgang at the foot', function () {
    [$group, $member] = rozeChapterWithMember();

    actingAs($member)
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertSee('WhatsApp')
        ->assertSeeInOrder(['Jouw materiaal', 'WhatsApp']); // doorgang after the naslag
});
```

- [ ] **Step 2: Run to confirm they fail**

Run: `php artisan test --compact --filter="foto gallery slot|whatsapp doorgang"`
Expected: FAIL — neither block renders.

- [ ] **Step 3: Replace LIVING SLOT B with the foto-galerij shell**

Swap the `{{-- LIVING SLOT B … --}}` comment for this. Faux thumbs use a neutral placeholder; the upload affordance is visibly disabled (no backend).

```blade
    {{-- LIVING SLOT B · FOTO'S — shared chapter album + upload. FAUX shell: Group is not yet
         HasMedia, there is no group gallery. Backend dep: Nico #37 (Group media library). --}}
    <section id="fotos" class="chapter-body roze-gallery scroll-mt-24">
        <div class="roze-gallery__head">
            <h2 class="chapter-section__title">Foto's van het chapter</h2>
            <button type="button" class="roze-gallery__upload" disabled aria-disabled="true">
                <flux:icon name="arrow-up-tray" variant="micro" class="size-4" /> Foto's toevoegen (binnenkort)
            </button>
        </div>
        <p class="roze-gallery__lead">Het gedeelde album van {{ $gemeente }}. Hier komen de foto's van onze ritten samen.</p>
        <ul role="list" class="roze-gallery__grid">
            @for ($i = 0; $i < 6; $i++)
                <li class="roze-gallery__cell" aria-hidden="true"></li>
            @endfor
        </ul>
    </section>
```

- [ ] **Step 4: Add the WhatsApp-doorgang at the foot**

In `resources/views/groups/roze-hesjes.blade.php`, after the `roze-materials-section` closing `</section>` (the last section), add:

```blade
    {{-- WHATSAPP-DOORGANG — deliberate hand-off to the chatter, kept apart from the page so
         "stand van zaken" and "gesprek" don't try to be each other. FAUX href until a per-group
         whatsapp URL exists (Nico #37). --}}
    <section class="chapter-body roze-whatsapp">
        <div class="roze-whatsapp__inner">
            <div>
                <strong class="roze-whatsapp__title">De WhatsApp-groep van {{ $gemeente }}</strong>
                <p class="roze-whatsapp__body">Voor het dagelijkse gepraat, snelle vragen en "wie kan er zondag mee".</p>
            </div>
            <a href="#" class="roze-whatsapp__btn" aria-disabled="true">Naar WhatsApp →</a>
        </div>
    </section>
```

- [ ] **Step 5: Run the tests — expect PASS**

Run: `php artisan test --compact --filter="foto gallery slot|whatsapp doorgang"`
Expected: PASS (2).

- [ ] **Step 6: Commit**

```bash
git add resources/views/groups/roze-hesjes.blade.php tests/Feature/RozeHesjesLivingHubTest.php
git commit -m "feat(roze-hesjes): foto-galerij shell + whatsapp doorgang (faux)"
```

---

## Task 6: CSS partial (appearance) for the new blocks

Append to the existing partial. Tokens only — "roze" = `--color-kidical-red`, soft/deep via `color-mix`. Reuse `.chapter-body`, `.chapter-section__title` (already imported). No raw hex/px (CssArchitectureTest enforces).

**Files:**
- Modify: `resources/css/pages/chapters-roze-hesjes.css`
- Test: `tests/Feature/CssArchitectureTest.php` (must stay green)

- [ ] **Step 1: Append the appearance rules**

Add inside the existing `@layer components { … }` block in `resources/css/pages/chapters-roze-hesjes.css`:

```css
    /* WAT IS NIEUW — calm strook of recent changes */
    .roze-whatsup__list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.6rem; }
    .roze-whatsup__item { display: flex; align-items: center; gap: 0.75rem; }
    .roze-whatsup__icon {
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        width: 2.1rem; height: 2.1rem; border-radius: 9999px;
        background-color: color-mix(in oklab, var(--color-kidical-red), white 86%);
        color: var(--color-kidical-red);
    }
    .roze-whatsup__text { color: var(--color-kidical-ink); font-weight: 600; }
    .roze-whatsup__text:hover { color: var(--color-kidical-red); }

    /* ROSTER — "Nieuw" marker */
    .roze-roster__member { position: relative; }
    .roze-roster__new {
        margin-left: auto; align-self: center;
        font-size: var(--text-xs); font-weight: 800; letter-spacing: 0.03em;
        padding: 0.15rem 0.55rem; border-radius: 9999px;
        background-color: var(--color-kidical-red); color: white;
    }

    /* IN VOORBEREIDING — draft entries under the agenda */
    .roze-drafts { margin-top: 1.25rem; }
    .roze-drafts__label {
        font-size: var(--text-sm); font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%); margin-bottom: 0.5rem;
    }
    .roze-draft {
        display: flex; flex-direction: column; gap: 0.2rem;
        background-color: white;
        border: 1px dashed color-mix(in oklab, var(--color-kidical-red), white 55%);
        border-radius: var(--radius-card, 1rem); padding: 1rem 1.25rem;
        transition: border-color 0.15s ease, transform 0.15s ease;
    }
    .roze-draft:hover { transform: translateY(-2px); border-color: var(--color-kidical-red); }
    .roze-draft__flag {
        align-self: flex-start; font-size: var(--text-xs); font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.04em;
        color: var(--color-kidical-red);
        background-color: color-mix(in oklab, var(--color-kidical-red), white 86%);
        padding: 0.1rem 0.5rem; border-radius: 9999px; margin-bottom: 0.25rem;
    }
    .roze-draft__title { font-family: var(--font-heading); font-weight: 800; color: var(--color-kidical-ink); }
    .roze-draft__hint { font-size: var(--text-sm); color: var(--color-kidical-red); font-weight: 700; }

    /* RIDE PREVIEW page */
    .roze-preview { max-width: 44rem; }
    .roze-preview__back { font-weight: 700; color: var(--color-kidical-red); }
    .roze-preview__flag {
        display: inline-block; font-size: var(--text-sm); font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-kidical-red);
        background-color: color-mix(in oklab, var(--color-kidical-red), white 86%);
        padding: 0.2rem 0.6rem; border-radius: 9999px; margin: 1rem 0 0.5rem;
    }
    .roze-preview__when { color: var(--color-text-body); margin-bottom: 1.5rem; }
    .roze-preview__status {
        background-color: color-mix(in oklab, var(--color-kidical-red), white 90%);
        border: 1px solid color-mix(in oklab, var(--color-kidical-red), white 72%);
        border-radius: var(--radius-card, 1rem); padding: 1.25rem 1.5rem;
    }
    .roze-preview__status-title { display: block; font-family: var(--font-heading); font-weight: 800; color: var(--color-kidical-ink); margin-bottom: 0.35rem; }
    .roze-preview__status-body { color: var(--color-text-body); line-height: 1.5; }
    .roze-preview__foot { margin-top: 1.5rem; font-size: var(--text-sm); color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%); }

    /* FOTO-GALERIJ — shell with disabled upload */
    .roze-gallery__head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
    .roze-gallery__upload {
        display: inline-flex; align-items: center; gap: 0.4rem;
        font-size: var(--text-sm); font-weight: 700;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
        background-color: color-mix(in oklab, var(--color-kidical-ink), transparent 92%);
        border-radius: 9999px; padding: 0.45rem 0.9rem; cursor: not-allowed;
    }
    .roze-gallery__lead { color: var(--color-text-body); margin: 0.5rem 0 1.25rem; }
    .roze-gallery__grid { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    @media (min-width: 640px) { .roze-gallery__grid { grid-template-columns: repeat(3, 1fr); } }
    .roze-gallery__cell {
        aspect-ratio: 1; border-radius: 0.85rem;
        background-color: color-mix(in oklab, var(--color-kidical-red), white 88%);
    }

    /* WHATSAPP-DOORGANG — foot hand-off */
    .roze-whatsapp__inner {
        display: flex; align-items: center; justify-content: space-between; gap: 1.25rem; flex-wrap: wrap;
        background-color: color-mix(in oklab, var(--color-kidical-blue), white 88%);
        border-radius: var(--radius-card, 1rem); padding: 1.5rem 1.75rem;
    }
    .roze-whatsapp__title { display: block; font-family: var(--font-heading); font-weight: 800; font-size: var(--text-xl); color: var(--color-kidical-ink); }
    .roze-whatsapp__body { color: var(--color-text-body); }
    .roze-whatsapp__btn { flex-shrink: 0; font-weight: 800; color: white; background-color: var(--color-kidical-blue); padding: 0.6rem 1.1rem; border-radius: 9999px; }
```

- [ ] **Step 2: Build + CSS architecture test**

Run: `npm run build && php artisan test --compact --filter=CssArchitectureTest`
Expected: build clean; CssArchitectureTest PASS (partial already registered; no raw hex/px).

- [ ] **Step 3: Commit**

```bash
git add resources/css/pages/chapters-roze-hesjes.css
git commit -m "style(roze-hesjes): appearance for wat-is-nieuw, drafts, preview, gallery, whatsapp"
```

---

## Task 7: Verification + pipeline/log + backend spec

**Files:**
- Modify: `docs/wiki/design/30-skeleton/00-page-registry.md`
- Modify: `docs/wiki/log.md`

- [ ] **Step 1: Full new-iteration suite green**

Run: `php artisan test --compact tests/Feature/RozeHesjesLivingHubTest.php`
Expected: all green (8 tests).

- [ ] **Step 2: Full suite — no regressions**

Run: `php artisan test --compact`
Expected: green. (`CalendarProximityTest` may flake order-dependently — re-run in isolation; not a regression signal.)

- [ ] **Step 3: Pint**

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean.

- [ ] **Step 4: Build**

Run: `npm run build`
Expected: clean.

- [ ] **Step 5: Screenshots (Herd-linked worktree)**

Log in as a chapter member (or rely on `BackstageDemoAccess` for the seeded demo chapter) and screenshot the roze hub (desktop + mobile) and the ride-preview page. Use a `.cjs` Playwright script with `ignoreHTTPSErrors: true`. Visually verify the new order (hero → welkom → wat-is-nieuw → agenda+draft → foto's → roster+nieuw → onboarding → materiaal → whatsapp) and the preview's single status line. **Wire stays 🟠 until Frederik's own critique/refine pass.**

- [ ] **Step 6: Pipeline status + log**

Per `/pipeline` rules, update the roze-hesje row (P-11 family) in `00-page-registry.md` — keep Wire 🟠, Back 🟠 (the new living slots are faux pending Nico). Update the row, its Top gaps cell (add "living-hub iteration: wat-is-nieuw + foto's + draft preview live as faux shells [content]/[asset]"), and the Roll-up prose. Append a `## [2026-06-15] build | roze-hesje living hub` entry to `docs/wiki/log.md`.

- [ ] **Step 7: Commit**

```bash
git add docs/wiki/design/30-skeleton/00-page-registry.md docs/wiki/log.md
git commit -m "docs(build): roze-hesje living-hub status + log entry"
```

---

## Open backend dependencies (spec for Nico — [#37](https://github.com/ndeblauw/kidicalmass/issues/37))

Faked this pass with clearly-commented demo data; wire to real data when these land:

1. **Group media library** — make `Group` implement `HasMedia` with a `gallery` collection; enable hesje upload (kapitein-moderated?). Replaces the foto-galerij shell + disabled upload. (Note: `Activity` already has a `gallery` media collection — a chapter album could alternatively aggregate from its rides' galleries; decide which.)
2. **Activity draft/lifecycle state** + a light **next-step** field — to represent a real draft ride and the single "wat moet er nog gebeuren" status line. Replaces the faux exemplar in `ride-preview.blade.php` and the agenda draft sub-block. Read-only for hesjes; editable by captains (Filament P-21).
3. **Per-group WhatsApp URL** (`groups.whatsapp` or a links table) — replaces the faux `#` href in the whatsapp doorgang.
4. **Real change feed** — events for photo-added / member-joined / ride-status-moved, to replace the faux "wat is nieuw" array. The new-member marker already runs on real `group_user.created_at`, so a "lid bijgekomen" event is the cheapest first real feed source.
