# Error Pages Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace Laravel's default error pages with five on-brand views (404/403/419 on the site layout, standalone 500/503) plus a non-production preview route.

**Architecture:** A shared `x-error-page` Blade component carries the illustration + code + headline + actions for the three site-layout errors; each error view is mostly copy. 500/503 are self-contained HTML documents with inline styles (no Vite, no DB, no lang files). A non-prod `/preview/errors/{code}` route renders any of the five directly. Spec: `docs/superpowers/specs/2026-07-04-error-pages-design.md`.

**Tech Stack:** Laravel 13 Blade views (`resources/views/errors/*` picked up by convention), existing components (`x-layouts::site`, `x-cta-button`, `x-nav-card`, `x-section-heading`), Pest 4 feature tests.

## Global Constraints

- Copy is hardcoded NL, follows `docs/tone-of-voice.md`, **no em-dashes** anywhere.
- Headings are raw `<h1>`/`<h2>`, never `flux:heading`; no font-size/weight utilities on headings (type scale lives in `@layer base`).
- No new CSS partial expected; appearance utilities live inside components and must reference tokens (`text-kidical-blue`), never raw hex. The standalone 500/503 pages are the one sanctioned exception: literal token values in an inline `<style>`.
- Decorative images get `alt=""` + `aria-hidden="true"`.
- Tests assert the `data-error-page` seam, status codes, and `href`s; never utility classes or full copy sentences.
- Run `vendor/bin/pint --dirty --format agent` before each commit that touches PHP.
- Shared checkout with Nico: stage by explicit path only, never `git add -A`, don't push.
- Route helpers: `URL::defaults(['locale' => config('app.locale')])` is set in `AppServiceProvider`, so `route('activities.index')` etc. work in error views without passing a locale.

---

### Task 1: `x-error-page` component + 404 view

**Files:**
- Create: `resources/views/components/error-page.blade.php`
- Create: `resources/views/errors/404.blade.php`
- Test: `tests/Feature/ErrorPagesTest.php`

**Interfaces:**
- Consumes: `x-layouts::site` (prop `title`), `x-cta-button` (props `href`, `variant`, `icon`), `x-nav-card` (props `href`, `icon`, `title`, slot = description), `x-section-heading` (slot).
- Produces: `<x-error-page code="…" title="…" illustration="img/illustrations/….svg">` with default slot (body copy) and optional `<x-slot:actions>`; emits `data-error-page="{code}"`. Tasks 2 uses this component; Task 4's preview route renders these views.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/ErrorPagesTest.php`:

```php
<?php

it('shows the branded 404 with routes back into the site', function () {
    $response = $this->get('/nl/deze-pagina-bestaat-niet');

    $response->assertNotFound();
    $response->assertSee('data-error-page="404"', false);
    $response->assertSee(route('activities.index'), false);
    $response->assertSee(route('groups.index'), false);
    $response->assertSee(route('getting-started'), false);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=ErrorPagesTest`
Expected: FAIL (the default Laravel 404 has no `data-error-page` attribute).

- [ ] **Step 3: Create the component**

`resources/views/components/error-page.blade.php`:

```blade
@props(['code', 'title', 'illustration'])

{{-- Shared shell for the error pages that keep the site layout (404/403/419):
     illustration, status code, headline, body copy (slot) and an optional
     actions slot. data-error-page is the stable test seam. The standalone
     500/503 views deliberately do NOT use this component: they must render
     without the app fully booting. --}}
<section data-error-page="{{ $code }}" class="mx-auto flex max-w-2xl flex-col items-center gap-4 pt-8 pb-4 text-center">
    <img src="{{ asset($illustration) }}" alt="" aria-hidden="true" class="h-44 w-auto sm:h-56">
    <p class="font-heading text-kidical-blue" aria-hidden="true">{{ $code }}</p>
    <h1>{{ $title }}</h1>
    <div>{{ $slot }}</div>
    @isset($actions)
        <div class="mt-2 flex flex-wrap items-center justify-center gap-4">{{ $actions }}</div>
    @endisset
</section>
```

- [ ] **Step 4: Create the 404 view**

`resources/views/errors/404.blade.php`:

