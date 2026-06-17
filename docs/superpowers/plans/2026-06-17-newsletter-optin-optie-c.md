# Newsletter opt-in ("Optie C") Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add the "Optie C — Voordelen + formulierkaart" newsletter call-to-action to the `/events` calendar sidebar and to every chapter page, as one reusable Blade component with three states (opt-in / thanks / already-on-board).

**Architecture:** A single token-styled Blade component `<x-newsletter-optin>` (no Livewire, no backend — a styled placeholder). Guests see the Optie C card with an email form that toggles to a thank-you via Alpine; authenticated visitors ("already subscribed") see a "manage your preferences" panel instead of a form. The component adapts column→row via a Tailwind container query, so the narrow events sidebar stacks while the wide chapter spot goes two-column. It replaces the existing yellow "Mis geen rit" sidebar card and the chapter empty-state email form.

**Tech Stack:** Laravel 12, Blade components, Alpine.js, Tailwind CSS v4 (container queries + `@theme` tokens), Flux UI (`flux:icon.check-circle`), Pest 4.

## Global Constraints

- Public-site frontend rules (CLAUDE.md): headings use raw `<h1>`–`<h6>`, never `flux:heading`. Other `flux:*` components are fine.
- Component appearance + internal spacing = token-backed Tailwind utilities baked into the component's `.blade.php`. No `app.css`/partial entry for this component.
- Never a raw hex or `px` value in a `.blade.php` component (no `[color:#fff]`, no `min-h-[60px]`, no inline `style=""` with hex/px). Use tokens: `bg-kidical-light-blue`, `bg-kidical-blue`, `text-kidical-ink`, `text-white`, `rounded-card`, `shadow-card`, `rounded-full`. Enforced by `tests/Feature/CssArchitectureTest.php`.
- Copy is NL, tone-of-voice compliant (`docs/tone-of-voice.md`), and contains **no em-dashes** (an AI tell Frederik flags).
- Shared working tree with another developer: stage by explicit path, never `git add -A`, do not push `main`.
- Run tests with `php artisan test --compact` and a `--filter`. Run `vendor/bin/pint --dirty --format agent` after PHP changes.

---

### Task 1: Create the `<x-newsletter-optin>` component

**Files:**
- Create: `resources/views/components/newsletter-optin.blade.php`
- Test: `tests/Feature/NewsletterOptinTest.php`

**Interfaces:**
- Produces: a Blade component usable as `<x-newsletter-optin />` (generic) or `<x-newsletter-optin :group="$group" />` (localised). `$group` is an optional `App\Models\Group` (default `null`).
- Consumes: `route('settings')` (exists), `flux:icon.check-circle`, `@theme` color/radius/shadow tokens.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/NewsletterOptinTest.php`:

```php
<?php

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Blade;

test('guest sees the opt-in form with benefits and submit button', function () {
    $html = Blade::render('<x-newsletter-optin />');

    expect($html)
        ->toContain('Blijf op de hoogte')
        ->toContain('De nieuwste ritten, elke maand als eerste')
        ->toContain('jouw lokale groep')
        ->toContain('Eén rustige mail, makkelijk uit te schrijven')
        ->toContain('Je e-mailadres')
        ->toContain('type="email"')
        ->toContain('Ja, hou me op de hoogte');
});

test('group prop localises the lokale-groep benefit with the gemeente name', function () {
    $group = Group::create([
        'shortname' => 'sb',
        'name' => 'Kidical Mass Schaarbeek',
        'invisible' => false,
        'started_at' => now(),
    ]);

    $html = Blade::render('<x-newsletter-optin :group="$group" />', ['group' => $group]);

    expect($html)
        ->toContain('Het laatste nieuws uit Schaarbeek')
        ->not->toContain('jouw lokale groep');
});

