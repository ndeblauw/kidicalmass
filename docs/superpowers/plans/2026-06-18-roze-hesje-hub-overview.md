# Roze-hesje Hub — Overview + Shared Chrome Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Split the single `groups/roze-hesjes.blade.php` into a hub Overview page plus 5 sub-pages that share a compact pink hero and a slim sub-nav, building the Overview and the shared chrome to the surface spec while mechanically migrating existing content into the sub-pages.

**Architecture:** A new `RozeHesjeController` serves 6 membership-gated actions sharing a `hubContext()` helper. A `<x-roze-hub>` layout component renders the site layout + compact pink hero + `<x-roze-subnav>` + body slot; sub-nav tab order is computed by a unit-tested `App\Support\RozeHub\HubTabs` class. The icon-chip motif is extracted into a shared `<x-icon-chip>` (feature-card refactored to consume it). The Overview body adds a welcome panel, two *Voor de rit* tiles, and a `<x-roze-feed-card>` feed driven by a faux controller array.

**Tech Stack:** Laravel 12, Blade (anonymous components), Tailwind v4 (`@theme` tokens + role-based CSS partials), Pest 4. NL-only public site.

## Global Constraints

Every task's requirements implicitly include these (verbatim from the spec / project rules):

- **Tokens only for colour.** No raw hex anywhere; pink = existing `--color-kidical-red` (#E63A7B) — never mint a new pink.
- **Blade components must not contain raw `#hex` or `\d+px`** in Tailwind arbitrary `[...]` values or inline `style="…"` (enforced by `tests/Feature/CssArchitectureTest.php`). `rem` arbitrary values (e.g. `size-[2.75rem]`) ARE allowed. CSS partials MAY use raw `rem`/`px` lengths and `color-mix()`.
- **CSS lives in role-based partials**, never piled into `app.css`. Every `.css` file under `resources/css` (except `app.css`) MUST have a matching `@import './…'` line in `app.css` (enforced).
- **Headings:** raw `<h1>`–`<h6>` only, never `flux:heading`.
- **Accessibility:** decorative icons `aria-hidden="true"`; metadata as `<dl><dt><dd>`; dates as `<time datetime="ISO8601">`; `<html lang>` already dynamic via the site layout.
- **NL only. No em-dashes anywhere** (AI tell — project rule).
- **Radius-token note:** do NOT add `--radius-md` / `--radius-lg` to `@theme` — those keys would override Tailwind's built-in `rounded-md`/`rounded-lg` utilities used elsewhere. The hub's card radius (1.5rem), tile radius (1rem), and pill radius (9999px) are written as literal lengths inside the CSS partials (permitted there). Only the motion/shadow/hairline tokens below are added to `@theme`.
- Run `vendor/bin/pint --dirty --format agent` before finalising any PHP changes.

---

### Task 1: Design tokens + hub chrome CSS partial

**Files:**
- Modify: `resources/css/app.css` (add tokens to `@theme`; register the new partial in the `@import` block)
- Create: `resources/css/components/roze-hub.css` (hero + sub-nav styles)
- Test: `tests/Feature/CssArchitectureTest.php` (existing — must stay green)

**Interfaces:**
- Produces: the `@theme` tokens `--ease-brand`, `--shadow-float`, `--shadow-hover`, `--color-kidical-hairline`; the CSS classes `.roze-hub-hero`, `.roze-hub-hero__mark`, `.roze-hub-body`, `.roze-subnav`, `.roze-subnav__list`, `.roze-subnav__tab`, `.roze-subnav__tab--active`, `.roze-subnav__beheer` consumed by Task 4.

- [ ] **Step 1: Add tokens to `@theme`**

In `resources/css/app.css`, immediately after the `--shadow-hard-sm` block (currently ending at line ~55, before the `--color-text-body` comment), insert:

```css
    /* Roze-hub motion + surface language (hub Overview + shared chrome). */
    --ease-brand: cubic-bezier(0.22, 1, 0.36, 1);
    --shadow-float: 0 4px 20px rgb(0 0 0 / 0.08);
    --shadow-hover: 0 14px 30px -12px color-mix(in oklab, var(--color-kidical-ink), transparent 55%);
    --color-kidical-hairline: color-mix(in oklab, var(--color-kidical-ink), transparent 88%);
```

- [ ] **Step 2: Create the hub chrome partial**

Create `resources/css/components/roze-hub.css`:

```css
@layer components {
    /* === Roze-hub compact hero ============================================ */
    /* Full-bleed red band. Cancels the site layout's pt-28 with a negative top
       margin (same trick the other full-bleed bands use). */
    .roze-hub-hero {
        margin-top: -7rem;
        background: var(--color-kidical-red);
        padding: 1.0625rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .roze-hub-hero > h1 {
        margin: 0;
        color: #fff;
        font-family: var(--font-heading);
        font-weight: 800;
        font-synthesis: none;
        letter-spacing: -0.01em;
        line-height: 1.05;
        font-size: 1.34rem;
    }

    .roze-hub-hero__mark {
        flex: none;
        width: 2.375rem;
        height: 2.375rem;
        object-fit: contain;
    }

    /* === Hub sub-nav ====================================================== */
    .roze-subnav {
        background: #fff;
        border-bottom: 1px solid var(--color-kidical-hairline);
    }

    .roze-subnav__list {
        display: flex;
        gap: 0.4375rem;
        padding: 0.6875rem 0.875rem;
        overflow-x: auto;
        scrollbar-width: none;
        list-style: none;
        margin: 0;
    }

    .roze-subnav__list::-webkit-scrollbar {
        display: none;
    }

    .roze-subnav__tab {
        flex: none;
        white-space: nowrap;
        text-decoration: none;
        font-family: var(--font-sans);
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.375rem 0.8125rem;
        border-radius: 9999px;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 38%);
        transition: color 0.18s var(--ease-brand), background 0.18s var(--ease-brand);
    }

    .roze-subnav__tab--active {
        color: var(--color-kidical-red);
        background: color-mix(in oklab, var(--color-kidical-red), transparent 88%);
    }

    .roze-subnav__beheer {
        flex: none;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        white-space: nowrap;
        text-decoration: none;
        font-family: var(--font-sans);
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.4375rem 0.9375rem;
        border-radius: 9999px;
        color: var(--color-kidical-ink);
        background: color-mix(in oklab, var(--color-kidical-ink), transparent 92%);
    }

    /* === Hub body ========================================================= */
    .roze-hub-body {
        padding: 1.25rem 1.125rem 1.5rem;
    }

    /* Desktop: slim text row sub-nav + centered single column. */
    @media (min-width: 48rem) {
        .roze-hub-hero {
            padding: 1.625rem 2.25rem;
        }

        .roze-hub-hero > h1 {
            font-size: 2.3rem;
        }

        .roze-hub-hero__mark {
            width: 3.625rem;
            height: 3.625rem;
        }

        .roze-subnav__list {
            max-width: 60rem;
            margin: 0 auto;
            height: 3.5rem;
            gap: 1.75rem;
            padding: 0 2.25rem;
            overflow-x: visible;
            align-items: stretch;
        }

        .roze-subnav__tab {
            display: inline-flex;
            align-items: center;
            font-size: 0.95rem;
            padding: 0 0.125rem;
            border-radius: 0;
            background: none;
            border-bottom: 3px solid transparent;
            color: color-mix(in oklab, var(--color-kidical-ink), transparent 42%);
        }

        .roze-subnav__tab:hover {
            color: var(--color-kidical-red);
        }

        .roze-subnav__tab--active {
            color: var(--color-kidical-ink);
            background: none;
            border-bottom-color: var(--color-kidical-yellow);
        }

        .roze-subnav__beheer {
            margin-left: auto;
            align-self: center;
        }

        .roze-hub-body {
            max-width: 47.5rem;
            margin: 0 auto;
            padding: 2rem 1.5rem 3rem;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .roze-subnav__tab {
            transition: none;
        }
    }
}
```

- [ ] **Step 3: Register the partial in `app.css`**

In `resources/css/app.css`, find the `@import './components/…'` block (around lines 172-208) and add, in alphabetical position among the component imports:

```css
@import './components/roze-hub.css';
```

- [ ] **Step 4: Run the CSS architecture test**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS (the new partial is registered; the import resolves; no component touched yet).

- [ ] **Step 5: Commit**

```bash
git add resources/css/app.css resources/css/components/roze-hub.css
git commit -m "feat(roze-hesjes): add hub chrome tokens + CSS partial"
```

---

### Task 2: Extract `<x-icon-chip>` and refactor `<x-feature-card>`

**Files:**
- Create: `resources/views/components/icon-chip.blade.php`
- Modify: `resources/views/components/feature-card.blade.php`
- Test: `tests/Feature/IconChipTest.php`

**Interfaces:**
- Produces: `<x-icon-chip :color="…" size="sm|md|lg" :shadow="bool">{{ slot: icon svg }}</x-icon-chip>`. Colour map: `red|blue|orange|ink|green|violet|coral`. Size map: `sm`=2.25rem, `md`=2.75rem (default), `lg`=4.25rem. `shadow` (default false) adds `shadow-float`. Consumed by Tasks 4 and 6.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/IconChipTest.php`:

```php
<?php

use Illuminate\Support\Facades\Blade;

test('icon chip renders the chip square with mapped colour, size and tilt', function () {
    $html = Blade::render('<x-icon-chip color="blue" size="md"><svg></svg></x-icon-chip>');

    expect($html)
        ->toContain('bg-kidical-blue')
        ->toContain('size-[2.75rem]')
        ->toContain('rounded-chip')
        ->toContain('-rotate-3')
        ->toContain('<svg></svg>');
});

test('icon chip adds the float shadow only when requested', function () {
    expect(Blade::render('<x-icon-chip :shadow="true">x</x-icon-chip>'))->toContain('shadow-float');
    expect(Blade::render('<x-icon-chip>x</x-icon-chip>'))->not->toContain('shadow-float');
});

test('feature card still renders an icon chip after refactor', function () {
    $html = Blade::render('<x-feature-card icon="clock" title="Test" color="orange">Body</x-feature-card>');

    expect($html)
        ->toContain('bg-kidical-orange')
        ->toContain('size-[4.25rem]')
        ->toContain('rounded-chip')
        ->toContain('Test');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=IconChipTest`
Expected: FAIL (`x-icon-chip` component does not exist).

- [ ] **Step 3: Create the icon-chip component**

Create `resources/views/components/icon-chip.blade.php`:

```blade
@props([
    'color' => 'red', // red | blue | orange | ink | green | violet | coral
    'size' => 'md',   // sm 2.25rem | md 2.75rem | lg 4.25rem
    'shadow' => false,
])

{{-- The icon-chip motif: a tilted, rounded-square colour tile with a white icon.
     The single source of truth for the chip used by <x-feature-card>, the roze-hub
     Voor-de-rit tiles, and the feed cards. The icon (Flux or inline SVG) is the slot. --}}
@php
    // Literal class strings (NOT interpolated) so Tailwind's scanner generates them.
    $chipBg = match ($color) {
        'blue' => 'bg-kidical-blue',
        'orange' => 'bg-kidical-orange',
        'ink' => 'bg-kidical-ink',
        'green' => 'bg-kidical-green',
        'violet' => 'bg-kidical-violet',
        'coral' => 'bg-kidical-coral',
        default => 'bg-kidical-red',
    };
    $chipSize = match ($size) {
        'sm' => 'size-[2.25rem]',
        'lg' => 'size-[4.25rem]',
        default => 'size-[2.75rem]',
    };
    $chipShadow = $shadow ? 'shadow-float' : '';
@endphp

<span {{ $attributes->merge(['class' => "flex items-center justify-center shrink-0 -rotate-3 rounded-chip text-white {$chipBg} {$chipSize} {$chipShadow}"]) }}>
    {{ $slot }}
</span>
```

- [ ] **Step 4: Refactor feature-card to consume it**

In `resources/views/components/feature-card.blade.php`, delete the `@php … @endphp` block (lines 12-23, the `$chipBg` match) and replace the chip `<div>` (lines 26-28) so the file body reads:

```blade
<div {{ $attributes->merge(['class' => 'feature-card flex flex-col gap-[1.125rem] bg-white rounded-card p-10 shadow-card [&_a]:text-kidical-blue [&_a]:font-bold [&_a]:bg-none [&_a:hover]:underline']) }}>
    <x-icon-chip :color="$color" size="lg">
        <flux:icon name="{{ $icon }}" variant="solid" class="size-[2.4rem] text-white" aria-hidden="true" />
    </x-icon-chip>
    <h3 class="text-kidical-ink">{{ $title }}</h3>
    <p class="text-[1.3125rem] leading-[1.6] text-kidical-ink/75">{{ $slot }}</p>
</div>
```

(Keep the `@props` block at the top unchanged.)

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --compact --filter=IconChipTest`
Expected: PASS (all 3).

- [ ] **Step 6: Run the CSS architecture test**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS (icon-chip uses only token utilities + rem arbitrary values; no raw hex/px).

- [ ] **Step 7: Commit**

```bash
git add resources/views/components/icon-chip.blade.php resources/views/components/feature-card.blade.php tests/Feature/IconChipTest.php
git commit -m "refactor(components): extract <x-icon-chip> from feature-card"
```

---

### Task 3: Hub sub-nav ordering logic (`HubTabs`)

**Files:**
- Create: `app/Support/RozeHub/HubTabs.php`
- Test: `tests/Feature/RozeHub/HubTabsTest.php`

**Interfaces:**
- Produces: `App\Support\RozeHub\HubTabs::for(Group $group, string $active, bool $isCaptain, bool $showWelcome): array` returning an ordered list of `['key' => string, 'label' => string, 'route' => ?string, 'external' => bool, 'active' => bool]`. Internal tabs carry a route name in `route`; the Beheer tab has `route => null, external => true`. Consumed by Task 4.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/RozeHub/HubTabsTest.php`:

```php
<?php

use App\Models\Group;
use App\Support\RozeHub\HubTabs;

function tabKeys(array $tabs): array
{
    return array_map(fn (array $t) => $t['key'], $tabs);
}

test('new hesje (welcome on) puts Aan de slag second', function () {
    $tabs = HubTabs::for(new Group, 'overzicht', isCaptain: false, showWelcome: true);

    expect(tabKeys($tabs))->toBe([
        'overzicht', 'aan-de-slag', 'agenda', 'fotos', 'groep', 'materiaal',
    ]);
});

test('established hesje (welcome off) puts Aan de slag last, no Beheer', function () {
    $tabs = HubTabs::for(new Group, 'overzicht', isCaptain: false, showWelcome: false);

    expect(tabKeys($tabs))->toBe([
        'overzicht', 'agenda', 'fotos', 'groep', 'materiaal', 'aan-de-slag',
    ]);
});

test('captain gets Aan de slag second-to-last and Beheer last (external)', function () {
    $tabs = HubTabs::for(new Group, 'agenda', isCaptain: true, showWelcome: false);

    expect(tabKeys($tabs))->toBe([
        'overzicht', 'agenda', 'fotos', 'groep', 'materiaal', 'aan-de-slag', 'beheer',
    ]);

    $beheer = end($tabs);
    expect($beheer['external'])->toBeTrue();
    expect($beheer['route'])->toBeNull();
});

test('the active key is flagged on exactly one tab', function () {
    $tabs = HubTabs::for(new Group, 'agenda', isCaptain: false, showWelcome: false);

    $active = array_values(array_filter($tabs, fn (array $t) => $t['active']));
    expect($active)->toHaveCount(1);
    expect($active[0]['key'])->toBe('agenda');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=HubTabsTest`
Expected: FAIL (`App\Support\RozeHub\HubTabs` not found).

- [ ] **Step 3: Implement HubTabs**

Create `app/Support/RozeHub/HubTabs.php`:

```php
<?php

namespace App\Support\RozeHub;

use App\Models\Group;

class HubTabs
{
    /**
     * Ordered hub sub-nav tabs for one chapter and the current viewer's state.
     *
     * Order rules:
     *  - base: Overzicht, Agenda, Foto's, De Groep, Materiaal
     *  - Aan de slag floats: 2nd while inside the welcome window (non-captain);
     *    for captains it sits second-to-last (just before Beheer); otherwise last.
     *  - Beheer: captains only, always last, flagged as leaving the hub (external).
     *
     * @return array<int, array{key: string, label: string, route: ?string, external: bool, active: bool}>
     */
    public static function for(Group $group, string $active, bool $isCaptain, bool $showWelcome): array
    {
        $keys = ['overzicht', 'agenda', 'fotos', 'groep', 'materiaal'];

        if ($isCaptain) {
            $keys[] = 'aan-de-slag';
        } elseif ($showWelcome) {
            array_splice($keys, 1, 0, ['aan-de-slag']);
        } else {
            $keys[] = 'aan-de-slag';
        }

        $tabs = array_map(fn (string $key) => [
            'key' => $key,
            'label' => self::LABELS[$key],
            'route' => self::ROUTES[$key],
            'external' => false,
            'active' => $key === $active,
        ], $keys);

        if ($isCaptain) {
            $tabs[] = [
                'key' => 'beheer',
                'label' => 'Beheer',
                'route' => null,
                'external' => true,
                'active' => false,
            ];
        }

        return $tabs;
    }

    private const LABELS = [
        'overzicht' => 'Overzicht',
        'aan-de-slag' => 'Aan de slag',
        'agenda' => 'Agenda',
        'fotos' => "Foto's",
        'groep' => 'De Groep',
        'materiaal' => 'Materiaal',
    ];

    private const ROUTES = [
        'overzicht' => 'groups.roze-hesjes',
        'aan-de-slag' => 'groups.roze-hesjes.aan-de-slag',
        'agenda' => 'groups.roze-hesjes.agenda',
        'fotos' => 'groups.roze-hesjes.fotos',
        'groep' => 'groups.roze-hesjes.groep',
        'materiaal' => 'groups.roze-hesjes.materiaal',
    ];
}
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --compact --filter=HubTabsTest`
Expected: PASS (all 4).

- [ ] **Step 5: Commit**

```bash
git add app/Support/RozeHub/HubTabs.php tests/Feature/RozeHub/HubTabsTest.php
git commit -m "feat(roze-hesjes): hub sub-nav tab ordering logic"
```

---

### Task 4: `<x-roze-subnav>` and `<x-roze-hub>` chrome components

**Files:**
- Create: `resources/views/components/roze-subnav.blade.php`
- Create: `resources/views/components/roze-hub.blade.php`
- Test: `tests/Feature/RozeHubComponentTest.php`

**Interfaces:**
- Consumes: `HubTabs::for(...)` (Task 3); `<x-icon-chip>` is NOT used here. CSS classes from Task 1.
- Produces: `<x-roze-hub :group="$group" active="overzicht" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl">…body…</x-roze-hub>`. Consumed by Tasks 6 and 7.

**Note on the wrapper tag:** the current `groups/roze-hesjes.blade.php` opens with `<x-layouts::site title="Kidical Mass {{ $group->name }}">`. Use that exact tag form in `roze-hub.blade.php`.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/RozeHubComponentTest.php`:

```php
<?php

use App\Models\Group;
use Illuminate\Support\Facades\Blade;

test('roze-hub renders the chapter name in the compact hero', function () {
    $group = Group::factory()->create(['name' => 'Schaarbeek']);

    $html = Blade::render(
        '<x-roze-hub :group="$group" active="overzicht" :is-captain="false" :show-welcome="false">BODY</x-roze-hub>',
        ['group' => $group],
    );

    expect($html)
        ->toContain('roze-hub-hero')
        ->toContain('Kidical Mass Schaarbeek')
        ->toContain('BODY');
});

test('the active tab carries the active modifier class', function () {
    $group = Group::factory()->create(['name' => 'Schaarbeek']);

    $html = Blade::render(
        '<x-roze-hub :group="$group" active="agenda" :is-captain="false" :show-welcome="false">x</x-roze-hub>',
        ['group' => $group],
    );

    expect($html)->toContain('roze-subnav__tab--active');
});

test('Beheer appears only for captains', function () {
    $group = Group::factory()->create(['name' => 'Schaarbeek']);

    $captainHtml = Blade::render(
        '<x-roze-hub :group="$group" active="overzicht" :is-captain="true" :show-welcome="false" beheer-url="/admin">x</x-roze-hub>',
        ['group' => $group],
    );
    $memberHtml = Blade::render(
        '<x-roze-hub :group="$group" active="overzicht" :is-captain="false" :show-welcome="false">x</x-roze-hub>',
        ['group' => $group],
    );

    expect($captainHtml)->toContain('roze-subnav__beheer');
    expect($memberHtml)->not->toContain('roze-subnav__beheer');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=RozeHubComponentTest`
Expected: FAIL (`x-roze-hub` not found).

- [ ] **Step 3: Create the sub-nav component**

Create `resources/views/components/roze-subnav.blade.php`:

```blade
@props([
    'tabs',              // array from HubTabs::for(...)
    'group',             // App\Models\Group — for route() binding
    'beheerUrl' => null, // external Filament URL for the Beheer tab
])

<nav class="roze-subnav" aria-label="Roze-hesje hub">
    <ul class="roze-subnav__list" role="list">
        @foreach ($tabs as $tab)
            <li>
                @if ($tab['external'])
                    <a href="{{ $beheerUrl }}" class="roze-subnav__beheer">
                        {{-- wrench --}}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.5 2.5-2.4-2.4 2.5-2.5z"></path></svg>
                        <span>{{ $tab['label'] }}</span>
                        {{-- external arrow --}}
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"></path></svg>
                    </a>
                @else
                    <a
                        href="{{ route($tab['route'], $group) }}"
                        @class([
                            'roze-subnav__tab',
                            'roze-subnav__tab--active' => $tab['active'],
                        ])
                        @if ($tab['active']) aria-current="page" @endif
                    >{{ $tab['label'] }}</a>
                @endif
            </li>
        @endforeach
    </ul>
</nav>
```

- [ ] **Step 4: Create the hub layout component**

Create `resources/views/components/roze-hub.blade.php`:

```blade
@props([
    'group',
    'active',
    'isCaptain' => false,
    'showWelcome' => false,
    'beheerUrl' => null,
])

@php
    $tabs = \App\Support\RozeHub\HubTabs::for($group, $active, (bool) $isCaptain, (bool) $showWelcome);
@endphp

<x-layouts::site title="Kidical Mass {{ $group->name }}">
    <header class="roze-hub-hero">
        <h1>Kidical Mass {{ $group->name }}</h1>
        <img src="{{ asset('img/logos/logo-icon.png') }}" alt="" aria-hidden="true" class="roze-hub-hero__mark">
    </header>

    <x-roze-subnav :tabs="$tabs" :group="$group" :beheer-url="$beheerUrl" />

    <div class="roze-hub-body">
        {{ $slot }}
    </div>
</x-layouts::site>
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --compact --filter=RozeHubComponentTest`
Expected: PASS (all 3). If `route()` errors on a missing locale param, the routes do not exist yet — they are created in Task 5; re-run this test after Task 5 if so. To keep this task self-contained, the test only asserts component structure, but `route()` needs the named routes to resolve. **If Step 5 fails only on `Route … not defined`, proceed to Task 5 and run both test files together at Task 5 Step 6.**

- [ ] **Step 6: Run the CSS architecture test**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add resources/views/components/roze-subnav.blade.php resources/views/components/roze-hub.blade.php tests/Feature/RozeHubComponentTest.php
git commit -m "feat(roze-hesjes): <x-roze-hub> + <x-roze-subnav> chrome"
```

> **Reviewer note:** the route names referenced by `HubTabs`/`roze-subnav` are defined in Task 5. Tasks 4 and 5 may be reviewed as a pair if `route()` resolution blocks Task 4's render test.

---

### Task 5: `RozeHesjeController` + routes (the 6-action split, access control)

**Files:**
- Create: `app/Http/Controllers/RozeHesjeController.php`
- Modify: `routes/web.php` (lines 44-55 region — repoint `groups.roze-hesjes`, add 5 sub-routes; keep `groups.ride-preview`)
- Modify: `app/Http/Controllers/GroupController.php` (remove `rozeHesjes()` and its `ROZE_WELCOME_WEEKS` const, and `ridePreview()` — moved into the new controller; remove now-unused imports)
- Test: `tests/Feature/RozeHesjeHubTest.php`

**Interfaces:**
- Consumes: `<x-roze-hub>` (Task 4). Group membership pivot `role` column (`'captain'`).
- Produces: routes named `groups.roze-hesjes`, `groups.roze-hesjes.aan-de-slag`, `.agenda`, `.fotos`, `.groep`, `.materiaal`; view data per action (see below). View templates are created in Tasks 6-7 — this task creates **temporary one-line stub views** so routes resolve, replaced in Tasks 6-7.

- [ ] **Step 1: Write the failing access test**

Create `tests/Feature/RozeHesjeHubTest.php` (mirrors the existing membership pattern in `tests/Feature/GroupsTest.php`):

```php
<?php

use App\Models\Group;
use App\Models\User;

use function Pest\Laravel\actingAs;

/** @return array<int, string> the 6 hub route names */
function hubRoutes(): array
{
    return [
        'groups.roze-hesjes',
        'groups.roze-hesjes.aan-de-slag',
        'groups.roze-hesjes.agenda',
        'groups.roze-hesjes.fotos',
        'groups.roze-hesjes.groep',
        'groups.roze-hesjes.materiaal',
    ];
}

function hubUrl(string $name, Group $group): string
{
    return route($name, ['locale' => 'nl', 'group' => $group]);
}

test('a member can open every hub page', function (string $name) {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl($name, $group))->assertOk();
})->with(hubRoutes());

test('a logged-in non-member is forbidden from every hub page', function (string $name) {
    $group = Group::factory()->create();

    actingAs(User::factory()->create())->get(hubUrl($name, $group))->assertForbidden();
})->with(hubRoutes());

test('the Beheer link shows for a captain and not for a plain member', function () {
    $group = Group::factory()->create();
    $captain = User::factory()->create();
    $plain = User::factory()->create();
    $group->users()->attach($captain, ['role' => 'captain']);
    $group->users()->attach($plain, ['role' => null]);

    actingAs($captain)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertSee('roze-subnav__beheer', escape: false);
    actingAs($plain)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertDontSee('roze-subnav__beheer', escape: false);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=RozeHesjeHubTest`
Expected: FAIL (routes not defined / controller missing).

- [ ] **Step 3: Create the controller with `hubContext` + faux feed**

Create `app/Http/Controllers/RozeHesjeController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cookie;

/**
 * The roze-hesje hub for one chapter: a logged-in, membership-gated section that
 * lives inside the public site. One Overview page plus 5 sub-pages, all sharing the
 * compact pink hero + sub-nav chrome (<x-roze-hub>). Backend for the feed, galleries,
 * draft-state and per-group links is faux/seeded for now (Nico, GitHub #37).
 */
class RozeHesjeController extends Controller
{
    /** How long the welcome block + "nieuw" marker stay visible, from first visit. */
    private const ROZE_WELCOME_WEEKS = 2;

    public function overview(string $locale, Group $group): View
    {
        return view('groups.roze-hesjes.overzicht', [
            ...$this->hubContext($group),
            'feed' => $this->fauxFeed($group),
        ]);
    }

    public function aanDeSlag(string $locale, Group $group): View
    {
        return view('groups.roze-hesjes.aan-de-slag', $this->hubContext($group));
    }

    public function agenda(string $locale, Group $group): View
    {
        $context = $this->hubContext($group);

        $groupIds = collect([$group->id]);
        $currentParent = $group->parent;
        while ($currentParent) {
            $groupIds->push($currentParent->id);
            $currentParent = $currentParent->parent;
        }

        $activities = Activity::query()
            ->with(['author', 'groups'])
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->get();

        $lead = $activities->first()?->author ?? $group->users->sortBy('name')->first();

        return view('groups.roze-hesjes.agenda', [
            ...$context,
            'activities' => $activities,
            'lead' => $lead,
        ]);
    }

    public function fotos(string $locale, Group $group): View
    {
        return view('groups.roze-hesjes.fotos', $this->hubContext($group));
    }

    public function groep(string $locale, Group $group): View
    {
        return view('groups.roze-hesjes.groep', [
            ...$this->hubContext($group),
            'roster' => $group->users->sortBy('name')->values(),
            'newMemberCutoff' => now()->subWeeks(self::ROZE_WELCOME_WEEKS),
        ]);
    }

    public function materiaal(string $locale, Group $group): View
    {
        return view('groups.roze-hesjes.materiaal', $this->hubContext($group));
    }

    /**
     * Shared chrome data + membership guard for every hub page.
     *
     * @return array{group: Group, isCaptain: bool, showWelcome: bool, beheerUrl: string}
     */
    private function hubContext(Group $group): array
    {
        $group->load(['users', 'children', 'parent']);

        $user = request()->user();
        abort_unless($user !== null && $group->users->contains('id', $user->id), 403);

        $membership = $group->users->firstWhere('id', $user->id);
        $isCaptain = $membership?->pivot->role === 'captain';

        // Time-boxed welcome: shown only during a hesje's first weeks. A per-group cookie
        // records the first visit; after the window the block auto-hides (does not reset).
        // Per-browser for now; a per-user flag is a later backend concern (Nico).
        $cookieKey = 'roze_welcome_'.$group->id;
        $firstSeen = request()->cookie($cookieKey);

        if ($firstSeen === null) {
            $showWelcome = true;
            Cookie::queue($cookieKey, now()->toIso8601String(), 60 * 24 * 90);
        } else {
            $showWelcome = Carbon::parse($firstSeen)->greaterThan(now()->subWeeks(self::ROZE_WELCOME_WEEKS));
        }

        // Beheer leaves the hub for the Filament panel. Panel root for now; the exact
        // group-edit deep-link is a later backend concern (Nico #37).
        $beheerUrl = url('/admin');

        return compact('group', 'isCaptain', 'showWelcome', 'beheerUrl');
    }

    /**
     * Faux change-feed (newest first). Each item deep-links to its exact target.
     * Real records come from the change-feed Nico builds (GitHub #37).
     *
     * @return array<int, array{type: string, color: string, icon: string, what: string, context: string, timestamp: string, relative: string, href: string}>
     */
    private function fauxFeed(Group $group): array
    {
        return [
            [
                'type' => 'photos',
                'color' => 'blue',
                'icon' => 'image',
                'what' => "3 nieuwe foto's van de rit van zondag",
                'context' => 'Rit van zondag',
                'timestamp' => now()->subDays(2)->toDateString(),
                'relative' => '2 dagen geleden',
                'href' => route('groups.roze-hesjes.fotos', $group),
            ],
            [
                'type' => 'draft',
                'color' => 'orange',
                'icon' => 'pencil',
                'what' => 'De Halloweenrit krijgt vorm',
                'context' => 'Route gewijzigd',
                'timestamp' => now()->subDays(3)->toDateString(),
                'relative' => '3 dagen geleden',
                'href' => route('groups.ride-preview', $group),
            ],
            [
                'type' => 'member',
                'color' => 'red',
                'icon' => 'user-plus',
                'what' => 'Sara rijdt nu mee als roze hesje',
                'context' => 'Nieuw lid',
                'timestamp' => now()->subDays(5)->toDateString(),
                'relative' => '5 dagen geleden',
                'href' => route('groups.roze-hesjes.groep', $group),
            ],
        ];
    }
}
```

- [ ] **Step 4: Add the routes**

In `routes/web.php`, replace the existing `groups.roze-hesjes` route block (lines 44-49) with the 6-route set (keep the `BackstageDemoAccess` middleware on each):

```php
        // Roze-hesje hub — the logged-in-only chapter section (replaces the old backstage).
        // Lives in the public framework with a compact roze hero + sub-nav; gated on chapter
        // membership. BackstageDemoAccess keeps the demo frictionless (auto-login outside prod).
        Route::middleware(BackstageDemoAccess::class)->group(function (): void {
            Route::get('chapters/{group}/roze-hesjes', [RozeHesjeController::class, 'overview'])->name('groups.roze-hesjes');
            Route::get('chapters/{group}/roze-hesjes/aan-de-slag', [RozeHesjeController::class, 'aanDeSlag'])->name('groups.roze-hesjes.aan-de-slag');
            Route::get('chapters/{group}/roze-hesjes/agenda', [RozeHesjeController::class, 'agenda'])->name('groups.roze-hesjes.agenda');
            Route::get('chapters/{group}/roze-hesjes/fotos', [RozeHesjeController::class, 'fotos'])->name('groups.roze-hesjes.fotos');
            Route::get('chapters/{group}/roze-hesjes/groep', [RozeHesjeController::class, 'groep'])->name('groups.roze-hesjes.groep');
            Route::get('chapters/{group}/roze-hesjes/materiaal', [RozeHesjeController::class, 'materiaal'])->name('groups.roze-hesjes.materiaal');
        });
```

Add the import near the top of `routes/web.php` (with the other controller `use` statements):

```php
use App\Http\Controllers\RozeHesjeController;
```

Leave the `groups.ride-preview` route (lines 51-55) unchanged.

- [ ] **Step 5: Create temporary stub views so routes resolve**

Create six minimal stub views (replaced in Tasks 6-7). Each:

```blade
{{-- TEMPORARY stub — replaced in Task 6/7 --}}
<x-roze-hub :group="$group" active="overzicht" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl">
    stub
</x-roze-hub>
```

Create at: `resources/views/groups/roze-hesjes/overzicht.blade.php`, `aan-de-slag.blade.php`, `agenda.blade.php`, `fotos.blade.php`, `groep.blade.php`, `materiaal.blade.php` — each with the matching `active` key (`overzicht`/`aan-de-slag`/`agenda`/`fotos`/`groep`/`materiaal`).

- [ ] **Step 6: Remove the moved methods from `GroupController`**

In `app/Http/Controllers/GroupController.php`, delete the `rozeHesjes()` method (lines ~132-178), the `ridePreview()` method (lines ~185-191) **only if** moving ride-preview too — **do NOT move `ridePreview()`; leave it in `GroupController`** (its route still points there). Delete only `rozeHesjes()` and the `ROZE_WELCOME_WEEKS` const (lines ~121-125). Remove any imports left unused by that deletion (check `Activity`, `Carbon`, `Cookie` are still used elsewhere in the file before removing).

> Correction for clarity: `groups.ride-preview` still maps to `GroupController@ridePreview`, so **keep `ridePreview()` in `GroupController`**. Only `rozeHesjes()` + its const move out.

- [ ] **Step 7: Run the tests**

Run: `php artisan test --compact --filter="RozeHesjeHubTest|RozeHubComponentTest|HubTabsTest"`
Expected: PASS (member 200 ×6, non-member 403 ×6, Beheer captain/member, plus Task 3/4 tests).

- [ ] **Step 8: Run pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/RozeHesjeController.php app/Http/Controllers/GroupController.php routes/web.php resources/views/groups/roze-hesjes/ tests/Feature/RozeHesjeHubTest.php
git commit -m "feat(roze-hesjes): RozeHesjeController + 6-route hub split"
```

---

### Task 6: Overview page — welcome panel, Voor de rit tiles, feed cards

**Files:**
- Create: `resources/views/components/roze-feed-card.blade.php`
- Modify: `resources/views/groups/roze-hesjes/overzicht.blade.php` (replace the stub)
- Modify: `resources/css/pages/chapters-roze-hesjes.css` (add tile + feed-shell + welcome-panel rules)
- Test: `tests/Feature/RozeHesjeHubTest.php` (add Overview-body assertions)

**Interfaces:**
- Consumes: `<x-roze-hub>` (Task 4), `<x-icon-chip>` (Task 2), `$feed` array (Task 5 `fauxFeed`), `$showWelcome`.
- Produces: `<x-roze-feed-card :color :icon :what :context :timestamp :relative :href />` (the flexible card shell).

- [ ] **Step 1: Write the failing Overview-body test**

Append to `tests/Feature/RozeHesjeHubTest.php`:

```php
test('the Overview shows the Voor de rit tiles and the feed', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertSee('Voor de rit')
        ->assertSee('Speech')
        ->assertSee('Playlist')
        ->assertSee('Sinds je laatste bezoek')
        ->assertSee("3 nieuwe foto's van de rit van zondag")
        ->assertSee('Sara rijdt nu mee als roze hesje');
});

test('the welcome panel shows on a first visit and hides afterwards', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    // First visit: no cookie yet -> welcome shown.
    actingAs($member)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertSee('Welkom bij de roze hesjes');

    // Past the window: cookie dated long ago -> hidden.
    actingAs($member)
        ->withCookie('roze_welcome_'.$group->id, now()->subWeeks(4)->toIso8601String())
        ->get(hubUrl('groups.roze-hesjes', $group))
        ->assertDontSee('Welkom bij de roze hesjes');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=RozeHesjeHubTest`
Expected: FAIL (stub overzicht has none of this content).

- [ ] **Step 3: Create the feed-card component**

Create `resources/views/components/roze-feed-card.blade.php`:

```blade
@props([
    'color' => 'blue',
    'icon' => 'image', // image | pencil | user-plus | calendar
    'what',
    'context',
    'timestamp', // ISO date for <time datetime>
    'relative',  // human label, e.g. "2 dagen geleden"
    'href',
])

@php
    $iconSvg = match ($icon) {
        'pencil' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'user-plus' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        default => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="M21 15l-5-5L5 21"/>',
    };
@endphp

<a href="{{ $href }}" class="roze-feed">
    <x-icon-chip :color="$color" size="md" :shadow="true">
        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $iconSvg !!}</svg>
    </x-icon-chip>
    <span class="roze-feed__body">
        <span class="roze-feed__what">{{ $what }}</span>
        <span class="roze-feed__meta">{{ $context }} · <time datetime="{{ $timestamp }}">{{ $relative }}</time></span>
    </span>
    <svg class="roze-feed__chev" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
</a>
```

- [ ] **Step 4: Build the Overview body**

Replace `resources/views/groups/roze-hesjes/overzicht.blade.php` with:

```blade
<x-roze-hub :group="$group" active="overzicht" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl">
    <div class="roze-overview">
        @if ($showWelcome)
            <div class="roze-welcome">
                <h2>Welkom bij de roze hesjes van {{ $group->name }}</h2>
                <p>Fijn dat je meerijdt. Begin bij <a href="{{ route('groups.roze-hesjes.aan-de-slag', $group) }}">Aan de slag</a> om je weg te vinden. Dit bericht verdwijnt vanzelf na je eerste weken.</p>
            </div>
        @endif

        <section class="roze-grab">
            <span class="roze-grab__label">Voor de rit</span>
            <div class="roze-grab__tiles">
                <a href="{{ route('groups.roze-hesjes.materiaal', $group) }}" class="roze-tile">
                    <x-icon-chip color="orange" size="sm" :shadow="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                    </x-icon-chip>
                    <span class="roze-tile__label">Speech</span>
                </a>
                {{-- Per-chapter playlist URL is faux for now (Nico #37). --}}
                <a href="{{ route('groups.roze-hesjes.materiaal', $group) }}" class="roze-tile">
                    <x-icon-chip color="violet" size="sm" :shadow="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </x-icon-chip>
                    <span class="roze-tile__label">Playlist</span>
                </a>
            </div>
        </section>

        <section class="roze-feeds">
            <h2>Sinds je laatste bezoek</h2>
            <div class="roze-feeds__list">
                @foreach ($feed as $item)
                    <x-roze-feed-card
                        :color="$item['color']"
                        :icon="$item['icon']"
                        :what="$item['what']"
                        :context="$item['context']"
                        :timestamp="$item['timestamp']"
                        :relative="$item['relative']"
                        :href="$item['href']"
                    />
                @endforeach
            </div>
        </section>
    </div>
</x-roze-hub>
```

- [ ] **Step 5: Add the Overview CSS**

Append to `resources/css/pages/chapters-roze-hesjes.css`, inside the file's existing `@layer` wrapper (match its indentation/structure):

```css
/* === Roze-hub Overview ================================================= */
.roze-overview {
    display: flex;
    flex-direction: column;
    gap: 1.375rem;
}

.roze-welcome {
    background: color-mix(in oklab, var(--color-kidical-red), transparent 90%);
    border-radius: 1.5rem;
    padding: 0.875rem 0.9375rem;
}

.roze-welcome > h2 {
    margin: 0 0 0.25rem;
    color: var(--color-kidical-red);
    font-size: 1.05rem;
}

.roze-welcome p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--color-text-body);
}

.roze-grab {
    display: flex;
    flex-direction: column;
    gap: 0.5625rem;
}

.roze-grab__label {
    font-family: var(--font-sans);
    font-weight: 700;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%);
}

.roze-grab__tiles {
    display: flex;
    gap: 0.625rem;
}

.roze-tile {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.5625rem;
    text-decoration: none;
    background: #fff;
    border: 1px solid var(--color-kidical-hairline);
    border-radius: 1rem;
    padding: 0.625rem 0.75rem;
    transition: transform 0.18s var(--ease-brand), box-shadow 0.18s var(--ease-brand);
}

.roze-tile:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-hover);
}

.roze-tile__label {
    font-family: var(--font-sans);
    font-weight: 700;
    font-size: 0.92rem;
    color: var(--color-kidical-ink);
}

.roze-feeds {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.roze-feeds > h2 {
    margin: 0;
    font-size: 1.3rem;
}

.roze-feeds__list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.roze-feed {
    display: flex;
    align-items: center;
    gap: 0.8125rem;
    text-decoration: none;
    background: #fff;
    border-radius: 1.5rem;
    box-shadow: var(--shadow-float);
    padding: 0.8125rem 0.9375rem;
    transition: transform 0.18s var(--ease-brand), box-shadow 0.18s var(--ease-brand);
}

.roze-feed:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

.roze-feed__body {
    flex: 1;
    min-width: 0;
}

.roze-feed__what {
    display: block;
    font-family: var(--font-sans);
    font-weight: 700;
    font-size: 0.97rem;
    line-height: 1.3;
    color: var(--color-kidical-ink);
}

.roze-feed__meta {
    display: block;
    font-family: var(--font-sans);
    font-size: 0.78rem;
    color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%);
    margin-top: 0.1875rem;
}

.roze-feed__chev {
    flex: none;
    color: color-mix(in oklab, var(--color-kidical-ink), transparent 62%);
}

@media (min-width: 48rem) {
    .roze-overview {
        gap: 1.5rem;
    }

    .roze-feeds > h2 {
        font-size: 1.6rem;
    }

    .roze-feed {
        padding: 1rem 1.125rem;
    }
}

@media (prefers-reduced-motion: reduce) {
    .roze-tile,
    .roze-feed {
        transition: none;
    }
}
```

- [ ] **Step 6: Run the tests**

Run: `php artisan test --compact --filter=RozeHesjeHubTest`
Expected: PASS (access + Beheer + Overview-body + welcome on/off).

- [ ] **Step 7: Run the CSS architecture test**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS (feed-card + tiles use only token utilities / rem; the page CSS partial is already registered).

- [ ] **Step 8: Run pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/roze-feed-card.blade.php resources/views/groups/roze-hesjes/overzicht.blade.php resources/css/pages/chapters-roze-hesjes.css tests/Feature/RozeHesjeHubTest.php
git commit -m "feat(roze-hesjes): Overview body — welcome, Voor de rit, feed"
```

---

### Task 7: Migrate the 5 sub-pages and retire the old single page

**Files:**
- Modify (replace stubs): `resources/views/groups/roze-hesjes/aan-de-slag.blade.php`, `agenda.blade.php`, `fotos.blade.php`, `groep.blade.php`, `materiaal.blade.php`
- Delete: `resources/views/groups/roze-hesjes.blade.php` (the old single page; its `roze-hesjes/` directory replaces it)
- Modify: `resources/css/pages/chapters-roze-hesjes.css` (remove the now-dead `.roze-head` rules)
- Test: `tests/Feature/RozeHesjeHubTest.php` (add per-sub-page landmark assertions)

**Interfaces:**
- Consumes: `<x-roze-hub>`, and the migrated sections' existing components (`<x-feature-card>`, `<x-cta-button>`, `<x-ride-day>`, roster markup) — moved verbatim from the old file.

**Migration map** — open the old `resources/views/groups/roze-hesjes.blade.php` (242 lines) and move each section's inner markup (everything EXCEPT the old `<header class="roze-head">` hero) into the matching sub-page, wrapped in `<x-roze-hub>` with the correct `active` key. Drop the old `@if ($showWelcome)` welkom block from the migration (it now lives only on the Overview). Carry any inline `@php` faux arrays (`$materials`, gallery cells) with their section.

| Sub-page (`active`) | Sections moved from old file (line ranges) |
|---|---|
| `aan-de-slag` (`aan-de-slag`) | "Voor je eerste rit" onboarding (L155-204: feature cards, video, `roze-steps`) + "WhatsApp-doorgang" (L232-240) |
| `agenda` (`agenda`) | "Op de agenda" (L83-111: `<x-ride-day>` loop, "Alle activiteiten" CTA, "In voorbereiding" draft block) — uses `$activities` + `$lead` |
| `fotos` (`fotos`) | "Foto's" gallery (L115-128) |
| `groep` (`groep`) | "De roze hesjes" roster (L132-150) — uses `$roster` + `$newMemberCutoff` |
| `materiaal` (`materiaal`) | "Jouw materiaal" (L208-227) + add the playlist link entry alongside the speech material |

Each sub-page follows this shape (example — `groep`):

```blade
<x-roze-hub :group="$group" active="groep" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl">
    {{-- moved verbatim from the old roze-hesjes.blade.php "De roze hesjes" section --}}
</x-roze-hub>
```

- [ ] **Step 1: Write the failing per-sub-page tests**

Append to `tests/Feature/RozeHesjeHubTest.php`:

```php
test('the De Groep sub-page lists a roster member by name', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create(['name' => 'Pieter Janssens']);
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl('groups.roze-hesjes.groep', $group))
        ->assertSee('Pieter Janssens');
});

test('each sub-page marks its own tab active', function (string $name, string $label) {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    $html = actingAs($member)->get(hubUrl($name, $group))->getContent();

    // The active tab carries the modifier and aria-current; assert the active label sits on it.
    expect($html)->toContain('aria-current="page"');
    expect($html)->toContain($label);
})->with([
    ['groups.roze-hesjes.aan-de-slag', 'Aan de slag'],
    ['groups.roze-hesjes.agenda', 'Agenda'],
    ['groups.roze-hesjes.fotos', "Foto's"],
    ['groups.roze-hesjes.groep', 'De Groep'],
    ['groups.roze-hesjes.materiaal', 'Materiaal'],
]);
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=RozeHesjeHubTest`
Expected: FAIL (stub sub-pages show "stub", not the roster name; active tab assertions may already pass on the stub's `active` keys — the roster assertion is the failing one).

- [ ] **Step 3: Migrate each sub-page**

Replace each of the 5 stub views per the migration map above. Move the section markup verbatim from the old file; do not redesign. For `agenda`, confirm the view uses the `$activities` / `$lead` variables the controller passes; for `groep`, `$roster` / `$newMemberCutoff`; for `materiaal`, add a playlist link entry next to the existing speech material (faux `href="#"`, with a `{{-- faux: per-chapter playlist URL (Nico #37) --}}` note).

- [ ] **Step 4: Delete the old single page + dead CSS**

```bash
git rm resources/views/groups/roze-hesjes.blade.php
```

In `resources/css/pages/chapters-roze-hesjes.css`, delete the `.roze-head` / `.roze-head__photo` rule block (the old hero is replaced by `.roze-hub-hero`). Leave all other `.roze-*` section rules intact (they style the migrated content).

- [ ] **Step 5: Run the full hub test file**

Run: `php artisan test --compact --filter=RozeHesjeHubTest`
Expected: PASS (all access, Beheer, Overview, welcome, roster, active-tab tests).

- [ ] **Step 6: Run the CSS architecture test**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS.

- [ ] **Step 7: Run pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/groups/roze-hesjes/ resources/css/pages/chapters-roze-hesjes.css tests/Feature/RozeHesjeHubTest.php
git commit -m "feat(roze-hesjes): migrate sections into the 5 hub sub-pages"
```

---

### Task 8: Full verification + visual check

**Files:** none (verification only)

- [ ] **Step 1: Run the full affected suite**

Run: `php artisan test --compact --filter="RozeHesje|RozeHub|HubTabs|IconChip|CssArchitecture|Groups"`
Expected: PASS. (Note: `CalendarProximityTest` is known-flaky in the full suite, order-dependent — not relevant here.)

- [ ] **Step 2: Build assets**

Run: `npm run build`
Expected: builds without errors (new CSS partial compiles).

- [ ] **Step 3: Visual verification (Overview, phone + desktop)**

Use the project screenshot helper or a `/tmp/*.cjs` Playwright script (HTTPS self-signed → `ignoreHTTPSErrors: true`; ESM project → `.cjs`). Log in as the seeded Schaarbeek captain/pinkvest (see `database/seeders/DemoUserSeeder.php`) and capture `…/chapters/{group}/roze-hesjes` at 392px and 1180px widths. Compare against the surface handoff:
- compact red hero (name left, sun right, no photo);
- sub-nav (phone: scroll tab strip; desktop: slim row, yellow underline on Overzicht, Beheer right with wrench + external arrow for the captain);
- welcome panel (first visit), two *Voor de rit* tiles, three feed cards with rotated colour chips + chevrons.

Batch all edits before re-screenshotting (token discipline — one screenshot pass).

- [ ] **Step 4: Update the build pipeline (P-09)**

Per `CLAUDE.md` "Updating the build pipeline": this advances P-09 Wire to 🟠 (renders + Claude visual check; 🟢 awaits Frederik's own critique pass). Run `/pipeline` or hand-edit the P-09 row + Top gaps + Roll-up in `docs/wiki/design/30-skeleton/00-page-registry.md`, and append a `## [2026-06-18] build | …` entry to `docs/wiki/log.md`. Commit separately.

---

## Self-review notes

- **Spec coverage:** routing/controller (T5), shared chrome `<x-roze-hub>` + hero + sub-nav (T1, T4), sub-nav ordering incl. Aan-de-slag float + captain Beheer (T3), icon-chip extraction (T2), Overview welcome + Voor de rit + feed shell (T6), tokens (T1, collision-safe radius handling noted), content migration (T7), state derivation isCaptain/showWelcome (T5), accessibility (components carry `aria-hidden`/`<time>`/`aria-current`), testing (T2-T8). The first-visit **redirect** to Aan de slag is intentionally NOT built (spec marked it deferrable; the welcome panel + sub-nav carry the intent) — flagged here so it is a conscious omission, not a gap.
- **Deferred (unchanged from spec):** per-type feed anatomy (shell only), Agenda → `RideCalendar` swap, all real backend (Nico #37), sub-page visual redesign.
- **Type consistency:** `HubTabs::for(Group, string, bool, bool): array` with keys `key/label/route/external/active` is produced in T3 and consumed identically in T4. `hubContext()` returns `group/isCaptain/showWelcome/beheerUrl`, matching every view's `<x-roze-hub>` props. Feed item keys (`type/color/icon/what/context/timestamp/relative/href`) match between `fauxFeed()` (T5) and `<x-roze-feed-card>` props (T6).
