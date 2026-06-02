# Public Site Structure — NL-only Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Stand up the full public information architecture from `docs/wiki/design/20-structure.md` as real, navigable `/nl/…` routes — reusing existing pages, adding missing ones as honest skeleton stubs.

**Architecture:** A single `/{locale}` route group (constrained to `nl` for now) wraps all public routes; a `SetLocale` middleware resolves the locale; `URL::defaults` (global, in `AppServiceProvider`) supplies the locale to every `route()` call so existing references and tests keep working. Shared chrome strings live in `lang/nl/`; page bodies stay hardcoded NL. Logged-in tier is out of scope.

**Tech Stack:** Laravel 12, Livewire/Flux, Pest 4, Tailwind v4.

---

## ⚠ Approach refinement vs. the approved spec — READ FIRST

The spec said *rename route names* to the IA vocabulary (`events.*`, `chapters.*`, `news.*`). This plan **keeps the existing route NAMES** (`activities.*`, `groups.*`, `articles.*`, `volunteer`, `home`) and changes **only the URL paths + adds the `/{locale}` prefix**. Rationale:

- Identical IA URLs are achieved (`/nl/events`, `/nl/chapters`, `/nl/about/news`) — names are internal.
- `route()` references across ~15 files (views, components, Filament resources, the iCal controller, tests) **don't change at all** — near-zero churn, far lower risk.
- The model layer stays consistent: model `Activity` → controller `ActivityController` → name `activities.*` → public URL/label "Events". "Events" is purely the public presentation name.

If the user prefers the full name-rename, that's a follow-up; this plan deliberately takes the safer path.

---

## File Structure

**New files:**
- `app/Http/Middleware/SetLocale.php` — validates `{locale}`, sets app locale + per-request URL default.
- `resources/views/components/stub.blade.php` — reusable skeleton-stub component (DRY: one component, many tiny pages).
- `resources/views/getting-started.blade.php`, `membership.blade.php`, `contact.blade.php`, `privacy.blade.php`, `cookies.blade.php` — stub pages.
- `resources/views/about/index.blade.php`, `mission.blade.php`, `vision.blade.php`, `organisation.blade.php`, `press.blade.php`, `partners.blade.php` — About section stubs.
- `lang/nl/nav.php`, `lang/nl/common.php` — chrome strings.
- `tests/Feature/LocaleRoutingTest.php`, `tests/Feature/PublicStructureTest.php`, `tests/Feature/NavigationTest.php` — tests.

**Modified files:**
- `routes/web.php` — wrap public routes in `/{locale}` group, new paths, add stub routes, keep names, root redirect.
- `bootstrap/app.php` — register the `setlocale` middleware alias.
- `app/Providers/AppServiceProvider.php` — global `URL::defaults(['locale' => 'nl'])`.
- `config/app.php` — default locale `nl`.
- `resources/views/layouts/site/header.blade.php`, `footer.blade.php` — 5-item NL nav + footer cluster.
- `tests/Feature/PublicPagesTest.php`, `tests/Feature/PageStatusTest.php` — update hardcoded paths.

---

## Task 1: Locale routing foundation + IA paths for existing pages

**Files:**
- Create: `app/Http/Middleware/SetLocale.php`
- Modify: `routes/web.php`, `bootstrap/app.php`, `app/Providers/AppServiceProvider.php`, `config/app.php`
- Test: `tests/Feature/LocaleRoutingTest.php` (create), `tests/Feature/PublicPagesTest.php`, `tests/Feature/PageStatusTest.php` (update)

- [ ] **Step 1: Write the failing locale-routing test**

Create `tests/Feature/LocaleRoutingTest.php`:

```php
<?php

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;

use function Pest\Laravel\get;

it('redirects the bare root to the nl prefix', function () {
    get('/')->assertRedirect('/nl');
});

it('serves the home page under /nl with a nl lang attribute', function () {
    get('/nl')
        ->assertOk()
        ->assertSee('lang="nl"', escape: false);
});

it('404s an unsupported locale', function () {
    get('/fr')->assertNotFound();
});

it('serves the renamed IA paths for existing pages', function () {
    $group = Group::factory()->create();
    $activity = Activity::factory()->create(['begin_date' => now()->addWeek()]);
    $activity->groups()->attach($group);
    Article::factory()->create();

    get('/nl/events')->assertOk();
    get('/nl/chapters')->assertOk();
    get('/nl/about/news')->assertOk();
    get('/nl/help-out')->assertOk();
});
```

