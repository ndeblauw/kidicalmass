# Roze-hesje Hub — App-shell Navigation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the public marketing nav inside the roze-hesje hub with a single slim app-shell bar, so the hub reads as one logged-in member workspace instead of two stacked navigations.

**Architecture:** The site layout (`layouts/site.blade.php`) gains an optional `navbar` slot: when a page supplies it, the layout renders that slot in place of the marketing header and drops the fixed-nav top padding. The hub component (`<x-roze-hub>`) supplies a sticky chrome block — a new `<x-roze-shell-bar>` (logo → home, chapter context label / switcher, account menu) stacked above the existing `<x-roze-subnav>`. The red `.roze-hub-hero` band is deleted.

**Tech Stack:** Laravel 12 anonymous Blade components, Livewire Flux (`flux:dropdown`/`flux:menu` for account + switcher), Tailwind v4 tokens, CSS partial `resources/css/components/roze-hub.css`, Pest feature + component tests.

## Global Constraints

- Public-site headings use raw `<h1>`–`<h6>`, never `flux:heading`. Other `flux:*` (button, dropdown, menu, icon) are fine.
- No raw hex/px in `.blade.php` components (enforced by `tests/Feature/CssArchitectureTest.php`). Colour/spacing values go in the CSS partial, token-backed; `#fff` for white is acceptable in the CSS partial (matches existing `.roze-subnav`).
- CSS lives in `resources/css/components/roze-hub.css` inside `@layer components`. No new `@theme` tokens.
- No em-dashes in any user-facing copy (project rule).
- Context label copy: chapter name leads, role qualifies — place = group name with a leading `kidical mass ` stripped; role label is the literal `roze hesjes`. The brand word "Kidical Mass" is carried by the logo only, never repeated in the label.
- Drop "Steun ons" from the hub entirely (no `steun-nav-btn` inside the hub).
- Route names (locale auto-injected via URL defaults, so pass only the group, exactly like `header.blade.php:44`): `groups.roze-hesjes` (overview) + `groups.roze-hesjes.{aan-de-slag,agenda,fotos,groep,materiaal}`.
- The chapter pill in `resources/views/layouts/site/header.blade.php` stays — it is the entry point INTO the hub from public pages, not a duplicate. The only edit allowed to that shared file is swapping its inline account dropdown for `<x-account-menu />` (Task 2); stage it by explicit path, change nothing else.
- Run `vendor/bin/pint --dirty --format agent` before each commit that touches PHP/Blade.

---

## File Structure

- `resources/views/layouts/site.blade.php` — **modify** (Task 1): add `navbar` slot + conditional top padding. One responsibility: the page shell. Default behaviour (marketing header) unchanged for all non-hub pages.
- `resources/views/components/account-menu.blade.php` — **create** (Task 2): the account dropdown (Instellingen / Admin / Uitloggen), extracted from the header so the shell bar and header share one source.
- `resources/views/layouts/site/header.blade.php` — **modify** (Task 2): replace its inline account dropdown with `<x-account-menu />`. No other change.
- `resources/views/components/roze-shell-bar.blade.php` — **create** (Task 3): the slim app-shell top bar (logo, context label / switcher, `<x-account-menu />`). New, single responsibility.
- `resources/views/components/roze-hub.blade.php` — **modify** (Task 4): supply the sticky chrome via the layout `navbar` slot; remove the `.roze-hub-hero` markup.
- `resources/css/components/roze-hub.css` — **modify** (Tasks 3 & 4): add `.roze-shell-bar*` rules (Task 3); delete `.roze-hub-hero*` and add `.roze-chrome` (Task 4). Keep `.roze-subnav*`, `.roze-hub-title`, `.roze-hub-body`.
- `tests/Feature/AccountMenuComponentTest.php` — **create** (Task 2): the extracted component renders its items.
- `tests/Feature/RozeShellBarComponentTest.php` — **create** (Task 3): isolated component tests for the shell bar.
- `tests/Feature/RozeHubComponentTest.php` — **modify** (Tasks 1 & 4): layout-slot cases (Task 1); replace the hero assertions with shell-bar assertions (Task 4).
- `tests/Feature/RozeHesjeHubTest.php` — **modify** (Task 4): add feature tests (marketing nav gone, switcher, single-chapter label).