```blade
{{--
    404 — pagina niet gevonden. Warmste en meest gebruikte errorpagina: vangt ook
    dode links van de oude Wix-site op, dus de nav-cards wijzen naar de plekken
    waar bezoekers meestal heen willen.
--}}
<x-layouts::site title="Pagina niet gevonden">

    <x-error-page code="404" title="Oeps, je bent verkeerd gereden" illustration="img/illustrations/heart-30-sign.svg">
        <p>Deze pagina bestaat niet meer, of heeft nooit bestaan. Geen zorgen: zo sta je weer op de route.</p>

        <x-slot:actions>
            <x-cta-button :href="route('home')" variant="secondary" icon="back">Naar de startpagina</x-cta-button>
        </x-slot:actions>
    </x-error-page>

    {{-- De nuttige helft: rechtstreeks naar de populairste bestemmingen. --}}
    <section class="mx-auto mt-16 max-w-4xl">
        <x-section-heading class="text-center">Waar wil je naartoe?</x-section-heading>
        <div class="mt-8 grid gap-6 sm:grid-cols-3">
            <x-nav-card :href="route('activities.index')" icon="calendar-days" title="Kalender">Vind een rit in jouw buurt.</x-nav-card>
            <x-nav-card :href="route('groups.index')" icon="map-pin" title="Lokale groepen">Ontdek wie er bij jou in de buurt fietst.</x-nav-card>
            <x-nav-card :href="route('getting-started')" icon="face-smile" title="Voor het eerst mee">Wat je mag verwachten op een rit.</x-nav-card>
        </div>
    </section>

</x-layouts::site>
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --compact --filter=ErrorPagesTest`
Expected: PASS (1 test).

- [ ] **Step 6: Commit**

```bash
git add resources/views/components/error-page.blade.php resources/views/errors/404.blade.php tests/Feature/ErrorPagesTest.php
git commit -m "feat(errors): branded 404 with x-error-page shell and nav-cards"
```

---

### Task 2: 403 and 419 views

**Files:**
- Create: `resources/views/errors/403.blade.php`
- Create: `resources/views/errors/419.blade.php`
- Modify: `tests/Feature/ErrorPagesTest.php` (append tests)

**Interfaces:**
- Consumes: `x-error-page` from Task 1; `route('login')` (Fortify); guarded route `groups.roze-hesjes` (guests get 403 via `abort_unless` in `RozeHesjeController`); `App\Models\Group` factory.
- Produces: nothing new for later tasks (Task 4 renders these views by name).

- [ ] **Step 1: Write the failing tests**

Append to `tests/Feature/ErrorPagesTest.php` (add `use App\Models\Group;` at the top):

```php
it('shows the branded 403 with a login action on member-only pages', function () {
    $group = Group::factory()->create();

    $response = $this->get(route('groups.roze-hesjes', $group));

    $response->assertForbidden();
    $response->assertSee('data-error-page="403"', false);
    $response->assertSee(route('login'), false);
});

it('renders the session-expired page with a retry action', function () {
    $html = view('errors.419')->render();

    expect($html)
        ->toContain('data-error-page="419"')
        ->toContain('history.back()');
});
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter=ErrorPagesTest`
Expected: the two new tests FAIL (no custom 403 markup; `errors.419` view not found).

- [ ] **Step 3: Create the 403 view**

`resources/views/errors/403.blade.php`:

```blade
{{-- 403 — hier komt vooral een begeleider terecht die de roze-hesjespagina
     opent zonder ingelogd te zijn. Ga uit van goede wil: nodig uit om in te
     loggen in plaats van te blokkeren. --}}
<x-layouts::site title="Alleen voor begeleiders">

    <x-error-page code="403" title="Deze pagina is voor begeleiders" illustration="img/illustrations/heart-sign-holder.svg">
        <p>Ben jij begeleider bij een lokale groep? Log dan even in, daarna kan je meteen verder.</p>

        <x-slot:actions>
            <x-cta-button :href="route('login')">Inloggen</x-cta-button>
            <x-cta-button :href="route('home')" variant="ghost" icon="back">Naar de startpagina</x-cta-button>
        </x-slot:actions>
    </x-error-page>

</x-layouts::site>
```