- [ ] **Step 2: Run it to verify it fails**

Run: `php artisan test --compact --filter=LocaleRoutingTest`
Expected: FAIL (routes `/nl`, `/nl/events`, etc. don't exist yet → 404).

- [ ] **Step 3: Create the SetLocale middleware**

Create `app/Http/Middleware/SetLocale.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /** @var list<string> */
    private const SUPPORTED = ['nl'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! in_array($locale, self::SUPPORTED, true)) {
            abort(404);
        }

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
```

- [ ] **Step 4: Register the middleware alias**

In `bootstrap/app.php`, replace the empty `withMiddleware` body:

```php
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'setlocale' => \App\Http\Middleware\SetLocale::class,
        ]);
    })
```

- [ ] **Step 5: Set the global URL locale default**

In `app/Providers/AppServiceProvider.php`, add `use Illuminate\Support\Facades\URL;` at the top, then add to `configureDefaults()`:

```php
        // Single locale for now — widen/derive when fr lands.
        URL::defaults(['locale' => 'nl']);
```

- [ ] **Step 6: Default the app locale to nl**

In `config/app.php`, change the two lines:

```php
    'locale' => env('APP_LOCALE', 'nl'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'nl'),
```

- [ ] **Step 7: Rewrite routes/web.php with the locale group + IA paths**

Replace `routes/web.php` with:

```php
<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpersonateController;
use Illuminate\Support\Facades\Route;

// Bare root → default locale.
Route::get('/', fn () => redirect('/nl'));

Route::prefix('{locale}')
    ->whereIn('locale', ['nl'])
    ->middleware('setlocale')
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');

        // Events (Activity model — "Events" is the public name for the rides calendar).
        Route::get('events', [ActivityController::class, 'index'])->name('activities.index');
        Route::get('events/{activity}', [ActivityController::class, 'show'])->name('activities.show');
        Route::get('events/{activity}/ical', [ActivityController::class, 'ical'])->name('activities.ical');

        // Chapters (Group model).
        Route::get('chapters', [GroupController::class, 'index'])->name('groups.index');
        Route::get('chapters/{group}', [GroupController::class, 'show'])->name('groups.show');

        // Help out.
        Route::view('help-out', 'volunteer')->name('volunteer');

        // Getting started.
        Route::view('getting-started', 'getting-started')->name('getting-started');

        // About section.
        Route::view('about', 'about.index')->name('about');
        Route::view('about/mission', 'about.mission')->name('about.mission');
        Route::view('about/vision', 'about.vision')->name('about.vision');
        Route::view('about/organisation', 'about.organisation')->name('about.organisation');
        Route::get('about/news', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('about/news/{article}', [ArticleController::class, 'show'])->name('articles.show');
        Route::view('about/press', 'about.press')->name('about.press');
        Route::view('about/partners', 'about.partners')->name('about.partners');

        // Membership.
        Route::view('membership', 'membership')->name('membership');

        // Contact (national).
        Route::view('contact', 'contact')->name('contact');

        // Legal / utilities.
        Route::view('privacy', 'privacy')->name('privacy');
        Route::view('cookies', 'cookies')->name('cookies');
    });

// Authenticated (unprefixed — deferred logged-in tier).
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->prefix('admin')->group(function (): void {
    Route::post('impersonate/{user}', [ImpersonateController::class, 'start'])
        ->name('admin.impersonate.start');
    Route::post('impersonate/stop', [ImpersonateController::class, 'stop'])
        ->name('admin.impersonate.stop');
});

require __DIR__.'/settings.php';
```

NOTE: the stub views (`getting-started`, `about.*`, `membership`, `contact`, `privacy`, `cookies`) are created in Task 2. The four routes asserted in this task's test (`events`, `chapters`, `about/news`, `help-out`) all use existing views, so this task's test passes now; the stub routes 404 until Task 2 but nothing references them yet.

- [ ] **Step 8: Update the existing hardcoded-path tests**

In `tests/Feature/PublicPagesTest.php`, change the four hardcoded paths (leave the `route(...)` calls untouched):

```php
// 'renders the home page with real data'
    get('/nl')
// 'renders the activities index with the event listed'
    get('/nl/events')
// 'renders the articles index with the article listed'
    get('/nl/about/news')
// 'renders the groups index with the group listed'
    get('/nl/chapters')
```

In `tests/Feature/PageStatusTest.php`, update the first test body:

```php
it('renders main pages with status 200', function () {
    $this->get('/')->assertRedirect('/nl');
    $this->get('/nl')->assertOk();
    $this->get('/nl/chapters')->assertOk();
    $this->get('/nl/about/news')->assertOk();
    $this->get('/nl/events')->assertOk();
    $this->get('/login')->assertOk();
    $this->get('/register')->assertOk();
});
```

- [ ] **Step 9: Run the full suite to verify green**

Run: `php artisan test --compact`
Expected: PASS. (`LocaleRoutingTest` green; `PublicPagesTest`, `PageStatusTest`, `GroupsTest`, `ActivityIcalTest`, `ActivityMapTest` still green because `route()` calls resolve via the global URL default.)

- [ ] **Step 10: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A
git commit -m "feat: add /nl locale routing + IA paths for existing public pages"
```

---

## Task 2: Stub pages + comprehensive public smoke test

**Files:**
- Create: `resources/views/components/stub.blade.php`, `resources/views/getting-started.blade.php`, `resources/views/membership.blade.php`, `resources/views/contact.blade.php`, `resources/views/privacy.blade.php`, `resources/views/cookies.blade.php`, `resources/views/about/index.blade.php`, `resources/views/about/mission.blade.php`, `resources/views/about/vision.blade.php`, `resources/views/about/organisation.blade.php`, `resources/views/about/press.blade.php`, `resources/views/about/partners.blade.php`
- Test: `tests/Feature/PublicStructureTest.php` (create)

- [ ] **Step 1: Write the failing structure smoke test**

Create `tests/Feature/PublicStructureTest.php`:

```php
<?php

use function Pest\Laravel\get;

it('serves every no-parameter public route with 200', function (string $path) {
    get($path)->assertOk();
})->with([
    '/nl',
    '/nl/events',
    '/nl/chapters',
    '/nl/help-out',
    '/nl/getting-started',
    '/nl/about',
    '/nl/about/mission',
    '/nl/about/vision',
    '/nl/about/organisation',
    '/nl/about/news',
    '/nl/about/press',
    '/nl/about/partners',
    '/nl/membership',
    '/nl/contact',
    '/nl/privacy',
    '/nl/cookies',
]);

it('marks stub pages as unfinished', function () {
    get('/nl/getting-started')->assertSee('Stub', escape: false);
});
```

- [ ] **Step 2: Run it to verify it fails**

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: FAIL — the stub paths 404 (views/routes missing).

- [ ] **Step 3: Create the reusable stub component**

Create `resources/views/components/stub.blade.php`:

```blade
@props(['title', 'sections' => []])

<x-layouts::site :title="$title">
    <div class="mx-auto max-w-3xl space-y-8">
        <header class="space-y-2">
            <h1>{{ $title }}</h1>
            <p class="inline-block rounded bg-yellow-100 px-3 py-1 text-sm font-bold text-kidical-ink">
                Stub — alleen structuur, nog geen definitieve inhoud.
            </p>
        </header>

        @foreach ($sections as $heading => $note)
            <section class="space-y-2">
                <h2>{{ $heading }}</h2>
                <p class="text-kidical-ink/50">[ placeholder: {{ $note }} ]</p>
            </section>
        @endforeach
    </div>
</x-layouts::site>
```

- [ ] **Step 4: Create the stub views**

Create `resources/views/getting-started.blade.php`:

```blade
<x-stub
    title="Voor het eerst mee"
    :sections="[
        'Wat te verwachten op een rit' => 'leeftijden ±4–12, geen loopfietsen, kind vergezeld door een volwassene, 5–7 km, traag tempo, max 1 uur, gratis zonder inschrijving, muziek onderweg',
        'Praktische FAQ' => 'leeftijd, uitrusting, weer, inschrijving',
        'Geen fiets?' => 'uitgesteld na de kern — providerlijst nog te bevestigen',
        'Andere fietsactiviteiten voor kinderen in België' => 'lijst',
    ]"