Out of scope (note, do not build): the mobile-only "identity line scrolls away while the tab strip stays pinned" refinement — v1 sticks the whole chrome on every width. A quiet `← terug naar de site` text link beyond the logo.

---

### Task 1: Layout `navbar` slot

Lets any page replace the marketing header with its own chrome and drop the fixed-nav padding, without affecting other pages.

**Files:**
- Modify: `resources/views/layouts/site.blade.php:14-23`
- Test: `tests/Feature/RozeHubComponentTest.php` (add two cases at the top; same file the layout is already exercised from)

**Interfaces:**
- Produces: `<x-layouts::site>` accepts an optional named slot `navbar`. When present, the layout renders `{{ $navbar }}` instead of `<x-layouts::site.header />`, and `<main>` uses `pt-0` instead of `pt-28`. When absent, behaviour is unchanged.

- [ ] **Step 1: Write the failing tests**

Add to the top of `tests/Feature/RozeHubComponentTest.php` (below the existing `use` lines):

```php
test('the site layout renders a supplied navbar slot instead of the marketing header', function () {
    $html = Blade::render(
        '<x-layouts::site><x-slot:navbar>SHELLBAR</x-slot:navbar>BODY</x-layouts::site>',
    );

    expect($html)
        ->toContain('SHELLBAR')
        ->toContain('BODY')
        ->not->toContain('site-nav__links');
});

test('the site layout falls back to the marketing header with no navbar slot', function () {
    $html = Blade::render('<x-layouts::site>BODY</x-layouts::site>');

    expect($html)
        ->toContain('site-nav__links')
        ->toContain('BODY');
});
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `php artisan test --compact --filter='navbar slot|marketing header'`
Expected: the first test FAILS (slot is ignored, `site-nav__links` still present).

- [ ] **Step 3: Implement the slot**

In `resources/views/layouts/site.blade.php`, replace the header line (currently line 15):

```blade
    <x-layouts::site.header />
```

with:

```blade
    @isset($navbar)
        {{ $navbar }}
    @else
        <x-layouts::site.header />
    @endisset
```

and replace the `<main>` opening tag (currently line 21):

```blade
    <main class="flex-1 container mx-auto px-4 pt-28 {{ isset($closing) ? 'pb-0' : 'pb-8' }}">
```

with:

```blade
    <main class="flex-1 container mx-auto px-4 {{ isset($navbar) ? 'pt-0' : 'pt-28' }} {{ isset($closing) ? 'pb-0' : 'pb-8' }}">
```

- [ ] **Step 4: Run the tests to verify they pass**

Run: `php artisan test --compact --filter='navbar slot|marketing header'`
Expected: PASS (both).

- [ ] **Step 5: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/layouts/site.blade.php tests/Feature/RozeHubComponentTest.php
git commit -m "feat(site-layout): optional navbar slot to swap the marketing header

- pages may supply <x-slot:navbar>; the layout renders it instead of the
  fixed marketing header and drops the pt-28 fixed-nav clearance
- default (no slot) behaviour unchanged for every existing page

Why: the roze-hesje hub needs its own app-shell chrome in the header's
place, not stacked under the marketing nav.

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 2: Extract `<x-account-menu>`

Pull the account dropdown out of the marketing header into one shared component, so the shell bar (Task 3) and the header use the same source. Pure extraction — identical rendered output.

**Files:**
- Create: `resources/views/components/account-menu.blade.php`
- Modify: `resources/views/layouts/site/header.blade.php:49-63` (the `@auth` inline `flux:dropdown`)
- Test: `tests/Feature/AccountMenuComponentTest.php`

**Interfaces:**
- Consumes: nothing from earlier tasks.
- Produces: `<x-account-menu />` — renders the account `flux:dropdown` (trigger `.account-nav-btn`; items Instellingen / Admin-if-`canAccessFilament` / Uitloggen). Assumes a signed-in user (it calls `Auth::user()`); every caller wraps it in `@auth`, exactly as the header does today.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/AccountMenuComponentTest.php`:

```php
<?php

use App\Models\User;
use Illuminate\Support\Facades\Blade;

use function Pest\Laravel\actingAs;

test('the account menu renders its trigger and the logout item', function () {
    actingAs(User::factory()->create());

    $html = Blade::render('<x-account-menu />');

    expect($html)
        ->toContain('account-nav-btn')
        ->toContain('Uitloggen');
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact tests/Feature/AccountMenuComponentTest.php`
Expected: FAIL ("Unable to locate a class or view for component [account-menu]").

- [ ] **Step 3: Create the component**

Create `resources/views/components/account-menu.blade.php` with the exact markup currently inline in the header:

```blade
<flux:dropdown>
    <flux:button variant="ghost" icon="ellipsis-vertical" aria-label="Account" class="account-nav-btn" />
    <flux:menu>
        <flux:menu.item href="{{ route('settings') }}" wire:navigate>{{ __('Instellingen') }}</flux:menu.item>
        @if(Auth::user()->canAccessFilament())
            <flux:menu.separator />
            <flux:menu.item href="{{ url('/admin') }}">{{ __('Admin') }}</flux:menu.item>
        @endif
        <flux:menu.separator />
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <flux:menu.item type="submit">{{ __('Uitloggen') }}</flux:menu.item>
        </form>
    </flux:menu>
</flux:dropdown>
```

- [ ] **Step 4: Replace the inline dropdown in the header**

In `resources/views/layouts/site/header.blade.php`, inside the `@auth` block, replace the entire inline `<flux:dropdown> … </flux:dropdown>` (currently lines 49-63 — the one with the `ellipsis-vertical` trigger) with a single line:

```blade
                        <x-account-menu />
```

Leave everything else in that file unchanged — the `@foreach ($myChapters ...)` chapter pills directly above it stay exactly as they are.

- [ ] **Step 5: Run the test + a header regression check**

Run: `php artisan test --compact tests/Feature/AccountMenuComponentTest.php`
Expected: PASS.

Run: `php artisan test --compact --filter='Header|Nav|Smoke'`
Expected: PASS (header still renders for the pages that use it). If no such tests exist, this filter runs zero tests — that is fine; the component test plus the unchanged markup cover the extraction.

- [ ] **Step 6: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/account-menu.blade.php resources/views/layouts/site/header.blade.php tests/Feature/AccountMenuComponentTest.php
git commit -m "refactor(nav): extract <x-account-menu> from the site header

- the account dropdown (Instellingen / Admin / Uitloggen) moves to one
  shared component; the header now renders <x-account-menu />
- identical output; lets the roze-hesje shell bar reuse the same menu

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 3: `<x-roze-shell-bar>` component

The slim top bar: logo back to the public site, the chapter context label (a switcher when the member runs more than one chapter), and the shared account menu. Calm/white, not red.

**Files:**
- Create: `resources/views/components/roze-shell-bar.blade.php`
- Modify: `resources/css/components/roze-hub.css` (add shell-bar styles only this task; hero deletion happens in Task 3)
- Test: `tests/Feature/RozeShellBarComponentTest.php`

**Interfaces:**
- Consumes: `<x-account-menu />` (Task 2).
- Produces: `<x-roze-shell-bar :group="$group" />` where `$group` is an `App\Models\Group`. Emits a `.roze-shell-bar` block. Computes `$myChapters` itself from `Auth` (mirrors `header.blade.php`), so callers pass only the group. Renders a `.roze-shell-switch` button (inside a `flux:dropdown`) when the signed-in user belongs to more than one visible chapter, otherwise a plain `.roze-shell-bar__context` span. The account menu (`<x-account-menu />`, wrapped in `.roze-shell-bar__account`) renders only under `@auth`.

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/RozeShellBarComponentTest.php`:

```php
<?php