test('authenticated visitor sees a manage-preferences panel and no email form', function () {
    $this->actingAs(User::factory()->create());

    $html = Blade::render('<x-newsletter-optin />');

    expect($html)
        ->toContain('Beheer voorkeuren')
        ->toContain(route('settings'))
        ->not->toContain('type="email"')
        ->not->toContain('Ja, hou me op de hoogte');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=NewsletterOptinTest`
Expected: FAIL — component view `newsletter-optin` not found.

- [ ] **Step 3: Write the component**

Create `resources/views/components/newsletter-optin.blade.php`:

```blade
@props(['group' => null])

@php
    $gemeente = null;
    if ($group) {
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    }

    $benefits = [
        'De nieuwste ritten, elke maand als eerste',
        $gemeente ? "Het laatste nieuws uit {$gemeente}" : 'Het laatste nieuws uit jouw lokale groep',
        'Eén rustige mail, makkelijk uit te schrijven',
    ];

    $fieldId = 'newsletter-email-'.\Illuminate\Support\Str::random(6);
@endphp

@auth
    <div {{ $attributes->class('bg-kidical-light-blue rounded-card p-8 flex flex-col gap-3 items-start') }}>
        <h3 class="text-kidical-ink">Je bent al mee</h3>
        <p class="text-kidical-ink/75">Je staat op de hoogte. Je nieuwsvoorkeuren beheer je in je profiel.</p>
        <a href="{{ route('settings') }}" class="bg-kidical-blue text-white rounded-full px-5 py-2.5 font-bold no-underline">Beheer voorkeuren</a>
    </div>
@else
    <div {{ $attributes->class('@container') }} x-data="{ sent: false }">
        <div class="bg-kidical-light-blue rounded-card p-8 flex flex-col @lg:flex-row gap-8 @lg:items-center">
            <div class="flex flex-col gap-4 @lg:flex-1">
                <h3 class="text-kidical-ink">Blijf op de hoogte</h3>
                <ul class="flex flex-col gap-2.5">
                    @foreach ($benefits as $benefit)
                        <li class="flex items-start gap-2.5 text-kidical-ink">
                            <flux:icon.check-circle variant="solid" class="size-5 text-kidical-blue shrink-0 mt-0.5" aria-hidden="true" />
                            <span>{{ $benefit }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white rounded-card shadow-card p-6 @lg:flex-1">
                <form @submit.prevent="sent = true" x-show="!sent" class="flex flex-col gap-3">
                    <label for="{{ $fieldId }}" class="text-kidical-ink font-bold">Je e-mailadres</label>
                    <input
                        type="email"
                        id="{{ $fieldId }}"
                        required
                        placeholder="jouw@email.be"
                        class="rounded-full border-2 border-kidical-ink/15 px-4 py-2.5 text-kidical-ink focus:border-kidical-blue focus:outline-none">
                    <button type="submit" class="bg-kidical-blue text-white rounded-full px-5 py-2.5 font-bold">Ja, hou me op de hoogte</button>
                    <p class="text-kidical-ink/60 text-sm">Eén mail per maand. Uitschrijven kan altijd.</p>
                </form>
                <p x-show="sent" x-cloak class="text-kidical-blue font-bold">Bedankt! Je staat op de lijst.</p>
            </div>
        </div>
    </div>
@endauth
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=NewsletterOptinTest`
Expected: PASS (3 tests).

- [ ] **Step 5: Run the CSS architecture test**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS — the component uses only token utilities, no arbitrary hex/px.

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/newsletter-optin.blade.php tests/Feature/NewsletterOptinTest.php
git commit -m "feat(public): newsletter opt-in component (Optie C)

- Three states: guest opt-in form, Alpine thank-you, auth manage-prefs panel
- Container-query responsive: stacks narrow, two-column wide
- Styled placeholder, no backend; token-backed Tailwind only
Why: shared 'Blijf op de hoogte' CTA for calendar + chapter pages"
```

---

### Task 2: Wire the component into the `/events` sidebar

**Files:**
- Modify: `resources/views/livewire/ride-calendar.blade.php:89-97`
- Modify: `resources/css/pages/calendar.css` (remove dead newsletter rules)
- Test: `tests/Feature/NewsletterOptinTest.php` (add a calendar-page case)

**Interfaces:**
- Consumes: `<x-newsletter-optin />` from Task 1.

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/NewsletterOptinTest.php`:

```php
use function Pest\Laravel\get;

test('the calendar page shows the opt-in in the sidebar and not the old card', function () {
    get(route('activities.index'))
        ->assertOk()
        ->assertSee('Blijf op de hoogte')
        ->assertSee('Ja, hou me op de hoogte')
        ->assertDontSee('Mis geen rit')
        ->assertDontSee('Schrijf je in');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter="calendar page shows the opt-in"`
Expected: FAIL — page still renders "Mis geen rit" / "Schrijf je in" and not the opt-in.

- [ ] **Step 3: Replace the sidebar panel markup**

In `resources/views/livewire/ride-calendar.blade.php`, replace the `@if ($when !== 'voorbije') ... @endif` aside block (lines ~89-97):

```blade
            {{-- Sticky sidebar (desktop only; hidden on mobile via CSS) --}}
            @if ($when !== 'voorbije')
                <aside class="kal-sidebar">
                    <div class="kal-sidebar__panel kal-sidebar__panel--newsletter">
                        <h3 class="kal-sidebar__heading">Mis geen rit</h3>
                        <p class="kal-sidebar__body">Één seintje per maand met ritten bij jou in de buurt. Geen spam, altijd uitschrijfbaar.</p>
                        <button type="button" class="kal-sidebar__btn">Schrijf je in</button>
                    </div>
                </aside>
            @endif
```

with:

```blade
            {{-- Sticky sidebar (desktop only; hidden on mobile via CSS) --}}
            @if ($when !== 'voorbije')
                <aside class="kal-sidebar">
                    <x-newsletter-optin />
                </aside>
            @endif
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter="calendar page shows the opt-in"`
Expected: PASS.

- [ ] **Step 5: Remove the dead newsletter CSS**

First confirm the rules are now unused in views:

Run: `grep -rn "kal-sidebar__heading\|kal-sidebar__body\|kal-sidebar__btn\|kal-sidebar__panel--newsletter" resources/views`
Expected: no matches.

If there are no matches, delete these rule blocks from `resources/css/pages/calendar.css`: `.kal-sidebar__panel--newsletter`, `.kal-sidebar__heading`, `.kal-sidebar__body`, `.kal-sidebar__btn`, and `.kal-sidebar__btn:hover`. Keep `.kal-sidebar`, `.kal-sidebar__panel`, `.kal-sidebar__panel--nudge`, `.kal-sidebar__nudge-icon` untouched (out of scope; may serve another panel variant). If a class above still has a match, leave that one in place.

- [ ] **Step 6: Verify CSS partial test still passes**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS — every partial still imported; no dangling import.

- [ ] **Step 7: Commit**

```bash
git add resources/views/livewire/ride-calendar.blade.php resources/css/pages/calendar.css tests/Feature/NewsletterOptinTest.php
git commit -m "feat(calendar): swap sidebar 'Mis geen rit' card for newsletter opt-in

- Replace yellow sidebar panel with <x-newsletter-optin>
- Drop now-unused .kal-sidebar newsletter CSS rules"
```

---

### Task 3: Wire the component into every chapter page

**Files:**
- Modify: `resources/views/groups/show.blade.php:91-117` (empty-state note + always-on opt-in)
- Modify: `resources/css/pages/chapters.css` (remove dead `.chapter-notify*` rules)
- Modify: `tests/Feature/GroupsTest.php:253-260` (update the broken assertion)
- Test: `tests/Feature/NewsletterOptinTest.php` (add chapter-page cases)

**Interfaces:**
- Consumes: `<x-newsletter-optin :group="$group" />` from Task 1.

- [ ] **Step 1: Update the existing assertion that this task breaks, and add chapter cases**

In `tests/Feature/GroupsTest.php`, the test "chapter home shows a designed empty state when no upcoming ride" (lines ~253-260) currently asserts the removed form button `'Hou me op de hoogte'`. Change its body to:

```php
test('chapter home shows a designed empty state when no upcoming ride', function () {
    $group = Group::create(['shortname' => 'nm', 'name' => 'Kidical Mass Namur', 'zip' => '5000', 'invisible' => false, 'started_at' => now()]);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Nog geen fietstocht gepland')
        ->assertSee('Blijf op de hoogte');
});
```

Append to `tests/Feature/NewsletterOptinTest.php`:

```php
test('chapter page always shows the localised opt-in, with and without a ride', function () {
    $author = App\Models\User::factory()->create();
    $group = Group::create(['shortname' => 'sb', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);

    // Without a ride
    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Nog geen fietstocht gepland')
        ->assertSee('Blijf op de hoogte')
        ->assertSee('Het laatste nieuws uit Schaarbeek');

    // With a ride
    App\Models\Activity::create([
        'title_nl' => 'Kidical Mass Schaarbeek', 'title_fr' => 'x',
        'content_nl' => 'x', 'content_fr' => 'x',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addWeek(), 'duration_minutes' => 60,
        'location' => 'Place Colignon', 'author_id' => $author->id,
    ])->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Nog geen fietstocht gepland')
        ->assertSee('Blijf op de hoogte')
        ->assertSee('Het laatste nieuws uit Schaarbeek');
});
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter="chapter page always shows the localised opt-in"`
Expected: FAIL — the opt-in markup is not on the chapter page yet.

- [ ] **Step 3: Replace the empty-state form and add the always-on card**

In `resources/views/groups/show.blade.php`, replace the `@unless ($hasRide) ... @endunless` block (lines ~91-102) with a text-only honest note (form removed):

```blade
        {{-- No ride on the agenda yet (there may still be workshops/meetings below):
             a warm, honest note — never a workshop dressed as a ride. The opt-in below
             handles "leave your email". --}}
        @unless ($hasRide)
            <div class="chapter-next__card chapter-next__card--empty">
                <p class="chapter-next__empty-lead">Nog geen fietstocht gepland.</p>
                <p class="chapter-next__empty-body">We laten het je weten zodra {{ $gemeente }} vertrekt. Schrijf je hieronder in.</p>
            </div>
        @endunless
```

Then add the always-on opt-in just before the closing `</section>` of the agenda section (after the `@if ($activities->isNotEmpty()) ... @endif` block, ~line 116):

```blade
        <div class="mt-12">
            <x-newsletter-optin :group="$group" />
        </div>
    </section>
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --compact --filter="NewsletterOptinTest|GroupsTest"`
Expected: PASS, including the updated empty-state test.

- [ ] **Step 5: Remove the dead chapter form CSS**

Confirm the form classes are unused in views:

Run: `grep -rn "chapter-notify" resources/views`
Expected: no matches.

If none, delete these rule blocks from `resources/css/pages/chapters.css`: `.chapter-notify`, `.chapter-next__card--empty .chapter-notify`, `.chapter-notify__input`, `.chapter-notify__input:focus`, `.chapter-notify__btn`, `.chapter-notify__btn:hover`, `.chapter-notify__done`. Keep `.chapter-next__card--empty`, `.chapter-next__empty-lead`, `.chapter-next__empty-body` (still used by the honest note).

- [ ] **Step 6: Run the affected suite + CSS test**

Run: `php artisan test --compact --filter="NewsletterOptinTest|GroupsTest|CssArchitectureTest"`
Expected: PASS.

- [ ] **Step 7: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/groups/show.blade.php resources/css/pages/chapters.css tests/Feature/GroupsTest.php tests/Feature/NewsletterOptinTest.php
git commit -m "feat(chapters): always-on newsletter opt-in, honest empty note kept

- Replace empty-state email form with <x-newsletter-optin :group>, shown always
- Keep text-only 'Nog geen fietstocht gepland' note when no ride
- Drop now-unused .chapter-notify CSS; update empty-state test"
```

---

### Task 4: Build assets and verify in the browser

**Files:** none (verification only).

- [ ] **Step 1: Build the frontend so new Tailwind utilities (container query, tokens) are generated**

Run: `npm run build`
Expected: build succeeds, no Vite manifest errors.

- [ ] **Step 2: Run the full affected test suite once more**

Run: `php artisan test --compact --filter="NewsletterOptinTest|GroupsTest|CssArchitectureTest|CalendarFilterBarTest"`
Expected: all PASS.

- [ ] **Step 3: Visual check (one screenshot pass, both pages)**

Use the project Playwright pattern (`.cjs`, `ignoreHTTPSErrors: true`). Screenshot, as a guest:
- `https://kidicalmass.test/events` — confirm the sidebar shows the light-blue opt-in card, benefits with blue check icons stacked above the white form card.
- `https://kidicalmass.test/chapters/9` — confirm the two-column opt-in (benefits left, white form card right) appears at the end of the agenda, with the gemeente name in the second bullet.

Then, logged in (use `route('login.as', ...)` demo login if available), reload `/events` and confirm the sidebar shows the "Je bent al mee / Beheer voorkeuren" panel with no email field.

Expected: layouts match the design; no overflow or collapsed columns. Batch any fixes, then re-screenshot only if a visual change was made.

- [ ] **Step 4: No commit unless Step 3 required code fixes**

If fixes were needed, `vendor/bin/pint --dirty --format agent` then commit the touched files by explicit path.

---

## Notes for the implementer

- This is a **styled placeholder**: the form does nothing server-side on purpose. Do not add a `Subscriber` model, migration, route, or Livewire component.
- Group-specificity (subscribing to particular chapters) is a **future** "kies je groepen" preferences page — out of scope. The `:group` prop only localises copy here.
- The `@lg` container-query breakpoint (32rem) is what makes the narrow events sidebar stack while the wide chapter spot goes two-column — do not swap it for a viewport `lg:` variant.