/>
```

Create `resources/views/about/index.blade.php`:

```blade
<x-stub
    title="Over ons"
    :sections="[
        'Missie' => 'link naar /about/mission',
        'Visie' => 'link naar /about/vision',
        'Organisatie' => 'link naar /about/organisation',
        'Nieuws' => 'link naar /about/news',
        'Pers' => 'link naar /about/press',
        'Partners' => 'link naar /about/partners',
    ]"
/>
```

Create `resources/views/about/mission.blade.php`:

```blade
<x-stub
    title="Missie"
    :sections="[
        'Wat Kidical Mass is' => 'inleiding',
        'De 3 assen' => 'Start (kinderen op de fiets) · Support (dagelijkse mobiliteit) · Spread (fietscultuur)',
        'Inclusiviteit' => 'iedereen welkom',
        'Impact in cijfers' => 'live statistieken (PAT-4)',
    ]"
/>
```

Create `resources/views/about/vision.blade.php`:

```blade
<x-stub
    title="Visie"
    :sections="[
        'Beleidseisen' => '4 eisen (fietspaden, fietsparking gezinnen, veilige schoolomgeving, 30 km/u)',
        'Manifest kindvriendelijke stad' => 'samengevoegd — herschrijven + NL-vertaling nodig (bron is FR-only, gedateerd 2024)',
    ]"