use App\Models\Group;
use Illuminate\Support\Facades\Blade;

test('the shell bar links the logo back to the public home', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek']);

    $html = Blade::render('<x-roze-shell-bar :group="$group" />', ['group' => $group]);

    expect($html)
        ->toContain('roze-shell-bar')
        ->toContain('href="'.route('home').'"');
});

test('the shell bar shows the chapter name and the roze-hesje role', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek']);

    $html = Blade::render('<x-roze-shell-bar :group="$group" />', ['group' => $group]);

    expect($html)
        ->toContain('Schaarbeek')
        ->toContain('roze hesjes')
        ->not->toContain('Kidical Mass Schaarbeek'); // brand carried by the logo, not the label
});

test('the shell bar shows a plain label (no switcher) when no one is signed in', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek']);

    $html = Blade::render('<x-roze-shell-bar :group="$group" />', ['group' => $group]);

    expect($html)
        ->toContain('roze-shell-bar__context')
        ->not->toContain('roze-shell-switch');
});
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `php artisan test --compact tests/Feature/RozeShellBarComponentTest.php`
Expected: FAIL ("Unable to locate a class or view for component [roze-shell-bar]").

- [ ] **Step 3: Create the component**

Create `resources/views/components/roze-shell-bar.blade.php`:

```blade
@props(['group'])

@php
    // Mirror header.blade.php: the visitor's visible chapters drive the switcher.
    $myChapters = \Illuminate\Support\Facades\Auth::check()
        ? \Illuminate\Support\Facades\Auth::user()->groups()->where('invisible', false)->orderBy('name')->get()
        : collect();

    $place = \Illuminate\Support\Str::of($group->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim();
    $hasSwitcher = $myChapters->count() > 1;
@endphp

<div class="roze-shell-bar">
    <div class="roze-shell-bar__inner">
        <a href="{{ route('home') }}" class="roze-shell-bar__logo" aria-label="Terug naar Kidical Mass">
            <img src="{{ asset('img/logos/logo-icon.png') }}" alt="Kidical Mass" class="roze-shell-bar__mark">
        </a>

        @if ($hasSwitcher)
            <flux:dropdown>
                <button type="button" class="roze-shell-switch roze-shell-bar__context" aria-label="Wissel van groep">
                    <span class="roze-shell-bar__place">{{ $place }}</span>
                    <span class="roze-shell-bar__role">roze hesjes</span>
                    <flux:icon name="chevron-down" class="size-4" aria-hidden="true" />
                </button>
                <flux:menu>
                    @foreach ($myChapters as $chapter)
                        <flux:menu.item href="{{ route('groups.roze-hesjes', $chapter) }}">
                            {{ \Illuminate\Support\Str::of($chapter->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim() }}
                        </flux:menu.item>
                    @endforeach
                </flux:menu>
            </flux:dropdown>
        @else
            <span class="roze-shell-bar__context">
                <span class="roze-shell-bar__place">{{ $place }}</span>
                <span class="roze-shell-bar__role">roze hesjes</span>
            </span>
        @endif

        @auth
            <div class="roze-shell-bar__account">
                <x-account-menu />
            </div>
        @endauth
    </div>
</div>
```

- [ ] **Step 4: Add the shell-bar styles**

In `resources/css/components/roze-hub.css`, inside the top `@layer components {` block, immediately AFTER the closing brace of the existing `.roze-hub-hero__mark { ... }` rule (around line 31), add:

```css
    /* === App-shell top bar ================================================ */
    /* Calm white bar (NOT red) — a logged-in member workspace, not the
       marketing megaphone. Red survives only as the role accent. */
    .roze-shell-bar {
        background: #fff;
    }

    .roze-shell-bar__inner {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 1.125rem;
    }

    .roze-shell-bar__logo {
        flex: none;
        display: inline-flex;
    }

    .roze-shell-bar__mark {
        width: 2rem;
        height: 2rem;
        object-fit: contain;
    }

    .roze-shell-bar__context {
        display: inline-flex;
        align-items: baseline;
        gap: 0.4rem;
        background: none;
        border: 0;
        padding: 0;
        cursor: inherit;
    }

    .roze-shell-switch {
        cursor: pointer;
    }

    .roze-shell-bar__place {
        font-family: var(--font-sans);
        font-weight: 800;
        font-size: 1rem;
        letter-spacing: -0.01em;
        color: var(--color-kidical-ink);
    }

    .roze-shell-bar__role {
        font-family: var(--font-sans);
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--color-kidical-red);
    }

    .roze-shell-bar__account {
        margin-left: auto;
    }
```

Then, inside the existing `@media (min-width: 48rem) { ... }` block, after the `.roze-subnav__list { ... }` rule, add:

```css
        .roze-shell-bar__inner {
            max-width: 47.5rem;
            margin: 0 auto;
            padding: 0.75rem 1.5rem;
        }

        .roze-shell-bar__place {
            font-size: 1.15rem;
        }
```

- [ ] **Step 5: Run the tests to verify they pass**

Run: `php artisan test --compact tests/Feature/RozeShellBarComponentTest.php`
Expected: PASS (all three).

- [ ] **Step 6: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/roze-shell-bar.blade.php resources/css/components/roze-hub.css tests/Feature/RozeShellBarComponentTest.php
git commit -m "feat(roze-hesjes): app-shell top bar (logo, chapter label, account)

- new <x-roze-shell-bar>: logo links back to the public home, a calm white
  bar names 'Schaarbeek roze hesjes' (red only as the role accent), and the
  account menu rides on the right
- a member of >1 visible chapter gets a chapter switcher dropdown instead of
  a plain label; no 'Steun ons' in the hub
- styles aligned to the existing 47.5rem hub column

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 4: Wire the hub to the shell + delete the red hero

Swap the hub's red hero band for the sticky shell-bar + subnav chrome via the layout slot, and update the affected tests.

**Files:**
- Modify: `resources/views/components/roze-hub.blade.php:13-24`
- Modify: `resources/css/components/roze-hub.css` (delete `.roze-hub-hero*`; add `.roze-chrome`)
- Modify: `tests/Feature/RozeHubComponentTest.php` (replace the hero assertions)
- Modify: `tests/Feature/RozeHesjeHubTest.php` (add feature tests)

**Interfaces:**
- Consumes: `<x-layouts::site>` `navbar` slot (Task 1); `<x-roze-shell-bar :group>` (Task 3); the existing `<x-roze-subnav :tabs :group :beheer-url>`.
- Produces: `<x-roze-hub>` renders, in the `navbar` slot, `<div class="roze-chrome">` wrapping the shell bar + subnav; the page body stays in `<main>`. No `.roze-hub-hero` anywhere.

- [ ] **Step 1: Update the component tests (write the new expectations first)**

In `tests/Feature/RozeHubComponentTest.php`, replace the first test (`'roze-hub renders the chapter name in the compact hero'`) with:

```php
test('roze-hub renders the chapter name in the app-shell bar, not a hero', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek']);

    $html = Blade::render(
        '<x-roze-hub :group="$group" active="overzicht" :is-captain="false" :show-welcome="false">BODY</x-roze-hub>',
        ['group' => $group],
    );

    expect($html)
        ->toContain('roze-shell-bar')
        ->toContain('roze-chrome')
        ->toContain('Schaarbeek')
        ->toContain('BODY')
        ->not->toContain('roze-hub-hero');
});
```

Leave the other two tests (`'the active tab carries the active modifier class'`, `'Beheer appears only for captains'`) unchanged — they still hold.

In `tests/Feature/RozeHesjeHubTest.php`, add these tests (after the existing Beheer test):