- [ ] **Step 4: Create the 419 view**

`resources/views/errors/419.blade.php`:

```blade
{{-- 419 — verlopen sessie, meestal een contactformulier dat te lang openstond.
     history.back() als inline onclick: de publieke site verscheept geen JS-framework,
     en x-cta-button zonder href rendert een <button> waar het attribuut op landt. --}}
<x-layouts::site title="Even opnieuw proberen">

    <x-error-page code="419" title="Je was er even tussenuit" illustration="img/illustrations/relaxed-rider.svg">
        <p>Deze pagina stond te lang open. Ga terug en probeer het opnieuw, meer is het niet.</p>

        <x-slot:actions>
            <x-cta-button onclick="history.back()" icon="back">Ga terug</x-cta-button>
            <x-cta-button :href="route('home')" variant="ghost">Naar de startpagina</x-cta-button>
        </x-slot:actions>
    </x-error-page>

</x-layouts::site>
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --compact --filter=ErrorPagesTest`
Expected: PASS (3 tests).

- [ ] **Step 6: Commit**

```bash
git add resources/views/errors/403.blade.php resources/views/errors/419.blade.php tests/Feature/ErrorPagesTest.php
git commit -m "feat(errors): warm 403 (login nudge) and 419 (retry) pages"
```

---

### Task 3: Standalone 500 and 503

**Files:**
- Create: `resources/views/errors/500.blade.php`
- Create: `resources/views/errors/503.blade.php`
- Modify: `tests/Feature/ErrorPagesTest.php` (append test)
- Modify: `docs/superpowers/specs/2026-07-04-error-pages-design.md` (illustration note, see Step 4)