/>
```

Create `resources/views/about/organisation.blade.php`:

```blade
<x-stub
    title="Organisatie"
    :sections="[
        'Bestuur' => 'governance',
        'Coördinatieduo' => 'Leticia & Cecilia',
        'Lokale groepsstructuur' => 'lokaal → nationaal',
        'Organigram' => 'toegankelijk organigram',
    ]"
/>
```

Create `resources/views/about/press.blade.php`:

```blade
<x-stub
    title="Pers"
    :sections="[
        'Nationale berichtgeving' => 'logo\'s/citaten, taallabels',
        'Lokale berichtgeving' => 'ook op afdelingspaginas (dual-homed)',
        'Perscontact / mediakit' => 'contact + downloads',
    ]"
/>
```

Create `resources/views/about/partners.blade.php`:

```blade
<x-stub
    title="Partners"
    :sections="[
        'Nationale partners & subsidiënten' => 'Bruxelles Mobilité, Clean Cities, Bruxelles Ville, gemeente Schaarbeek',
        'Campagne-affiliatie' => '#StreetsForKids (Clean Cities)',
        'Spacefunders' => 'logo-weergave (PAT-5)',
    ]"
/>
```

Create `resources/views/membership.blade.php`:

```blade
<x-stub
    title="Lid worden / Spacefunding"
    :sections="[
        'Wat Spacefunding is' => 'het terugkerende steunmodel via Growfunding',
        'Word lid' => 'terugkerend; instaptier €3/mnd Kidi Buddy → t-shirt = lidmaatschap',
        'Alle tiers' => '6 tiers €3–500/mnd; €20+ logo/social',
        'Naar Growfunding' => 'de site linkt door, verwerkt geen betalingen',
    ]"
/>
```

Create `resources/views/contact.blade.php`:

```blade
<x-stub
    title="Contact"
    :sections="[
        'Nationaal contact' => 'naar het coördinatieduo — pers / partnerschap / algemeen',
        'Vrijwilliger worden?' => 'dat loopt via Meehelpen (per afdeling), niet hier',
    ]"
/>
```

Create `resources/views/privacy.blade.php`:

```blade
<x-stub
    title="Privacybeleid"
    :sections="[
        'GDPR' => 'placeholder — juridische tekst nog aan te leveren (contactformulier + e-mailinschrijving)',
    ]"
/>
```

Create `resources/views/cookies.blade.php`:

```blade
<x-stub
    title="Cookiebeleid"
    :sections="[
        'GDPR' => 'placeholder — cookietekst nog aan te leveren',
    ]"
/>
```

- [ ] **Step 5: Run the structure test to verify it passes**

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: PASS (all 16 paths 200; getting-started shows "Stub").

- [ ] **Step 6: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A
git commit -m "feat: add public IA stub pages (getting-started, about/*, membership, contact, legal)"
```

---

## Task 3: Navigation + footer chrome (NL via lang files)

**Files:**
- Create: `lang/nl/nav.php`, `lang/nl/common.php`
- Modify: `resources/views/layouts/site/header.blade.php`, `resources/views/layouts/site/footer.blade.php`
- Test: `tests/Feature/NavigationTest.php` (create)

- [ ] **Step 1: Write the failing navigation test**

Create `tests/Feature/NavigationTest.php`:

```php
<?php

use function Pest\Laravel\get;

it('shows the five-item dutch main nav on a public page', function () {
    get('/nl')
        ->assertSee('Kalender')
        ->assertSee('Afdelingen')
        ->assertSee('Voor het eerst')
        ->assertSee('Meehelpen')
        ->assertSee('Over ons');
});

it('links the footer to contact, membership and legal pages', function () {
    get('/nl')
        ->assertSee(route('contact'))
        ->assertSee(route('membership'))
        ->assertSee(route('privacy'))
        ->assertSee(route('cookies'));
});
```

- [ ] **Step 2: Run it to verify it fails**

Run: `php artisan test --compact --filter=NavigationTest`
Expected: FAIL — header still shows English "Groups/Articles/Activities"; footer lacks the new links.

- [ ] **Step 3: Create the nav language file**

Create `lang/nl/nav.php`:

```php
<?php

return [
    'events' => 'Kalender',
    'chapters' => 'Afdelingen',
    'getting_started' => 'Voor het eerst',
    'help_out' => 'Meehelpen',
    'about' => 'Over ons',
    'mission' => 'Missie',
    'vision' => 'Visie',
    'organisation' => 'Organisatie',
    'news' => 'Nieuws',
    'press' => 'Pers',
    'partners' => 'Partners',
    'login' => 'Inloggen',
];
```

- [ ] **Step 4: Create the common chrome language file**

Create `lang/nl/common.php`:

```php
<?php

return [
    'contact' => 'Contact',
    'membership' => 'Lid worden',
    'privacy' => 'Privacy',
    'cookies' => 'Cookies',
    'home' => 'Home',
];
```

- [ ] **Step 5: Rebuild the header nav**

Replace `resources/views/layouts/site/header.blade.php` lines 18–55 (the `<flux:navbar>` desktop block, the `@guest` user-menu block, and the mobile `<nav>` block) with the 5-item structure. Desktop nav:

```blade
            <!-- Main Navigation -->
            <flux:navbar class="hidden md:flex">
                <flux:navbar.item href="{{ route('activities.index') }}" :current="request()->routeIs('activities.*')" class="font-bold text-lg">{{ __('nav.events') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('groups.index') }}" :current="request()->routeIs('groups.*')" class="font-bold text-lg">{{ __('nav.chapters') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('getting-started') }}" :current="request()->routeIs('getting-started')" class="font-bold text-lg">{{ __('nav.getting_started') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('volunteer') }}" :current="request()->routeIs('volunteer')" class="font-bold text-lg">{{ __('nav.help_out') }}</flux:navbar.item>
                <flux:dropdown>
                    <flux:navbar.item icon:trailing="chevron-down" :current="request()->routeIs('about.*') || request()->routeIs('articles.*')" class="font-bold text-lg">{{ __('nav.about') }}</flux:navbar.item>
                    <flux:menu>
                        <flux:menu.item href="{{ route('about.mission') }}">{{ __('nav.mission') }}</flux:menu.item>
                        <flux:menu.item href="{{ route('about.vision') }}">{{ __('nav.vision') }}</flux:menu.item>
                        <flux:menu.item href="{{ route('about.organisation') }}">{{ __('nav.organisation') }}</flux:menu.item>
                        <flux:menu.item href="{{ route('articles.index') }}">{{ __('nav.news') }}</flux:menu.item>
                        <flux:menu.item href="{{ route('about.press') }}">{{ __('nav.press') }}</flux:menu.item>
                        <flux:menu.item href="{{ route('about.partners') }}">{{ __('nav.partners') }}</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </flux:navbar>
```

Replace the `@guest` user-menu block (desktop) so guests see only a login link (no public Register — invite-only):

```blade
            <!-- User Menu -->
            <div class="hidden md:flex items-center gap-2">
                @guest
                    <flux:button href="{{ route('login') }}" variant="ghost">{{ __('nav.login') }}</flux:button>
                @else
                    <flux:dropdown>
                        <flux:button variant="ghost">{{ Auth::user()->name }}</flux:button>
                        <flux:menu>
                            <flux:menu.item href="{{ route('profile.edit') }}">Profile</flux:menu.item>
                            <flux:menu.separator />
                            <flux:menu.item href="{{ route('logout') }}" method="POST">Logout</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                @endguest
            </div>
```

Replace the mobile `<nav>` block:

```blade
        <!-- Mobile Navigation -->
        <nav x-show="mobileOpen" x-transition class="md:hidden pb-4 space-y-1">
            <flux:navbar.item href="{{ route('activities.index') }}" :current="request()->routeIs('activities.*')">{{ __('nav.events') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('groups.index') }}" :current="request()->routeIs('groups.*')">{{ __('nav.chapters') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('getting-started') }}" :current="request()->routeIs('getting-started')">{{ __('nav.getting_started') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('volunteer') }}" :current="request()->routeIs('volunteer')">{{ __('nav.help_out') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('about.mission') }}" :current="request()->routeIs('about.*')">{{ __('nav.about') }}</flux:navbar.item>
            @guest
                <flux:navbar.item href="{{ route('login') }}">{{ __('nav.login') }}</flux:navbar.item>
            @endguest
        </nav>
```

- [ ] **Step 6: Add the footer cluster**

In `resources/views/layouts/site/footer.blade.php`, the existing list has a `route('home')` "Home" item. Add the new cluster items next to it (keep existing structure; add these `<li>` entries in the same `<ul>`):

```blade
                    <li><a href="{{ route('home') }}">{{ __('common.home') }}</a></li>
                    <li><a href="{{ route('contact') }}">{{ __('common.contact') }}</a></li>
                    <li><a href="{{ route('membership') }}">{{ __('common.membership') }}</a></li>
                    <li><a href="{{ route('privacy') }}">{{ __('common.privacy') }}</a></li>
                    <li><a href="{{ route('cookies') }}">{{ __('common.cookies') }}</a></li>
                    <li><a href="{{ route('login') }}">{{ __('nav.login') }}</a></li>
```

- [ ] **Step 7: Run the navigation test to verify it passes**

Run: `php artisan test --compact --filter=NavigationTest`
Expected: PASS (NL labels visible; footer links present).

- [ ] **Step 8: Run the full suite, Pint, and commit**

Run: `php artisan test --compact`
Expected: PASS (whole suite green).

```bash
vendor/bin/pint --dirty --format agent
git add -A
git commit -m "feat: rebuild public nav + footer to 5-item NL structure via lang/nl"
```

---

## Self-Review

**1. Spec coverage:**
- Locale architecture (`/{locale}`, `nl` only, middleware, root redirect) → Task 1. ✅
- Hybrid i18n (chrome in `lang/nl/`, bodies hardcoded NL) → Task 3 (chrome) + Task 2 (NL stub bodies). ✅
- Public-tier-only, logged-in deferred → no My-activities/backstage routes; only a login link in nav. ✅
- Route map reuse vs. stub (every row) → Task 1 (reuse: events/chapters/news/help-out/partners-page-as-stub) + Task 2 (stubs ×10). ✅ *(Note: `/about/partners` is a stub here rather than wrapping `<x-partners>`, since that component already renders globally in the layout; flagged below.)*
- Stub format (skeleton sections, obviously unfinished, no fake content) → Task 2 `components/stub.blade.php`. ✅
- Nav (5-item) + footer cluster → Task 3. ✅
- Testing (smoke all routes, `/`→`/nl` redirect, invalid-locale 404) → LocaleRoutingTest + PublicStructureTest. ✅
- Out of scope (models, FR/EN, Wix redirects, slug refinements) → untouched. ✅

**2. Placeholder scan:** No "TBD/TODO/handle edge cases" — every step has concrete code or an exact command. The in-page "[ placeholder: … ]" strings are intentional product content (visible stub markers), not plan placeholders.

**3. Type/name consistency:** Route names referenced in nav/tests (`activities.*`, `groups.*`, `articles.*`, `volunteer`, `getting-started`, `about.*`, `membership`, `contact`, `privacy`, `cookies`, `home`, `login`) all match the definitions in Task 1's `routes/web.php`. Middleware alias `setlocale` matches its use in the route group. `lang/nl/nav.php` + `common.php` keys match every `__()` call in Task 3.

**Deviations flagged for the user:**
- Route **names kept** (not renamed) — see the top banner.
- `/about/partners` rendered as a **stub** (the `<x-partners>` bar already appears site-wide via the layout); promote to a real page later.
- First-pass **NL nav labels** (Kalender / Afdelingen / Voor het eerst / Meehelpen / Over ons) live in `lang/nl/nav.php` — trivial to tweak.