```php
test('the hub renders the app-shell bar and hides the marketing nav', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek', 'invisible' => false]);
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertSee('roze-shell-bar', escape: false)
        ->assertSee('Schaarbeek', escape: false)
        ->assertDontSee('site-nav__links', escape: false)
        ->assertDontSee('steun-nav-btn', escape: false);
});

test('a member of one chapter sees a plain context label, no switcher', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek', 'invisible' => false]);
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertSee('roze-shell-bar__context', escape: false)
        ->assertDontSee('roze-shell-switch', escape: false);
});

test('a member of multiple chapters gets a chapter switcher', function () {
    $here = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek', 'invisible' => false]);
    $other = Group::factory()->create(['name' => 'Kidical Mass Gent', 'invisible' => false]);
    $member = User::factory()->create();
    $here->users()->attach($member, ['role' => null]);
    $other->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl('groups.roze-hesjes', $here))
        ->assertSee('roze-shell-switch', escape: false)
        ->assertSee('Gent', escape: false);
});
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `php artisan test --compact --filter='app-shell|context label|chapter switcher|app-shell bar'`
Expected: FAIL (hub still renders `roze-hub-hero` + the marketing nav; `roze-shell-bar` absent).

- [ ] **Step 3: Rewire the hub component**

Replace the body of `resources/views/components/roze-hub.blade.php` (the part from `<x-layouts::site ...>` to its close, currently lines 13-24) with:

```blade
<x-layouts::site title="Kidical Mass {{ $group->name }}">
    {{-- App-shell chrome in the header's place: one slim bar + the hub tabs,
         sticky together. The red marketing nav is gone here (member workspace). --}}
    <x-slot:navbar>
        <div class="roze-chrome">
            <x-roze-shell-bar :group="$group" />
            <x-roze-subnav :tabs="$tabs" :group="$group" :beheer-url="$beheerUrl" />
        </div>
    </x-slot:navbar>

    <div class="roze-hub-body">
        {{ $slot }}
    </div>
