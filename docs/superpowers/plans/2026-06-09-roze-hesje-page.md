# Roze-hesje Page Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the standalone backstage with a per-chapter, logged-in-only **roze-hesje page** that lives in the public site framework (same layout + ride/show kit as `groups/show.blade.php`) with a roze hero, gated on chapter membership.

**Architecture:** A new locale-prefixed route `groups.roze-hesjes` (`/{locale}/chapters/{group}/roze-hesjes`) handled by a new `GroupController::rozeHesjes` method. It reuses the existing `BackstageDemoAccess` middleware for frictionless demo login, then abort-403s any logged-in non-member. The Blade view reuses `x-layouts::site`, the `x-ride-day`/`x-ride-row` agenda primitives, and the layout's `closing` slot (mirroring `groups/show.blade.php`), but paints a roze hero and swaps the public page's kapiteins+CTA for **roster + materiaal + an inline welkomstgids**. The only change to shared chrome is a roze `🎀 {gemeente}` nav button shown to logged-in members.

**Tech Stack:** Laravel 12, Livewire/Flux, Blade, Pest 4, Tailwind v4 (token-backed role-based CSS partials), Alpine.js.

**Decisions locked (Frederik, 2026-06-09):**
- No intro between hero and agenda — straight to "Op de agenda".
- No press/downloads foot block — the "Jouw materiaal" section is the single materials home.
- Materials carry a **publiek vs besloten** visibility split (besloten = hesje-only). The roze page shows both, marked; the public page (other thread) would show only publiek.
- Welkomstgids/onboarding is folded **inline** into the roze page — no separate welcome page — split into: (a) a **compact, time-boxed welcome block** (cookie-driven, visible the first ~2 weeks from first visit, then auto-hides) for new hesjes, and (b) a **permanent onboarding section** ("Voor je eerste rit") so the same info stays findable after the welcome block expires.
- The **startspeech is NOT inline onboarding** — it is ride-leader (kapitein) material. It survives only as a *besloten* material tile labelled for ride-leaders; no inline speech section.
- Welcome window = a single constant (`ROZE_WELCOME_WEEKS = 2`), tentative — trivially changeable.
- Nav button label: just `🎀 {gemeente}`.
- "roze" = the existing `--color-kidical-red` token (#E63A7B, identical to the prototype's pink); soft/deep shades via `color-mix`. No new token.

**Source briefing:** `docs/wiki/design/30-skeleton/chapters-roze-hesjes.md` · prototype right column: `docs/wiki/design/prototype-chapter-pages.html`.

---

## File Structure

| File | Action | Responsibility |
|------|--------|----------------|
| `routes/web.php` | Modify | Register `groups.roze-hesjes` inside the locale group with `BackstageDemoAccess` middleware. |
| `app/Http/Controllers/GroupController.php` | Modify | Add `rozeHesjes()`: membership gate + typed agenda (incl. parent region) + roster + lead. |
| `resources/views/groups/roze-hesjes.blade.php` | Create | The page: roze hero → agenda → roster → materiaal → inline welkomstgids → historiek → roze closing. |
| `resources/css/pages/chapters-roze-hesjes.css` | Create | All page appearance (roze hero, roster, materiaal, onboarding, historiek, closing). Tokens only. |
| `resources/css/app.css` | Modify | Register the new partial in the `@import` block (CssArchitectureTest enforces this). |
| `resources/views/layouts/site/header.blade.php` | Modify | Roze `🎀 {gemeente}` nav button(s) for logged-in members (desktop + mobile), active on the roze page. |
| `resources/css/chrome.css` | Modify | `.roze-nav-btn` appearance (nav shell). Tokens only. |
| `tests/Feature/RozeHesjesTest.php` | Already written (RED) | 9 feature tests; drive every task. |
| `docs/wiki/design/30-skeleton/00-page-registry.md` + `docs/wiki/log.md` | Modify (final) | Pipeline status bump + log entry. |

**Already done (this thread):** isolated worktree re-based onto local `main` HEAD + the uncommitted v3 foundation; `tests/Feature/RozeHesjesTest.php` written and confirmed RED (8 fail for "route missing", 1 trivially passes); foundation suite green.

---

## Task 1: Route + controller (gating, agenda data)

**Files:**
- Modify: `routes/web.php` (after the `groups.show` line, ~line 33)
- Modify: `app/Http/Controllers/GroupController.php` (new method after `show()`)
- Test: `tests/Feature/RozeHesjesTest.php` (already written)

- [ ] **Step 1: Confirm the relevant tests fail**

Run: `php artisan test --compact --filter="roze page renders|rejects a logged-in non-member|guest with no demo volunteer|upcoming ride in the typed agenda"`
Expected: FAIL — `Route [groups.roze-hesjes] not defined`.

- [ ] **Step 2: Add the route**

In `routes/web.php`, immediately after the `groups.show` route inside the `Route::prefix('{locale}')` group:

```php
// Roze-hesje page — the logged-in-only chapter surface (replaces the old backstage).
// Lives in the public framework with a roze hero; gated on chapter membership.
// BackstageDemoAccess keeps the demo frictionless (auto-login outside production).
Route::get('chapters/{group}/roze-hesjes', [GroupController::class, 'rozeHesjes'])
    ->middleware(BackstageDemoAccess::class)
    ->name('groups.roze-hesjes');
```

(`use App\Http\Middleware\BackstageDemoAccess;` is already imported.)

- [ ] **Step 3: Add the controller method**

In `app/Http/Controllers/GroupController.php`, add `use Illuminate\Support\Facades\Cookie;` to the imports, then add this method + class constant after `show()` (`Activity` and `View` are already imported):

```php
/**
 * How long the compact welcome block stays visible to a roze hesje, measured from their
 * first visit (stored in a per-group cookie). Tentative — easy to retune.
 */
private const ROZE_WELCOME_WEEKS = 2;

/**
 * The roze-hesje page — the logged-in-only surface for one chapter (replaces the old
 * backstage). Membership-gated: a visitor must be a roze hesje of this chapter. The full
 * roster + besloten materials are visible here, not on the public page.
 */
public function rozeHesjes(string $locale, Group $group): View
{
    $group->load(['users', 'children', 'parent']);

    $user = request()->user();
    abort_unless($user !== null && $group->users->contains('id', $user->id), 403);

    // Typed upcoming agenda incl. the parent region's rides (mirrors show()).
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

    $roster = $group->users->sortBy('name')->values();
    $lead = $activities->first()?->author ?? $roster->first();

    // Time-boxed welcome: show the compact welcome block only during a hesje's first weeks.
    // A per-group cookie records the first visit; after the window the block auto-hides, but
    // the permanent onboarding section keeps the same info findable. Per-browser for now;
    // a per-user flag is a later backend concern (Nico).
    $cookieKey = 'roze_welcome_'.$group->id;
    $firstSeen = request()->cookie($cookieKey);

    if ($firstSeen === null) {
        $showWelcome = true;
        // Persist well beyond the window so the block correctly hides (not resets) after it.
        Cookie::queue($cookieKey, now()->toIso8601String(), 60 * 24 * 90);
    } else {
        $showWelcome = \Illuminate\Support\Carbon::parse($firstSeen)
            ->greaterThan(now()->subWeeks(self::ROZE_WELCOME_WEEKS));
    }

    return view('groups.roze-hesjes', compact('group', 'activities', 'roster', 'lead', 'showWelcome'));
}
```

- [ ] **Step 4: Create a minimal view so the route resolves**

Create `resources/views/groups/roze-hesjes.blade.php` with just enough to assert against (full build in Tasks 2–5):

```blade
<x-layouts::site title="Kidical Mass {{ $group->name }}">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;
    @endphp
    <h1>{{ $group->name }}</h1>
    <h2>Op de agenda in {{ $gemeente }}</h2>
    @foreach ($activities as $activity)
        <p>{{ $activity->location }}</p>
    @endforeach
</x-layouts::site>
```

- [ ] **Step 5: Run the four tests — expect PASS**

Run: `php artisan test --compact --filter="roze page renders|rejects a logged-in non-member|guest with no demo volunteer|upcoming ride in the typed agenda"`
Expected: PASS (4 tests). The membership gate (403), the guest redirect (BackstageDemoAccess → `backstage.activate`), the member render, and the agenda location.

- [ ] **Step 6: Commit**

```bash
git add routes/web.php app/Http/Controllers/GroupController.php resources/views/groups/roze-hesjes.blade.php tests/Feature/RozeHesjesTest.php
git commit -m "feat(roze-hesjes): route + membership-gated controller"
```

---

## Task 2: Page view — roze hero, agenda, roster

**Files:**
- Modify: `resources/views/groups/roze-hesjes.blade.php`
- Test: `tests/Feature/RozeHesjesTest.php`

- [ ] **Step 1: Confirm roster test fails**

Run: `php artisan test --compact --filter="full roster"`
Expected: FAIL — view does not yet render "De roze hesjes van Mons" / member names.

- [ ] **Step 2: Build hero + agenda + roster**

Replace the body of `resources/views/groups/roze-hesjes.blade.php` (keep the `$gemeente` php block) with the structured page. Composition utilities (`grid`/`gap`/`flex`/`max-w`) stay in the template; appearance lives in the partial (Task 6). Reuse the `x-ride-day` agenda exactly like `groups/show.blade.php`.

```blade
<x-layouts::site title="Kidical Mass {{ $group->name }}">
    @php
        $gemeente = trim((string) preg_replace('/^\s*kidical\s+mass\s+/i', '', $group->name));
        $gemeente = $gemeente !== '' ? $gemeente : $group->name;

        $agendaByDay = $activities->groupBy(fn ($a) => $a->begin_date->format('Y-m-d'));
        $hasRide = $activities->contains(fn ($a) => $a->activity_type === \App\Enums\ActivityType::KIDICALMASS);

        $allActivitiesUrl = route('activities.index', ['gemeente' => $group->id]);
    @endphp

    {{-- 1 · ROZE HERO — kidical-red band, round group photo + group name. Signals the
         logged-in roze-hesje state (mirrors .chapter-head, but roze). --}}
    <header class="roze-head">
        <img src="{{ asset('img/logos/logo-icon.png') }}" alt="" aria-hidden="true" class="roze-head__daisy">
        <div class="container mx-auto px-4 roze-head__inner">
            <div class="roze-head__layout">
                <div>
                    <p class="roze-head__kicker">Roze hesjes · besloten</p>
                    <h1>{{ $group->name }}</h1>
                </div>
                <figure class="roze-head__photo">
                    <img src="{{ asset('img/photography/volunteers/volunteers-pink-vests-with-flag.jpg') }}"
                         alt="Roze hesjes van {{ $gemeente }} aan de start van een Kidical Mass">
                </figure>
            </div>
        </div>
    </header>

    {{-- 2 · OP DE AGENDA — straight to the agenda (no intro, per decision). Typed,
         day-grouped on the shared ride kit, exactly like the public page. --}}
    <section class="chapter-body chapter-agenda">
        <h2 class="chapter-section__title">Op de agenda in {{ $gemeente }}</h2>

        @unless ($hasRide)
            <p class="roze-agenda__note">Nog geen fietstocht gepland. Hou de agenda in de gaten, of plan er samen een in.</p>
        @endunless

        @if ($activities->isNotEmpty())
            <div class="chapter-agenda__list">
                @foreach ($agendaByDay as $periodKey => $dayActivities)
                    <x-ride-day :period-key="$periodKey" :rows="$dayActivities->map(fn ($a) => ['item' => $a])->values()->all()" />
                @endforeach
            </div>
            <div class="chapter-agenda__foot">
                <a href="{{ $allActivitiesUrl }}" class="chapter-next__all link-plain">Alle activiteiten in {{ $gemeente }} (ook voorbije) →</a>
            </div>
        @endif
    </section>

    {{-- 3 · DE ROZE HESJES — the full roster (replaces the public kapiteins section).
         Everyone is visible to fellow hesjes, regardless of their public opt-in. --}}
    <section class="roze-roster-band">
        <div class="container mx-auto px-4">
            <h2 class="chapter-section__title">De roze hesjes van {{ $gemeente }}</h2>
            <ul role="list" class="roze-roster">
                @foreach ($roster as $member)
                    <li class="roze-roster__member">
                        <span class="roze-roster__avatar" aria-hidden="true">{{ $member->initials() }}</span>
                        <div class="min-w-0">
                            <strong class="roze-roster__name">{{ $member->name }}</strong>
                            <span class="roze-roster__role">{{ $lead && $member->id === $lead->id ? 'Coördinator' : 'Roze hesje' }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Tasks 3–5 add: materiaal, inline welkomstgids, historiek, roze closing --}}
</x-layouts::site>
```

- [ ] **Step 3: Run the roster + renders + agenda tests — expect PASS**

Run: `php artisan test --compact --filter="full roster|roze page renders|upcoming ride in the typed agenda"`
Expected: PASS (3). Roster shows both members' full names; "De roze hesjes van Mons" present.

- [ ] **Step 4: Commit**

```bash
git add resources/views/groups/roze-hesjes.blade.php
git commit -m "feat(roze-hesjes): roze hero + typed agenda + full roster"
```

---

## Task 3: Materiaal section (publiek vs besloten)

**Files:**
- Modify: `resources/views/groups/roze-hesjes.blade.php`
- Test: `tests/Feature/RozeHesjesTest.php`

- [ ] **Step 1: Confirm materiaal test fails**

Run: `php artisan test --compact --filter="besloten downloads"`
Expected: FAIL — "Jouw materiaal" / "Besloten" not yet rendered.

- [ ] **Step 2: Add the materiaal section**

Insert before the `{{-- Tasks 3–5 add --}}` comment. Faux data, clearly commented (no per-group materials model yet — Nico's backend, GitHub #37). The `visibility` key is the honest preview of the eventual publiek/besloten field.

```blade
    {{-- 4 · JOUW MATERIAAL — the chapter's material library (replaces the public CTA).
         FAUX until the backend lands (no per-group materials model). visibility = the
         eventual publiek/besloten split: besloten = hesje-only; publiek would also show
         on the public page. The startspeech tile anchors to the inline welkomstgids. --}}
    @php
        $materials = [
            ['icon' => 'document-text', 'title' => 'Afsprakencharter', 'desc' => 'Onze afspraken voor organisatoren en hesjes.', 'tag' => 'PDF', 'visibility' => 'besloten', 'href' => '#'],
            ['icon' => 'map', 'title' => 'Zo organiseer je een rit', 'desc' => 'Route, gemeentecontact en promo, stap voor stap.', 'tag' => 'Gids', 'visibility' => 'besloten', 'href' => '#'],
            ['icon' => 'play-circle', 'title' => 'Veilig begeleiden', 'desc' => 'Korte video over meefietsen als roze hesje.', 'tag' => 'Video', 'visibility' => 'besloten', 'href' => 'https://www.youtube.com/watch?v=i9YQxJ-ChNM'],
            ['icon' => 'megaphone', 'title' => 'De startspeech', 'desc' => 'Het woordje voor de start, voor wie een rit trekt.', 'tag' => 'Voor kapiteins', 'visibility' => 'besloten', 'href' => '#'],
            ['icon' => 'arrow-down-tray', 'title' => 'Posters & promo', 'desc' => 'Affiches en flyers om in je buurt op te hangen.', 'tag' => 'Download', 'visibility' => 'publiek', 'href' => '#'],
            ['icon' => 'arrow-down-tray', 'title' => 'Flyer '.$gemeente.' 2026', 'desc' => 'De lokale flyer om uit te delen.', 'tag' => 'PDF', 'visibility' => 'publiek', 'href' => '#'],
        ];
    @endphp
    <section class="chapter-body roze-materials-section">
        <h2 class="chapter-section__title">Jouw materiaal</h2>
        <p class="roze-materials__lead">Alles op één plek. <strong>Besloten</strong> blijft bij de hesjes; <strong>publiek</strong> mag je vrij delen.</p>
        <div class="roze-materials">
            @foreach ($materials as $material)
                @php $external = \Illuminate\Support\Str::startsWith($material['href'], 'http'); @endphp
                <a href="{{ $material['href'] }}" @if ($external) target="_blank" rel="noopener" @endif class="roze-material link-plain">
                    <span class="roze-material__icon roze-material__icon--{{ $material['visibility'] }}" aria-hidden="true">
                        <flux:icon name="{{ $material['icon'] }}" variant="solid" class="size-6" />
                    </span>
                    <strong class="roze-material__title">{{ $material['title'] }}</strong>
                    <span class="roze-material__desc">{{ $material['desc'] }}</span>
                    <span class="roze-material__tags">
                        <span class="roze-material__tag">{{ $material['tag'] }}</span>
                        <span class="roze-material__badge roze-material__badge--{{ $material['visibility'] }}">{{ $material['visibility'] === 'besloten' ? 'Besloten' : 'Publiek' }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
```

- [ ] **Step 3: Run the materiaal test — expect PASS**

Run: `php artisan test --compact --filter="besloten downloads"`
Expected: PASS. Sees "Jouw materiaal", "Afsprakencharter", "Besloten", "Posters".

- [ ] **Step 4: Commit**

```bash
git add resources/views/groups/roze-hesjes.blade.php
git commit -m "feat(roze-hesjes): materiaal library with publiek/besloten split"
```

---

## Task 4: Welcome block (time-boxed) + permanent onboarding

**Files:**
- Modify: `tests/Feature/RozeHesjesTest.php` (swap the speech test for welcome + onboarding tests)
- Modify: `resources/views/groups/roze-hesjes.blade.php`

- [ ] **Step 1: Swap the test, then confirm it fails**

In `tests/Feature/RozeHesjesTest.php`, **delete** the test `'roze page folds in the welkomstgids with the start-of-ride speech'` and add these two:

```php
test('roze page greets a first-time visitor with a welcome block', function () {
    $group = rozeChapter();
    $member = User::factory()->create();
    $group->users()->attach($member);

    actingAs($member)
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertSee('Welkom bij de roze hesjes')
        ->assertCookie('roze_welcome_'.$group->id);
});

test('roze page hides the welcome block after the window but keeps the onboarding info', function () {
    $group = rozeChapter();
    $member = User::factory()->create();
    $group->users()->attach($member);

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(route('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertDontSee('Welkom bij de roze hesjes') // time-boxed block is gone
        ->assertSee('Voor je eerste rit')            // permanent onboarding stays
        ->assertSee('kindertempo');                  // its content stays findable
});
```

Run: `php artisan test --compact --filter="welcome block|onboarding info"`
Expected: FAIL — neither the welcome block nor the permanent onboarding section render yet.

- [ ] **Step 2: Add the time-boxed welcome block (under the hero)**

Insert directly after the `</header>` roze hero. A compact, contained notice (not a heading — a `<strong>` title — to respect the "no inline heading sizes" rule), gated by `$showWelcome`. It points at the permanent sections below, so nothing is lost when it expires.

```blade
    {{-- 1b · WELKOM — compact, time-boxed (per-group cookie, ~first weeks), then auto-hides.
         The same info lives permanently below in "Voor je eerste rit", so nothing is lost. --}}
    @if ($showWelcome)
        <section class="roze-welcome-section">
            <aside class="roze-welcome">
                <p class="roze-welcome__kicker">🎀 Nieuw hier?</p>
                <strong class="roze-welcome__title">Welkom bij de roze hesjes van {{ $gemeente }}!</strong>
                <p class="roze-welcome__body">Fijn dat je meerijdt. Heel even je weg vinden:</p>
                <ul class="roze-welcome__list">
                    <li>Wat een roze hesje doet en hoe je eerste rit verloopt, lees je bij <a href="#voor-je-eerste-rit" class="link-plain">Voor je eerste rit</a>.</li>
                    <li>Je charter, gids en posters staan bij <a href="#jouw-materiaal" class="link-plain">Jouw materiaal</a>.</li>
                    <li>De volgende ritten zie je bovenaan, op de agenda.</li>
                </ul>
                <p class="roze-welcome__foot">Dit welkomstbericht verdwijnt vanzelf na je eerste weken.</p>
            </aside>
        </section>
    @endif
```

- [ ] **Step 3: Add the permanent onboarding section (after the roster band)**

Replaces the old speech section. Reuses the shared `<x-feature-card>` (no new CSS) for "wat doet een roze hesje", plus the first-ride stepper. **No startspeech here** — it is a besloten material tile only (Task 3).

```blade
    {{-- 5 · VOOR JE EERSTE RIT — permanent onboarding (always here, so the welcome block's
         info stays findable after it expires). What a roze hesje does + the ride, step by
         step. The startspeech is NOT here — it is kapitein material (a besloten tile above). --}}
    <section id="voor-je-eerste-rit" class="roze-onboarding scroll-mt-24">
        <div class="container mx-auto px-4">
            <h2 class="chapter-section__title">Voor je eerste rit</h2>

            <h3 class="roze-onboarding__sub">Wat doet een roze hesje?</h3>
            <div class="roze-onboarding__cards">
                <x-feature-card icon="users" title="Je rijdt mee met de groep" color="red">
                    Je fietst naast de kinderen en houdt ze samen. Geen kopwerk, gewoon meerijden en mee opletten.
                </x-feature-card>
                <x-feature-card icon="sparkles" title="Je brengt rust en goeie energie" color="orange">
                    Een vrolijke, kalme aanwezigheid op de weg doet meer dan je denkt. Dat ben jij.
                </x-feature-card>
                <x-feature-card icon="eye" title="Goed zichtbaar zijn is genoeg" color="blue">
                    Een fluo hesje en een glimlach. Meer heb je niet nodig om het verschil te maken.
                </x-feature-card>
                <x-feature-card icon="academic-cap" title="Geen verkeersopleiding nodig" color="green">
                    Dat leer je vanzelf, samen met het team. Je staat er nooit alleen voor.
                </x-feature-card>
            </div>

            <h3 class="roze-onboarding__sub">Je eerste rit, stap voor stap</h3>
            <ol role="list" class="roze-steps">
                @foreach ([
                    ['Voor de start', 'De hesjes zitten in een gemeenschappelijke tas en worden ter plaatse uitgedeeld. Je hoeft zelf niks mee te brengen.'],
                    ['Onderweg', 'Vooraan rijdt een kapitein, achteraan een sluiter. Jij rijdt mee in de groep en houdt mee alles samen.'],
                    ['Het tempo', 'We rijden op kindertempo, ongeveer 8 à 9 km per uur. Rustig aan, het is geen koers.'],
                    ['Aan de kruispunten', 'We zetten ze samen veilig af zodat de groep kan passeren, en sluiten daarna weer aan.'],
                ] as $i => $step)
                    <li class="roze-step">
                        <span class="roze-step__num">{{ $i + 1 }}</span>
                        <div>
                            <strong class="roze-step__title">{{ $step[0] }}</strong>
                            <p class="roze-step__body">{{ $step[1] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
```

Also add `id="jouw-materiaal"` to the materiaal section opening tag from Task 3 (so the welcome block's anchor resolves): `<section id="jouw-materiaal" class="chapter-body roze-materials-section">`.

- [ ] **Step 4: Run the welcome + onboarding tests — expect PASS**

Run: `php artisan test --compact --filter="welcome block|onboarding info"`
Expected: PASS (2). First visit shows the welcome + queues the cookie; an old cookie hides the welcome but the permanent onboarding ("Voor je eerste rit" / "kindertempo") stays.

- [ ] **Step 5: Commit**

```bash
git add resources/views/groups/roze-hesjes.blade.php tests/Feature/RozeHesjesTest.php
git commit -m "feat(roze-hesjes): time-boxed welcome block + permanent onboarding"
```

---

## Task 5: Historiek + roze closing slot

**Files:**
- Modify: `resources/views/groups/roze-hesjes.blade.php`
- Test: existing `roze page renders` stays green (no new assertion; visual)

- [ ] **Step 1: Add the historiek block + roze closing**

Append after the onboarding section, before `</x-layouts::site>`. Historiek is low-key (faux copy, clearly commented). Closing uses the layout's `closing` slot painted roze, mirroring `groups/show.blade.php`'s yellow band.

```blade
    {{-- 6 · HOE HET HIER BEGON — low-key historiek foot. FAUX copy until a per-group
         history field exists; clearly removable. --}}
    <section class="chapter-body roze-histo">
        <h2 class="chapter-section__title">Hoe het hier begon</h2>
        <p class="roze-histo__body">
            Kidical Mass {{ $gemeente }} begon met een handvol gezinnen die hun straten wilden
            delen met de kinderen. Editie na editie groeide de groep, dankzij fijne partners en
            de coördinatie van Kidical Mass Belgium. Wat klein begon, rijdt nu vrolijk door de buurt.
        </p>
    </section>

    {{-- 7 · ROZE CLOSING — fused with the footer (layout closing slot, main → pb-0).
         A warm thank-you back to the public page. No recruitment CTA (these are hesjes). --}}
    <x-slot:closing>
        <section class="roze-closing">
            <div class="container mx-auto px-4 roze-closing__inner">
                <h2>Bedankt dat je mee rijdt. 🎀</h2>
                <a href="{{ route('groups.show', $group) }}" class="roze-closing__link">← Naar de publieke pagina</a>
            </div>
        </section>
    </x-slot:closing>
```

- [ ] **Step 2: Run the renders test — expect PASS (still)**

Run: `php artisan test --compact --filter="roze page renders"`
Expected: PASS.

- [ ] **Step 3: Commit**

```bash
git add resources/views/groups/roze-hesjes.blade.php
git commit -m "feat(roze-hesjes): historiek foot + roze closing band"
```

---

## Task 6: CSS partial (appearance) + register

**Files:**
- Create: `resources/css/pages/chapters-roze-hesjes.css`
- Modify: `resources/css/app.css` (add to `@import` block)
- Test: `tests/Feature/CssArchitectureTest.php` (must stay green)

- [ ] **Step 1: Create the partial**

`resources/css/pages/chapters-roze-hesjes.css`. Tokens only — "roze" = `--color-kidical-red`; soft/deep via `color-mix`. The roze hero mirrors `.chapter-head` (100vw bleed + `margin-top` cancels `<main>`'s `pt-28`). Reuses `.chapter-body`, `.chapter-section__title`, `.chapter-agenda__*`, `.chapter-next__all` from `pages/chapters.css` (already imported) for the agenda.

```css
@layer components {
    /* 1 · ROZE HERO — roze band (mirrors .chapter-head, roze instead of blue) */
    .roze-head {
        position: relative;
        z-index: 1;
        width: 100vw;
        margin-left: calc(50% - 50vw);
        margin-top: calc(var(--spacing) * -28);
        background-color: var(--color-kidical-red);
        overflow: hidden;
    }
    .roze-head__daisy {
        position: absolute;
        right: -2.5rem;
        top: 50%;
        transform: translateY(-50%) rotate(15deg);
        width: 11rem;
        opacity: 0.85;
        z-index: 1;
        pointer-events: none;
    }
    .roze-head__inner {
        position: relative;
        z-index: 2;
        padding-top: calc(var(--spacing) * 24);
        padding-bottom: calc(var(--spacing) * 9);
    }
    .roze-head__layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        align-items: center;
    }
    @media (min-width: 768px) {
        .roze-head__layout { grid-template-columns: 1.1fr 0.9fr; gap: 2.5rem; }
    }
    .roze-head__kicker {
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-size: var(--text-sm);
        color: color-mix(in oklab, white, transparent 15%);
        margin-bottom: 0.5rem;
    }
    .roze-head h1 {
        color: white;
        font-size: clamp(var(--text-5xl), 5vw, var(--text-7xl));
        line-height: 1.0;
        transform: rotate(-2deg);
        transform-origin: left;
        animation: hero-h1-in 0.6s cubic-bezier(0.22, 1, 0.36, 1) 0.05s both;
    }
    .roze-head__photo {
        margin: 0;
        aspect-ratio: 1;
        width: 100%;
        max-width: 15rem;
        justify-self: start;
        border-radius: 50%;
        overflow: hidden;
        border: 5px solid color-mix(in oklab, white, transparent 70%);
        box-shadow: 0 12px 34px -10px color-mix(in oklab, var(--color-kidical-ink), transparent 50%);
    }
    @media (min-width: 768px) { .roze-head__photo { justify-self: end; } }
    .roze-head__photo img { width: 100%; height: 100%; object-fit: cover; display: block; }

    /* 2 · AGENDA note (reuses .chapter-agenda / .chapter-agenda__list / .chapter-next__all) */
    .roze-agenda__note {
        margin-top: 0.5rem;
        color: var(--color-text-body);
        background-color: var(--color-kidical-light-yellow);
        border-radius: 0.85rem;
        padding: 0.85rem 1.1rem;
        font-weight: 600;
    }

    /* 3 · ROZE ROSTER — soft-roze band, the full team */
    .roze-roster-band {
        width: 100vw;
        margin-left: calc(50% - 50vw);
        background-color: color-mix(in oklab, var(--color-kidical-red), white 90%);
        padding-block: clamp(3rem, 6vw, 4.5rem);
    }
    .roze-roster {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    @media (min-width: 640px) { .roze-roster { grid-template-columns: repeat(2, 1fr); } }
    .roze-roster__member {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        background-color: white;
        border: 1px solid color-mix(in oklab, var(--color-kidical-red), white 75%);
        border-radius: 0.85rem;
        padding: 0.7rem 0.9rem;
    }
    .roze-roster__avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 9999px;
        background-color: var(--color-kidical-red);
        color: white;
        font-family: var(--font-heading);
        font-weight: 800;
    }
    .roze-roster__name { display: block; font-weight: 700; color: var(--color-kidical-ink); line-height: 1.2; }
    .roze-roster__role { font-size: var(--text-sm); color: var(--color-text-body); }

    /* 4 · MATERIAAL — token-backed tiles with a visibility badge */
    .roze-materials__lead { margin-bottom: 1.5rem; color: var(--color-text-body); }
    .roze-materials {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    @media (min-width: 640px) { .roze-materials { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1024px) { .roze-materials { grid-template-columns: repeat(3, 1fr); } }
    .roze-material {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        background-color: white;
        border: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 90%);
        border-radius: var(--radius-card, 1rem);
        padding: 1.25rem;
        box-shadow: var(--shadow-card, 0 3px 14px rgb(0 0 0 / 0.07));
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .roze-material:hover { transform: translateY(-2px); box-shadow: 0 10px 28px -12px color-mix(in oklab, var(--color-kidical-ink), transparent 55%); }
    .roze-material__icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 0.85rem;
        transform: rotate(-3deg);
        color: white;
    }
    .roze-material__icon--besloten { background-color: var(--color-kidical-red); }
    .roze-material__icon--publiek { background-color: var(--color-kidical-blue); }
    .roze-material__title { font-family: var(--font-heading); font-weight: 800; font-size: var(--text-lg); color: var(--color-kidical-ink); line-height: 1.2; }
    .roze-material__desc { color: var(--color-text-body); font-size: var(--text-sm); line-height: 1.4; }
    .roze-material__tags { display: flex; align-items: center; gap: 0.5rem; margin-top: auto; padding-top: 0.5rem; }
    .roze-material__tag { font-size: var(--text-xs); font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: color-mix(in oklab, var(--color-kidical-ink), transparent 50%); }
    .roze-material__badge {
        font-size: var(--text-xs);
        font-weight: 800;
        padding: 0.15rem 0.55rem;
        border-radius: 9999px;
    }
    .roze-material__badge--besloten { background-color: color-mix(in oklab, var(--color-kidical-red), white 82%); color: var(--color-kidical-red); }
    .roze-material__badge--publiek { background-color: color-mix(in oklab, var(--color-kidical-blue), white 82%); color: var(--color-kidical-blue); }

    /* 1b · WELKOM — compact, time-boxed contained notice (soft-roze card) */
    .roze-welcome-section { padding-top: clamp(1.5rem, 4vw, 2.5rem); }
    .roze-welcome {
        background-color: color-mix(in oklab, var(--color-kidical-red), white 90%);
        border: 1px solid color-mix(in oklab, var(--color-kidical-red), white 72%);
        border-radius: var(--radius-card, 1rem);
        padding: 1.5rem 1.75rem;
    }
    .roze-welcome__kicker { font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; font-size: var(--text-sm); color: var(--color-kidical-red); }
    .roze-welcome__title { display: block; font-family: var(--font-heading); font-weight: 800; font-size: var(--text-2xl); color: var(--color-kidical-ink); line-height: 1.15; margin: 0.25rem 0 0.5rem; }
    .roze-welcome__body { color: var(--color-text-body); }
    .roze-welcome__list { margin: 0.5rem 0 0; padding-left: 1.1rem; list-style: disc; display: flex; flex-direction: column; gap: 0.3rem; color: var(--color-text-body); }
    .roze-welcome__list a { color: var(--color-kidical-red); font-weight: 700; }
    .roze-welcome__foot { margin-top: 0.85rem; font-size: var(--text-sm); font-style: italic; color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%); }

    /* 5 · VOOR JE EERSTE RIT — permanent onboarding band (light-blue). The startspeech is
       gone (kapitein material). "wat doet een hesje" reuses the shared <x-feature-card>. */
    .roze-onboarding {
        width: 100vw;
        margin-left: calc(50% - 50vw);
        background-color: var(--color-kidical-light-blue);
        padding-block: clamp(3rem, 6vw, 4.5rem);
    }
    .roze-onboarding__sub { margin: 2rem 0 1rem; }
    .roze-onboarding__sub:first-of-type { margin-top: 0; }
    .roze-onboarding__cards { display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1rem; }
    @media (min-width: 768px) { .roze-onboarding__cards { grid-template-columns: repeat(2, 1fr); } }
    .roze-steps { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 1rem; max-width: 48rem; }
    .roze-step { display: flex; gap: 1rem; align-items: flex-start; background-color: white; border-radius: var(--radius-card, 1rem); box-shadow: var(--shadow-card, 0 3px 14px rgb(0 0 0 / 0.07)); padding: 1.25rem; }
    .roze-step__num { display: flex; align-items: center; justify-content: center; flex-shrink: 0; width: 2.75rem; height: 2.75rem; border-radius: 9999px; background-color: var(--color-kidical-blue); color: white; font-family: var(--font-heading); font-weight: 800; font-size: var(--text-xl); }
    .roze-step__title { display: block; font-family: var(--font-heading); font-weight: 800; font-size: var(--text-xl); color: var(--color-kidical-ink); line-height: 1.2; margin-bottom: 0.25rem; }
    .roze-step__body { color: var(--color-text-body); line-height: 1.5; }

    /* 6 · HISTORIEK — low-key foot */
    .roze-histo__body { max-width: 60ch; color: var(--color-text-body); line-height: 1.6; }

    /* 7 · ROZE CLOSING — fused with the footer (layout closing slot) */
    .roze-closing {
        width: 100vw;
        margin-left: calc(50% - 50vw);
        background-color: var(--color-kidical-red);
        color: white;
        padding-block: clamp(2.5rem, 5vw, 3.5rem);
    }
    .roze-closing__inner { display: flex; flex-direction: column; align-items: center; gap: 0.75rem; text-align: center; }
    .roze-closing h2 { color: white; }
    .roze-closing__link { color: white; font-weight: 800; }
}
```

- [ ] **Step 2: Register the partial in `app.css`**

In `resources/css/app.css`, add to the pages block (after `@import './pages/chapters.css';`):

```css
@import './pages/chapters-roze-hesjes.css';
```

- [ ] **Step 3: Build + CSS architecture test**

Run: `npm run build && php artisan test --compact --filter=CssArchitectureTest`
Expected: build clean; CssArchitectureTest PASS (partial registered, no raw hex/px in components).

- [ ] **Step 4: Commit**

```bash
git add resources/css/pages/chapters-roze-hesjes.css resources/css/app.css
git commit -m "style(roze-hesjes): page CSS partial (roze hero, roster, materiaal, onboarding)"
```

---

## Task 7: Roze nav button for logged-in members

**Files:**
- Modify: `resources/views/layouts/site/header.blade.php`
- Modify: `resources/css/chrome.css`
- Test: `tests/Feature/RozeHesjesTest.php`

- [ ] **Step 1: Confirm the nav tests fail**

Run: `php artisan test --compact --filter="roze chapter button"`
Expected: the member test FAILS (no `🎀` / roze link yet); the guest test already passes.

- [ ] **Step 2: Add the desktop nav button**

In `resources/views/layouts/site/header.blade.php`, inside the desktop `<div class="hidden md:flex items-center gap-3">`, **before** the `@auth` dropdown, add:

```blade
@auth
    @foreach (Auth::user()->groups()->where('invisible', false)->orderBy('name')->get() as $myChapter)
        <a href="{{ route('groups.roze-hesjes', $myChapter) }}"
           class="roze-nav-btn {{ request()->routeIs('groups.roze-hesjes') && optional(request()->route('group'))->is($myChapter) ? 'roze-nav-btn--active' : '' }}">
            🎀 {{ \Illuminate\Support\Str::of($myChapter->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim() }}
        </a>
    @endforeach
@endauth
```

- [ ] **Step 3: Add the mobile nav button**

In the mobile `<nav x-show="mobileOpen" ...>`, after the Support CTA line, add:

```blade
@auth
    @foreach (Auth::user()->groups()->where('invisible', false)->orderBy('name')->get() as $myChapter)
        <a href="{{ route('groups.roze-hesjes', $myChapter) }}" class="roze-nav-btn roze-nav-btn--block mb-2">
            🎀 {{ \Illuminate\Support\Str::of($myChapter->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim() }}
        </a>
    @endforeach
@endauth
```

- [ ] **Step 4: Add `.roze-nav-btn` to `chrome.css`**

```css
@layer components {
    .roze-nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background-color: var(--color-kidical-red);
        color: white;
        font-weight: 800;
        font-size: var(--text-sm);
        padding: 0.45rem 0.9rem;
        border-radius: 9999px;
        white-space: nowrap;
        text-decoration: none;
        transition: filter 0.15s ease;
    }
    .roze-nav-btn:hover { filter: brightness(1.05); }
    .roze-nav-btn--active { box-shadow: 0 0 0 3px color-mix(in oklab, var(--color-kidical-red), transparent 78%); }
    .roze-nav-btn--block { display: flex; justify-content: center; }
}
```

- [ ] **Step 5: Run the nav tests + build — expect PASS**

Run: `npm run build && php artisan test --compact --filter="roze chapter button"`
Expected: PASS (both). Member page shows `🎀` + the roze route href; guest page shows neither.

- [ ] **Step 6: Commit**

```bash
git add resources/views/layouts/site/header.blade.php resources/css/chrome.css
git commit -m "feat(roze-hesjes): roze chapter nav button for logged-in members"
```

---

## Task 8: Full verification (suite, Pint, build, screenshots)

**Files:** none (verification only)

- [ ] **Step 1: Full roze suite green**

Run: `php artisan test --compact tests/Feature/RozeHesjesTest.php`
Expected: 10 passed.

- [ ] **Step 2: Full suite green (no regressions)**

Run: `php artisan test --compact`
Expected: all green (note: `CalendarProximityTest` may flake order-dependently — re-run in isolation if it fails; it is not a regression signal).

- [ ] **Step 3: Pint**

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean.

- [ ] **Step 4: Build**

Run: `npm run build`
Expected: clean.

- [ ] **Step 5: Screenshots (Herd-linked worktree)**

Link the worktree as a Herd site, log in as a chapter member (or rely on `BackstageDemoAccess` for a seeded demo chapter), and screenshot the roze page (desktop + mobile) + the public page showing the `🎀` nav button. Use a `.cjs` Playwright script with `ignoreHTTPSErrors: true`. Visually verify: roze hero, agenda, roster, materiaal badges, inline startspeech, roze closing, nav button. **Wire stays 🟠 until Frederik's own critique/refine pass.**

---

## Task 9: Pipeline status + log (final)

**Files:**
- Modify: `docs/wiki/design/30-skeleton/00-page-registry.md`
- Modify: `docs/wiki/log.md`

- [ ] **Step 1: Update the page registry**

Per the `/pipeline` rules: update the backstage/roze row (P-11 family) — Wire 🟠 (Claude render check tops out at 🟠), Back 🟠 (faux data, awaiting Nico's per-group materials/role/visibility model). Edit the row, Top gaps cell, and Roll-up prose consistently (12 columns intact).

- [ ] **Step 2: Append a log entry**

Add a `## [2026-06-09] build | roze-hesje page` entry to `docs/wiki/log.md` summarising: route + membership gate, inline welkomstgids, materiaal publiek/besloten split, roze nav button; faux data + backend deps (GitHub #37).

- [ ] **Step 3: Commit**

```bash
git add docs/wiki/design/30-skeleton/00-page-registry.md docs/wiki/log.md
git commit -m "docs(build): roze-hesje page status + log entry"
```

---

## Open backend dependencies (spec for Nico — GitHub #37)

Carried, not built this pass (faked with clearly-commented demo data):
- Per-group **materials** model with a `visibility` (publiek/besloten) field, kapitein-editable in Filament (P-21).
- `group_user.role` (trekker / roze hesje / communicatie / foto / dj …) — roster roles currently fall back to lead heuristic + "Roze hesje".
- Per-group **photo/cover** (medialibrary) — hero currently reuses a shared volunteers photo.
- Per-group **history** text — historiek currently faux.
- Decide the **old backstage routes' fate** (`/backstage/{group}`, `/activeer/{group}`): redirect to the roze route vs keep. Deferred to a follow-up (this pass leaves them intact).