**Interfaces:**
- Consumes: nothing from the app on purpose. Static asset `/img/illustrations/volunteer-with-wrench.svg`, bunny.net font CSS (same CDN link as `partials/site-head.blade.php`), literal token values from `resources/css/app.css` `@theme`: ink `#281a39`, body text = ink at 75% opacity.
- Produces: `errors.500` / `errors.503` views for Task 4's preview route.

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/ErrorPagesTest.php`:

```php
it('renders the standalone 500 and 503 pages without app asset dependencies', function (string $code) {
    $html = view('errors.'.$code)->render();

    expect($html)
        ->toContain('data-error-page="'.$code.'"')
        ->not->toContain('vite');
})->with(['500', '503']);
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=ErrorPagesTest`
Expected: the new cases FAIL (`View [errors.500] not found`).

- [ ] **Step 3: Create both views**

`resources/views/errors/500.blade.php`:

```blade
{{-- 500 — bewust standalone: geen site-layout, geen @vite, geen DB, geen lang-files.
     Als de app plat ligt moet deze pagina nog renderen. Letterlijke tokenwaarden
     gekopieerd uit @theme in resources/css/app.css (de enige gedoogde plek). --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Er ging iets mis | Kidical Mass</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito-sans:400,700%7Ccaprasimo:400&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; box-sizing: border-box; padding: 1.5rem; background: #fff; color: #281a39; font-family: 'Nunito Sans', ui-sans-serif, system-ui, sans-serif; text-align: center; }
        main { max-width: 34rem; }
        img { height: 11rem; width: auto; }
        h1 { margin: 1.5rem 0 0.75rem; font-family: 'Caprasimo', 'Nunito Sans', sans-serif; font-weight: 400; font-size: 2rem; }
        p { margin: 0; font-size: 1.125rem; line-height: 1.6; color: rgba(40, 26, 57, 0.75); }
    </style>
</head>
<body data-error-page="500">
    <main>
        <img src="/img/illustrations/volunteer-with-wrench.svg" alt="" aria-hidden="true">
        <h1>Er ging iets mis bij ons</h1>
        <p>Niet bij jou. We sleutelen eraan, probeer het straks gerust nog eens.</p>
    </main>
</body>
</html>
```

`resources/views/errors/503.blade.php` is the identical document with these three lines swapped:

```blade
    <title>Even aan het sleutelen | Kidical Mass</title>
```
```blade
<body data-error-page="503">
```
```blade
        <h1>We zijn even aan het sleutelen</h1>
        <p>De site is zo terug. Probeer het over een paar minuten opnieuw.</p>
```

(and the opening comment says `503 — onderhoudsmodus` instead). Two small duplicated files, zero coupling; the spec explicitly allows this.

- [ ] **Step 4: Align the spec with the static-img choice**

In `docs/superpowers/specs/2026-07-04-error-pages-design.md`, change the standalone-pair bullet from "inlined `volunteer-with-wrench.svg`" to a root-relative static `<img>`: the SVG is 30 KB (too heavy to inline twice) and static files are served by the web server even when PHP is down, so the dependency story is identical.

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --compact --filter=ErrorPagesTest`
Expected: PASS (5 tests).

- [ ] **Step 6: Commit**

```bash
git add resources/views/errors/500.blade.php resources/views/errors/503.blade.php tests/Feature/ErrorPagesTest.php docs/superpowers/specs/2026-07-04-error-pages-design.md
git commit -m "feat(errors): standalone dependency-free 500 and 503 pages"
```

---

### Task 4: Non-production preview route

**Files:**
- Modify: `routes/web.php` (inside the existing `if (! app()->isProduction())` block at the bottom, next to `/build` and `/styleguide`)
- Modify: `tests/Feature/ErrorPagesTest.php` (append test)

**Interfaces:**
- Consumes: the five `errors.*` views from Tasks 1-3.
- Produces: `GET /preview/errors/{code}` (`preview.errors`), non-prod only, unlinked. Responds with the real status code so the preview is faithful.

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/ErrorPagesTest.php`:

```php
it('previews every error page on the non-production preview route', function (string $code) {
    $response = $this->get('/preview/errors/'.$code);

    $response->assertStatus((int) $code);
    $response->assertSee('data-error-page="'.$code.'"', false);
})->with(['404', '403', '419', '500', '503']);

it('rejects unknown codes on the preview route', function () {
    $this->get('/preview/errors/418')->assertNotFound();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=ErrorPagesTest`
Expected: the preview cases FAIL (404 for every code, so `403` etc. mismatch).

- [ ] **Step 3: Add the route**

In `routes/web.php`, inside the non-production block (after the `design.choices` route):

```php
    // Error-page previews — 500/503 can't be reached by URL otherwise.
    Route::get('preview/errors/{code}', function (string $code) {
        abort_unless(in_array($code, ['404', '403', '419', '500', '503'], true), 404);

        return response()->view('errors.'.$code, [], (int) $code);
    })->name('preview.errors');
```

- [ ] **Step 4: Run the full error test file, then the suite-adjacent checks**

Run: `php artisan test --compact --filter=ErrorPagesTest`
Expected: PASS (11 tests).

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean (fixes applied if any).

- [ ] **Step 5: Commit**

```bash
git add routes/web.php tests/Feature/ErrorPagesTest.php
git commit -m "feat(errors): non-prod /preview/errors/{code} route"
```

---

### Task 5: Visual verification + log entry

- [ ] **Step 1: Build assets and screenshot the five pages**

Run `npm run build` if Tailwind hasn't picked up the new component classes, then screenshot `https://kidicalmass.test/preview/errors/{404,403,419,500,503}` in ONE Playwright pass (single script, five screenshots; see global CLAUDE.md screenshot rules). Verify: illustration renders, heading hierarchy, nav-cards on 404, buttons on 403/419, fonts on 500/503.

- [ ] **Step 2: Append a log entry**

Append to `docs/wiki/log.md`:

```markdown
## [2026-07-04] build | error pages

Branded errorpagina's live: 404/403/419 op de site-layout via gedeelde x-error-page
(heart-30-sign als 404-scène, nav-cards naar kalender/groepen/voor-het-eerst-mee),
standalone 500/503 zonder app-afhankelijkheden. Preview via /preview/errors/{code}
(non-prod). Spec: docs/superpowers/specs/2026-07-04-error-pages-design.md.
```

- [ ] **Step 3: Commit**

```bash
git add docs/wiki/log.md
git commit -m "docs(wiki): log error-pages build"
```

Note: error pages are not a `P-nn` registry page; no pipeline update needed.