</x-layouts::site>
```

(The `@props` and `@php $tabs = ...` block at the top of the file stays unchanged.)

- [ ] **Step 4: Delete the hero CSS and add the sticky chrome**

In `resources/css/components/roze-hub.css`:

1. Delete the three base hero rules — `.roze-hub-hero { ... }`, `.roze-hub-hero > h1 { ... }`, and `.roze-hub-hero__mark { ... }` (the block currently spanning roughly lines 2-31, including its leading `/* === Roze-hub compact hero ... */` comment).
2. Inside the `@media (min-width: 48rem) { ... }` block, delete the three hero overrides — `.roze-hub-hero { padding: ... }`, `.roze-hub-hero > h1 { font-size: ... }`, and `.roze-hub-hero__mark { width/height ... }`.
3. At the very top of the `@layer components {` block (where the hero comment used to be), add the sticky wrapper:

```css
    /* The shell bar + sub-nav travel together and pin to the top on scroll. */
    .roze-chrome {
        position: sticky;
        top: 0;
        z-index: 20;
        background: #fff;
    }
```

- [ ] **Step 5: Run the targeted tests to verify they pass**

Run: `php artisan test --compact tests/Feature/RozeHubComponentTest.php tests/Feature/RozeHesjeHubTest.php`
Expected: PASS (all, including the unchanged member/captain/welcome cases).

- [ ] **Step 6: Run the full hub + architecture suite (no regressions)**

Run: `php artisan test --compact --filter='Roze|CssArchitecture'`
Expected: PASS. `CssArchitectureTest` confirms the partial is registered and no raw hex/px leaked into components.

- [ ] **Step 7: Verify live**

Open `https://kidicalmass.test/nl/chapters/{group}/roze-hesjes` signed in as a chapter member (use `mcp__laravel-boost__get-absolute-url` to resolve, and seed/pick a member group). Confirm at desktop + ~390px mobile: one navigation (shell bar + tabs), chapter name shown once, no red-on-red, no logo overlap, no "Steun ons", and the chrome pins to the top on scroll. Run `npm run build` first if the change is not reflected.

- [ ] **Step 8: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/roze-hub.blade.php resources/css/components/roze-hub.css tests/Feature/RozeHubComponentTest.php tests/Feature/RozeHesjeHubTest.php
git commit -m "feat(roze-hesjes): app-shell nav replaces the red hero + marketing chrome

- the hub renders one sticky chrome (shell bar + tabs) via the layout's
  navbar slot, in the marketing header's place; the red .roze-hub-hero band
  is deleted, so the chapter name shows once and there is no red-on-red
- members of multiple chapters get a switcher; account menu moves into the bar
- tests: component asserts the shell bar (not the hero); feature asserts the
  marketing nav and 'Steun ons' are gone, plus single/multi-chapter labels

Why: the hub is a logged-in member workspace; it should not wear the public
marketing nav stacked above its own tabs. Direction chosen 2026-06-18.
Brief: docs/superpowers/specs/2026-06-18-roze-hesje-hub-app-shell-nav-handoff.md

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

- [ ] **Step 9: Mark the brief built + log it**

In `docs/superpowers/specs/2026-06-18-roze-hesje-hub-app-shell-nav-handoff.md`, append to the top `Update` note: `Built 2026-06-18 — see plan 2026-06-18-roze-hesje-hub-app-shell-nav.md.` Append one line to `docs/wiki/log.md`: `## [2026-06-18] build | roze-hesje hub: app-shell nav (shell bar + sticky tabs) replaces the red hero + marketing chrome.` Then:

```bash
git add docs/superpowers/specs/2026-06-18-roze-hesje-hub-app-shell-nav-handoff.md docs/wiki/log.md
git commit -m "docs(roze-hesjes): mark app-shell nav built + log entry"
```

---

## Self-Review

**Spec coverage** (against the brief):
- One nav / drop marketing nav inside hub → Task 1 (slot) + Task 4 (hub uses it; feature test asserts `site-nav__links` gone). ✓
- Delete red hero, name once → Task 4 (CSS + markup delete; test asserts no `roze-hub-hero`, no `Kidical Mass Schaarbeek` contiguous). ✓
- Calm (not-red) bar, red as accent → Task 3 CSS (`.roze-shell-bar` white, `.roze-shell-bar__role` red). ✓
- Sticky, slim → Task 4 `.roze-chrome` sticky. Mobile scroll-away explicitly out of scope (noted). ✓
- Context label "Schaarbeek · roze hesjes", place leads → Task 3 markup + test. ✓
- Logo → home as the back-to-site exit → Task 3 (test asserts `href=route('home')`). ✓
- Drop "Steun ons" → not rendered in shell bar; Task 4 feature test asserts `steun-nav-btn` absent. ✓
- Multi-chapter switcher → Task 3 (`.roze-shell-switch`) + Task 4 feature test. ✓
- Shared account menu (decision 2026-06-18) → Task 2 (`<x-account-menu>` extracted, header refactored, used by shell bar in Task 3). ✓
- Keep `.roze-subnav` + yellow active underline + 47.5rem column → untouched; shell bar aligned to 47.5rem. ✓
- Active tab keeps yellow underline → existing `.roze-subnav__tab--active` unchanged; existing test still asserts it. ✓

**Placeholder scan:** none — every step has exact paths, full code, and exact commands.

**Type/name consistency:** `navbar` slot name (Task 1 produces / Task 4 consumes) ✓; `<x-account-menu />` (Task 2 produces / Task 3 consumes) ✓; `<x-roze-shell-bar :group>` (Task 3 produces / Task 4 consumes) ✓; classes `roze-shell-bar`, `roze-shell-bar__context`, `roze-shell-switch`, `roze-shell-bar__account`, `roze-chrome` consistent between component, CSS, and test assertions ✓; route name `groups.roze-hesjes` used with group-only arg matches `header.blade.php` ✓.
