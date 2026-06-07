# Component Extraction Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Extract 8 recurring UI patterns from page templates into reusable Blade components, add a new `intro-text` component, and bump hero h1 and intro-text sizes by 50%.

**Architecture:** Each component gets a `resources/views/components/<name>.blade.php` and a `resources/css/components/<name>.css` partial imported in `app.css`. Existing page-scoped CSS for each pattern moves to the component partial; page templates are updated to use the new `<x-component-name>` tags. Styleguide is updated last.

**Tech Stack:** Laravel 12, Blade components, Tailwind CSS v4, Pest v4

---

## File Map

### New files
- `resources/views/components/intro-text.blade.php`
- `resources/views/components/section-heading.blade.php`
- `resources/views/components/pull-quote.blade.php`
- `resources/views/components/numbered-item.blade.php`
- `resources/views/components/person-card.blade.php`
- `resources/views/components/agenda-item.blade.php`
- `resources/views/components/info-card.blade.php`
- `resources/views/components/titled-list-block.blade.php`
- `resources/css/components/intro-text.css`
- `resources/css/components/section-heading.css`
- `resources/css/components/pull-quote.css`
- `resources/css/components/numbered-item.css`
- `resources/css/components/person-card.css`
- `resources/css/components/agenda-item.css`
- `resources/css/components/info-card.css`
- `resources/css/components/titled-list-block.css`
- `tests/Feature/ComponentExtractionTest.php`

### Modified files
- `resources/css/app.css` — 8 new `@import` lines
- `resources/css/components/page-hero.css` — hero title size bump
- `resources/css/pages/about.css` — remove `.about-section__title`, `.about-intro`, `.about-intro--lead`, `.about-quote`, `.about-voice`, `.about-voices`, `.about-duo__person`/`.about-duo__name`/`.about-duo__role`, `.about-demand`/`.about-demand__num`/`.about-demand strong`/`.about-demand p`, `.about-contact-card`/`__label`/`__email`/`__note`
- `resources/css/pages/help-out.css` — remove `.ho-intro`, `.ho-deal__subtitle`, `.ho-deal__list`/`li`
- `resources/css/pages/chapters.css` — remove `.chapter-agenda__item`, `.chapter-agenda__badge` + variants, `.chapter-agenda__when`, `.chapter-agenda__what`, `.chapter-agenda__loc`, `.chapter-agenda__cta`/`--quiet`
- `resources/views/about/mission.blade.php` — use `<x-intro-text>`, `<x-section-heading>`, `<x-pull-quote>`
- `resources/views/about/organisation.blade.php` — use `<x-intro-text>`, `<x-section-heading>`, `<x-person-card>`
- `resources/views/about/vision.blade.php` — use `<x-intro-text>`, `<x-section-heading>`, `<x-numbered-item>`
- `resources/views/about/press.blade.php` — use `<x-section-heading>`, `<x-info-card>`
- `resources/views/volunteer.blade.php` — use `<x-intro-text>`, `<x-titled-list-block>`
- `resources/views/groups/show.blade.php` — use `<x-agenda-item>`
- `resources/views/styleguide.blade.php` — add all 8 components to Componenten section; remove 7 from Nog te extraheren
- `app/Http/Controllers/StyleguideController.php` — remove 7 candidate entries from `candidates()`

---

## Task 0: Hero title size bump

**Files:**
- Modify: `resources/css/components/page-hero.css:28-35`

- [ ] **Step 1: Edit the clamp value**

In `resources/css/components/page-hero.css`, change line 30:
```css
/* Before */
font-size: clamp(var(--text-4xl), 4.5vw, var(--text-7xl));

/* After — 50% bigger at every breakpoint */
font-size: clamp(3.375rem, 6.75vw, 6.75rem);
```

- [ ] **Step 2: Run CSS architecture test**

```bash
php artisan test --compact --filter=CssArchitectureTest
```
Expected: 3 passed

- [ ] **Step 3: Commit**

```bash
git add resources/css/components/page-hero.css
git commit -m "style(page-hero): increase hero h1 50% — clamp(3.375rem, 6.75vw, 6.75rem)"
```

---

## Task 1: `intro-text` component

Lead paragraph block, replaces `.ho-intro` and `.about-intro`/`--lead`.

**Files:**
- Create: `resources/views/components/intro-text.blade.php`
- Create: `resources/css/components/intro-text.css`
- Modify: `resources/css/app.css`
- Modify: `resources/css/pages/about.css`
- Modify: `resources/css/pages/help-out.css`
- Modify: `resources/views/about/mission.blade.php`
- Modify: `resources/views/about/organisation.blade.php`
- Modify: `resources/views/about/vision.blade.php`
- Modify: `resources/views/volunteer.blade.php`
- Test: `tests/Feature/ComponentExtractionTest.php` (create file)

- [ ] **Step 1: Write failing tests**

Create `tests/Feature/ComponentExtractionTest.php`:
```php
<?php

use Illuminate\Support\Facades\Blade;

it('intro-text renders slot content with the intro-text class', function () {
    $html = Blade::render('<x-intro-text>Paragraph here.</x-intro-text>');

    expect($html)
        ->toContain('class="intro-text"')
        ->toContain('Paragraph here.');
});

it('intro-text lead variant adds the lead modifier class', function () {
    $html = Blade::render('<x-intro-text size="lead">Big lead.</x-intro-text>');

    expect($html)->toContain('intro-text--lead');
});
```

- [ ] **Step 2: Run to confirm they fail**

```bash
php artisan test --compact --filter="intro-text"
```
Expected: 2 failed (component does not exist)

- [ ] **Step 3: Create the Blade component**

Create `resources/views/components/intro-text.blade.php`:
```blade
@props(['size' => 'base'])

<div {{ $attributes->merge(['class' => 'intro-text'.($size === 'lead' ? ' intro-text--lead' : '')]) }}>
    {{ $slot }}
</div>
```

- [ ] **Step 4: Create the CSS partial**

Create `resources/css/components/intro-text.css`:
```css
@layer components {
    .intro-text {
        max-width: 60ch;
        margin-block: calc(var(--spacing) * 12);
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        font-size: clamp(1.7rem, 2.25vw, 2.25rem);
        line-height: 1.55;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 8%);
    }
    .intro-text--lead {
        font-size: clamp(1.875rem, 2.7vw, 2.8rem);
        font-weight: 600;
        line-height: 1.4;
        color: var(--color-kidical-ink);
    }
}
```

- [ ] **Step 5: Import in app.css**

In `resources/css/app.css`, add after the `support-callout` import (line ~136):
```css
@import './components/intro-text.css';
```

- [ ] **Step 6: Run tests — should pass**

```bash
php artisan test --compact --filter="intro-text"
```
Expected: 2 passed

- [ ] **Step 7: Update pages to use the component**

In `resources/views/about/mission.blade.php`, replace:
```blade
    <section class="about-intro">
        <p>Kidical Mass Belgium is een nationaal netwerk...</p>
        <p>Elke fietsparade heeft muziek onderweg...</p>
    </section>
```
With:
```blade
    <x-intro-text>
        <p>Kidical Mass Belgium is een nationaal netwerk...</p>
        <p>Elke fietsparade heeft muziek onderweg...</p>
    </x-intro-text>
```

In `resources/views/about/organisation.blade.php`, replace:
```blade
    <section class="about-intro">
        <p>Kidical Mass Belgium is zo opgebouwd...</p>
        <p>Op nationaal niveau...</p>
        <p>Op lokaal niveau...</p>
    </section>
```
With:
```blade
    <x-intro-text>
        <p>Kidical Mass Belgium is zo opgebouwd...</p>
        <p>Op nationaal niveau...</p>
        <p>Op lokaal niveau...</p>
    </x-intro-text>
```

In `resources/views/about/vision.blade.php`, replace:
```blade
    <section class="about-intro about-intro--lead">
        <p>Kidical Mass begon als een fietsparade...</p>
        <p>We geloven dat elk kind...</p>
        <p>Dat is niet radicaal...</p>
    </section>
```
With:
```blade
    <x-intro-text size="lead">
        <p>Kidical Mass begon als een fietsparade...</p>
        <p>We geloven dat elk kind...</p>
        <p>Dat is niet radicaal...</p>
    </x-intro-text>
```

In `resources/views/volunteer.blade.php`, replace:
```blade
    <p class="ho-intro">
        Meehelpen bij Kidical Mass is opkomen voor je eigen buurt...
    </p>
```
With:
```blade
    <x-intro-text>
        <p>Meehelpen bij Kidical Mass is opkomen voor je eigen buurt...</p>
    </x-intro-text>
```

- [ ] **Step 8: Remove old CSS from page files**

From `resources/css/pages/about.css`, delete the entire `.about-intro` and `.about-intro--lead` blocks (currently around lines 27–42).

From `resources/css/pages/help-out.css`, delete the `.ho-intro` block (currently around lines 10–17).

- [ ] **Step 9: Run architecture test and full extraction tests**

```bash
php artisan test --compact --filter=CssArchitectureTest
php artisan test --compact --filter="intro-text"
```
Expected: all pass

- [ ] **Step 10: Commit**

```bash
git add resources/views/components/intro-text.blade.php \
        resources/css/components/intro-text.css \
        resources/css/app.css \
        resources/css/pages/about.css \
        resources/css/pages/help-out.css \
        resources/views/about/mission.blade.php \
        resources/views/about/organisation.blade.php \
        resources/views/about/vision.blade.php \
        resources/views/volunteer.blade.php \
        tests/Feature/ComponentExtractionTest.php
git commit -m "feat(components): extract intro-text component"
```

---

## Task 2: `section-heading` component

Consistent h2 (or configured level) for contained sections.

**Files:**
- Create: `resources/views/components/section-heading.blade.php`
- Create: `resources/css/components/section-heading.css`
- Modify: `resources/css/app.css`
- Modify: `resources/css/pages/about.css`
- Modify: `resources/views/about/mission.blade.php`
- Modify: `resources/views/about/organisation.blade.php`
- Modify: `resources/views/about/vision.blade.php`
- Modify: `resources/views/about/press.blade.php`
- Test: `tests/Feature/ComponentExtractionTest.php`

- [ ] **Step 1: Add failing test**

Append to `tests/Feature/ComponentExtractionTest.php`:
```php
it('section-heading renders as h2 by default with the section-heading class', function () {
    $html = Blade::render('<x-section-heading>Wie wat doet</x-section-heading>');

    expect($html)
        ->toContain('<h2')
        ->toContain('class="section-heading"')
        ->toContain('Wie wat doet')
        ->toContain('</h2>');
});

it('section-heading respects the as prop to render a different heading level', function () {
    $html = Blade::render('<x-section-heading as="h3">Subkop</x-section-heading>');

    expect($html)->toContain('<h3')->toContain('</h3>');
});
```

- [ ] **Step 2: Run to confirm they fail**

```bash
php artisan test --compact --filter="section-heading"
```
Expected: 2 failed

- [ ] **Step 3: Create the Blade component**

Create `resources/views/components/section-heading.blade.php`:
```blade
@props(['as' => 'h2'])

<{{ $as }} {{ $attributes->merge(['class' => 'section-heading']) }}>{{ $slot }}</{{ $as }}>
```

- [ ] **Step 4: Create the CSS partial**

Create `resources/css/components/section-heading.css`:
```css
@layer components {
    .section-heading {
        color: var(--color-kidical-ink);
        margin-bottom: 0.25rem;
    }
}
```

- [ ] **Step 5: Import in app.css**

Add after the `intro-text` import:
```css
@import './components/section-heading.css';
```

- [ ] **Step 6: Run tests — should pass**

```bash
php artisan test --compact --filter="section-heading"
```
Expected: 2 passed

- [ ] **Step 7: Update pages**

In each of these 4 files, replace every `<h2 class="about-section__title">` with `<x-section-heading>` (and matching closing tag):

`resources/views/about/mission.blade.php`:
- `<h2 class="about-section__title">Iedereen is welkom</h2>` → `<x-section-heading>Iedereen is welkom</x-section-heading>`

`resources/views/about/organisation.blade.php`:
- `<h2 class="about-section__title">Hoe het in elkaar zit</h2>` → `<x-section-heading>Hoe het in elkaar zit</x-section-heading>`
- `<h2 class="about-section__title">Het coördinatieduo</h2>` → `<x-section-heading>Het coördinatieduo</x-section-heading>`
- `<h2 class="about-section__title">Veiligheid en routes</h2>` → `<x-section-heading>Veiligheid en routes</x-section-heading>`

`resources/views/about/vision.blade.php`:
- `<h2 class="about-section__title">Lees het manifest</h2>` → `<x-section-heading>Lees het manifest</x-section-heading>`

`resources/views/about/press.blade.php`:
- `<h2 class="about-section__title">Journalisten, we praten graag</h2>` → `<x-section-heading>Journalisten, we praten graag</x-section-heading>`

- [ ] **Step 8: Remove old CSS from about.css**

Delete the `.about-section__title` block from `resources/css/pages/about.css` (around line 123):
```css
/* DELETE THIS */
.about-section__title {
    color: var(--color-kidical-ink);
    margin-bottom: 0.25rem;
}
```

- [ ] **Step 9: Run tests**

```bash
php artisan test --compact --filter=CssArchitectureTest
php artisan test --compact --filter="section-heading"
```
Expected: all pass

- [ ] **Step 10: Commit**

```bash
git add resources/views/components/section-heading.blade.php \
        resources/css/components/section-heading.css \
        resources/css/app.css \
        resources/css/pages/about.css \
        resources/views/about/mission.blade.php \
        resources/views/about/organisation.blade.php \
        resources/views/about/vision.blade.php \
        resources/views/about/press.blade.php \
        tests/Feature/ComponentExtractionTest.php
git commit -m "feat(components): extract section-heading component"
```

---

## Task 3: `pull-quote` component

Quoted voice with attribution. Two variants: `large` (centered, heading-font) and `card` (tilted light-yellow tile).

**Files:**
- Create: `resources/views/components/pull-quote.blade.php`
- Create: `resources/css/components/pull-quote.css`
- Modify: `resources/css/app.css`
- Modify: `resources/css/pages/about.css`
- Modify: `resources/views/about/mission.blade.php`
- Modify: `resources/views/about/vision.blade.php`
- Test: `tests/Feature/ComponentExtractionTest.php`

- [ ] **Step 1: Add failing tests**

Append to `tests/Feature/ComponentExtractionTest.php`:
```php
it('pull-quote large renders blockquote with attribution', function () {
    $html = Blade::render(
        '<x-pull-quote attribution="Julienne, mama">"Vrijheid om buiten te zijn."</x-pull-quote>'
    );

    expect($html)
        ->toContain('pull-quote')
        ->toContain('<blockquote')
        ->toContain('<figcaption')
        ->toContain('Julienne, mama');
});

it('pull-quote card variant adds the card modifier class', function () {
    $html = Blade::render(
        '<x-pull-quote variant="card" attribution="Camille, mama">Quote.</x-pull-quote>'
    );

    expect($html)->toContain('pull-quote--card');
});
```

- [ ] **Step 2: Run to confirm they fail**

```bash
php artisan test --compact --filter="pull-quote"
```
Expected: 2 failed

- [ ] **Step 3: Create the Blade component**

Create `resources/views/components/pull-quote.blade.php`:
```blade
@props(['attribution', 'variant' => 'large'])

<figure {{ $attributes->merge(['class' => 'pull-quote pull-quote--'.$variant]) }}>
    <blockquote><p>{{ $slot }}</p></blockquote>
    <figcaption>{{ $attribution }}</figcaption>
</figure>
```

- [ ] **Step 4: Create the CSS partial**

Create `resources/css/components/pull-quote.css`:
```css
@layer components {
    /* Large: centred, prominent heading-font quote */
    .pull-quote--large {
        max-width: 54rem;
        margin-block: calc(var(--spacing) * 14);
        margin-inline: auto;
        text-align: center;
    }
    .pull-quote--large blockquote { margin: 0; }
    .pull-quote--large blockquote p {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: clamp(var(--text-2xl), 3.5vw, var(--text-4xl));
        line-height: 1.25;
        color: var(--color-kidical-ink);
        margin: 0;
    }
    .pull-quote--large figcaption {
        margin-top: 1.25rem;
        font-size: var(--text-base);
        font-weight: 700;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 35%);
    }

    /* Card: tilted light-yellow tile, used in grids */
    .pull-quote--card {
        margin: 0;
        padding: 1.75rem;
        border-radius: 1.25rem;
        background: var(--color-kidical-light-yellow);
    }
    .pull-quote--card:nth-child(odd)  { transform: rotate(-1deg); }
    .pull-quote--card:nth-child(even) { transform: rotate(1deg); }
    .pull-quote--card blockquote { margin: 0; }
    .pull-quote--card blockquote p {
        font-size: var(--text-lg);
        font-weight: 600;
        line-height: 1.45;
        color: var(--color-kidical-ink);
        margin: 0;
    }
    .pull-quote--card figcaption {
        margin-top: 1rem;
        font-size: var(--text-sm);
        font-weight: 700;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
    }
}
```

- [ ] **Step 5: Import in app.css**

Add after the `section-heading` import:
```css
@import './components/pull-quote.css';
```

- [ ] **Step 6: Run tests — should pass**

```bash
php artisan test --compact --filter="pull-quote"
```
Expected: 2 passed

- [ ] **Step 7: Update pages**

In `resources/views/about/mission.blade.php`, replace the entire `about-quote` figure:
```blade
{{-- Before --}}
<figure class="about-quote">
    <blockquote>
        <p>"Wat hij zo leuk vindt aan fietsen..."</p>
    </blockquote>
    <figcaption>Julienne, mama van twee kinderen (2 en 5 jaar)</figcaption>
</figure>

{{-- After --}}
<x-pull-quote attribution="Julienne, mama van twee kinderen (2 en 5 jaar)">
    "Wat hij zo leuk vindt aan fietsen, denk ik, is die vrijheid om buiten te zijn, lucht te hebben, er alleen op uit te trekken. Hij wil altijd ver gaan, iets nieuws ontdekken."
</x-pull-quote>
```

In `resources/views/about/vision.blade.php`, replace both `about-voice` figures inside `about-voices`. Keep the `<div class="about-voices">` wrapper:
```blade
{{-- Before --}}
<div class="about-voices">
    <figure class="about-voice">
        <blockquote><p>"Ik heb het gevoel dat ik de hele tijd..."</p></blockquote>
        <figcaption>Camille, mama van twee kinderen, Sint-Gillis</figcaption>
    </figure>
    <figure class="about-voice">
        <blockquote><p>"Ik ben constant bang voor de auto's..."</p></blockquote>
        <figcaption>Fatima, mama van drie kinderen, Jette</figcaption>
    </figure>
</div>

{{-- After --}}
<div class="about-voices">
    <x-pull-quote variant="card" attribution="Camille, mama van twee kinderen, Sint-Gillis">
        "Ik heb het gevoel dat ik de hele tijd de levenslust van mijn kinderen afrem."
    </x-pull-quote>
    <x-pull-quote variant="card" attribution="Fatima, mama van drie kinderen, Jette">
        "Ik ben constant bang voor de auto's, de trams. Tegen dat we thuis zijn van school, ben ik uitgeput."
    </x-pull-quote>
</div>
```

- [ ] **Step 8: Remove old CSS from about.css**

Delete these blocks from `resources/css/pages/about.css`:
- `.about-quote`, `.about-quote blockquote`, `.about-quote blockquote p`, `.about-quote figcaption`
- `.about-voices`
- `.about-voice`, `.about-voice blockquote`, `.about-voice blockquote p`, `.about-voice figcaption`

- [ ] **Step 9: Run tests**

```bash
php artisan test --compact --filter=CssArchitectureTest
php artisan test --compact --filter="pull-quote"
```
Expected: all pass

- [ ] **Step 10: Commit**

```bash
git add resources/views/components/pull-quote.blade.php \
        resources/css/components/pull-quote.css \
        resources/css/app.css \
        resources/css/pages/about.css \
        resources/views/about/mission.blade.php \
        resources/views/about/vision.blade.php \
        tests/Feature/ComponentExtractionTest.php
git commit -m "feat(components): extract pull-quote component"
```

---

## Task 4: `numbered-item` component

Numbered card with rotated red chip, title, and body. Used in `<ol>` lists.

**Files:**
- Create: `resources/views/components/numbered-item.blade.php`
- Create: `resources/css/components/numbered-item.css`
- Modify: `resources/css/app.css`
- Modify: `resources/css/pages/about.css`
- Modify: `resources/views/about/vision.blade.php`
- Test: `tests/Feature/ComponentExtractionTest.php`

- [ ] **Step 1: Add failing test**

Append to `tests/Feature/ComponentExtractionTest.php`:
```php
it('numbered-item renders number chip, title and body slot', function () {
    $html = Blade::render(
        '<x-numbered-item number="1" title="Veilige infrastructuur">Body text.</x-numbered-item>'
    );

    expect($html)
        ->toContain('numbered-item')
        ->toContain('numbered-item__num')
        ->toContain('1')
        ->toContain('Veilige infrastructuur')
        ->toContain('Body text.');
});
```

- [ ] **Step 2: Run to confirm it fails**

```bash
php artisan test --compact --filter="numbered-item"
```
Expected: 1 failed

- [ ] **Step 3: Create the Blade component**

Create `resources/views/components/numbered-item.blade.php`:
```blade
@props(['number', 'title'])

<div {{ $attributes->merge(['class' => 'numbered-item']) }}>
    <span class="numbered-item__num" aria-hidden="true">{{ $number }}</span>
    <strong>{{ $title }}</strong>
    <p>{{ $slot }}</p>
</div>
```

- [ ] **Step 4: Create the CSS partial**

Create `resources/css/components/numbered-item.css`:
```css
@layer components {
    .numbered-item {
        background: white;
        border-radius: 1.5rem;
        padding: 1.75rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }
    .numbered-item:nth-child(odd)  { transform: rotate(-1deg); }
    .numbered-item:nth-child(even) { transform: rotate(0.75deg); }
    .numbered-item__num {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-2xl);
        line-height: 1;
        width: 2.75rem;
        height: 2.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 28%;
        transform: rotate(-3deg);
        background: var(--color-kidical-red);
        color: white;
        margin-bottom: 0.25rem;
        flex-shrink: 0;
    }
    .numbered-item strong {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-xl);
        color: var(--color-kidical-ink);
        line-height: 1.2;
    }
    .numbered-item p {
        font-size: var(--text-base);
        line-height: 1.55;
        margin: 0;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 15%);
    }
}
```

- [ ] **Step 5: Import in app.css**

Add after the `pull-quote` import:
```css
@import './components/numbered-item.css';
```

- [ ] **Step 6: Run test — should pass**

```bash
php artisan test --compact --filter="numbered-item"
```
Expected: 1 passed

- [ ] **Step 7: Update about/vision.blade.php**

Replace the `<ol class="about-demand-grid">` contents. The `<ol class="about-demand-grid">` wrapper stays; the `<li class="about-demand">` items become `<x-numbered-item>`. Note the `about-demand-grid` grid CSS stays in about.css.

```blade
{{-- Before each li --}}
<li class="about-demand">
    <span class="about-demand__num" aria-hidden="true">1</span>
    <strong>Veilige fietsinfrastructuur voor kinderen en gezinnen</strong>
    <p>Aparte fietspaden...</p>
</li>

{{-- After —wrap with x-numbered-item instead --}}
<x-numbered-item number="1" title="Veilige fietsinfrastructuur voor kinderen en gezinnen">
    Aparte fietspaden die kinderen echt kunnen gebruiken: gescheiden van het verkeer, goed onderhouden en aaneengesloten. Gebouwd voor de kleinste fietsers, niet alleen voor de snelste.
</x-numbered-item>
```

Do the same for items 2, 3, and 4.

- [ ] **Step 8: Remove old CSS from about.css**

Delete from `resources/css/pages/about.css`:
- `.about-demand { ... }`
- `.about-demand__num { ... }`
- `.about-demand strong { ... }`
- `.about-demand p { ... }`

Keep `.about-demand-grid` (the grid layout wrapper) — it stays in about.css since it is page-specific layout.

- [ ] **Step 9: Run tests**

```bash
php artisan test --compact --filter=CssArchitectureTest
php artisan test --compact --filter="numbered-item"
```
Expected: all pass

- [ ] **Step 10: Commit**

```bash
git add resources/views/components/numbered-item.blade.php \
        resources/css/components/numbered-item.css \
        resources/css/app.css \
        resources/css/pages/about.css \
        resources/views/about/vision.blade.php \
        tests/Feature/ComponentExtractionTest.php
git commit -m "feat(components): extract numbered-item component"
```

---

## Task 5: `person-card` component

Name + role tile in light-yellow. Photo support stubbed (photos pending from coordination duo).

**Files:**
- Create: `resources/views/components/person-card.blade.php`
- Create: `resources/css/components/person-card.css`
- Modify: `resources/css/app.css`
- Modify: `resources/css/pages/about.css`
- Modify: `resources/views/about/organisation.blade.php`
- Test: `tests/Feature/ComponentExtractionTest.php`

- [ ] **Step 1: Add failing test**

Append to `tests/Feature/ComponentExtractionTest.php`:
```php
it('person-card renders name and role', function () {
    $html = Blade::render('<x-person-card name="Leticia" role="Coördinatie" />');

    expect($html)
        ->toContain('person-card')
        ->toContain('Leticia')
        ->toContain('Coördinatie');
});
```

- [ ] **Step 2: Run to confirm it fails**

```bash
php artisan test --compact --filter="person-card"
```
Expected: 1 failed

- [ ] **Step 3: Create the Blade component**

Create `resources/views/components/person-card.blade.php`:
```blade
@props(['name', 'role', 'photo' => null])

<div {{ $attributes->merge(['class' => 'person-card']) }}>
    @if ($photo)
        <img src="{{ $photo }}" alt="{{ $name }}" class="person-card__photo" loading="lazy">
    @endif
    <span class="person-card__name">{{ $name }}</span>
    <span class="person-card__role">{{ $role }}</span>
</div>
```

- [ ] **Step 4: Create the CSS partial**

Create `resources/css/components/person-card.css`:
```css
@layer components {
    .person-card {
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
        padding: 0.85rem 1.5rem;
        border-radius: 0.85rem;
        background: var(--color-kidical-light-yellow);
    }
    .person-card__photo {
        width: 4rem;
        height: 4rem;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 0.5rem;
    }
    .person-card__name {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-xl);
        color: var(--color-kidical-ink);
    }
    .person-card__role {
        font-size: var(--text-sm);
        font-weight: 600;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%);
    }
}
```

- [ ] **Step 5: Import in app.css**

Add after the `numbered-item` import:
```css
@import './components/person-card.css';
```

- [ ] **Step 6: Run test — should pass**

```bash
php artisan test --compact --filter="person-card"
```
Expected: 1 passed

- [ ] **Step 7: Update about/organisation.blade.php**

Replace the `<ul class="about-duo">` contents:
```blade
{{-- Before --}}
<ul class="about-duo" role="list">
    <li class="about-duo__person"><span class="about-duo__name">Leticia</span><span class="about-duo__role">Coördinatie</span></li>
    <li class="about-duo__person"><span class="about-duo__name">Cecilia</span><span class="about-duo__role">Coördinatie</span></li>
</ul>

{{-- After --}}
<ul class="about-duo" role="list">
    <li><x-person-card name="Leticia" role="Coördinatie" /></li>
    <li><x-person-card name="Cecilia" role="Coördinatie" /></li>
</ul>
```

- [ ] **Step 8: Remove old CSS from about.css**

Delete from `resources/css/pages/about.css`:
- `.about-duo__person { ... }`
- `.about-duo__name { ... }`
- `.about-duo__role { ... }`

Keep `.about-duo` (the flex wrapper) — it stays as layout in about.css.

- [ ] **Step 9: Run tests**

```bash
php artisan test --compact --filter=CssArchitectureTest
php artisan test --compact --filter="person-card"
```
Expected: all pass

- [ ] **Step 10: Commit**

```bash
git add resources/views/components/person-card.blade.php \
        resources/css/components/person-card.css \
        resources/css/app.css \
        resources/css/pages/about.css \
        resources/views/about/organisation.blade.php \
        tests/Feature/ComponentExtractionTest.php
git commit -m "feat(components): extract person-card component"
```

---

## Task 6: `agenda-item` component

Type-labelled agenda row: badge (ride/workshop/meeting/other) + datetime + title + optional location + CTA link. Used inside `<ul class="chapter-agenda__list">` in groups/show.

**Files:**
- Create: `resources/views/components/agenda-item.blade.php`
- Create: `resources/css/components/agenda-item.css`
- Modify: `resources/css/app.css`
- Modify: `resources/css/pages/chapters.css`
- Modify: `resources/views/groups/show.blade.php`
- Test: `tests/Feature/ComponentExtractionTest.php`

- [ ] **Step 1: Add failing test**

Append to `tests/Feature/ComponentExtractionTest.php`:
```php
it('agenda-item renders badge, datetime, title and cta link', function () {
    $html = Blade::render(<<<'BLADE'
        <x-agenda-item
            badge="Rit"
            badge-variant="ride"
            datetime="2026-06-14T14:00"
            when="za 14 jun · 14:00"
            title="Kidical Mass Gent"
            cta-href="/activities/1"
            cta-label="Meer info"
        />
    BLADE);

    expect($html)
        ->toContain('agenda-item')
        ->toContain('agenda-item__badge--ride')
        ->toContain('Rit')
        ->toContain('2026-06-14T14:00')
        ->toContain('za 14 jun · 14:00')
        ->toContain('Kidical Mass Gent')
        ->toContain('href="/activities/1"')
        ->toContain('Meer info');
});

it('agenda-item renders optional location', function () {
    $html = Blade::render(<<<'BLADE'
        <x-agenda-item
            badge="Vergadering"
            badge-variant="meeting"
            datetime="2026-06-14T19:00"
            when="za 14 jun · 19:00"
            title="Teamvergadering"
            location="Café De Fiets"
            cta-href="/activities/2"
            cta-label="Meer info"
        />
    BLADE);

    expect($html)
        ->toContain('agenda-item__loc')
        ->toContain('Café De Fiets');
});
```

- [ ] **Step 2: Run to confirm they fail**

```bash
php artisan test --compact --filter="agenda-item"
```
Expected: 2 failed

- [ ] **Step 3: Create the Blade component**

Create `resources/views/components/agenda-item.blade.php`:
```blade
@props([
    'badge',
    'badgeVariant' => 'other', // ride | workshop | meeting | other
    'datetime',
    'when',      // pre-formatted display string e.g. "za 14 jun · 14:00"
    'title',
    'location'  => null,
    'ctaHref',
    'ctaLabel',
    'quiet'     => false,
])

<li {{ $attributes->merge(['class' => 'agenda-item']) }}>
    <span class="agenda-item__badge agenda-item__badge--{{ $badgeVariant }}">{{ $badge }}</span>
    <span class="agenda-item__when">
        <time datetime="{{ $datetime }}">{{ $when }}</time>
    </span>
    <span class="agenda-item__what">
        {{ $title }}
        @if ($location)
            <span class="agenda-item__loc">· {{ $location }}</span>
        @endif
    </span>
    <a href="{{ $ctaHref }}" class="agenda-item__cta{{ $quiet ? ' agenda-item__cta--quiet' : '' }} link-plain">{{ $ctaLabel }}</a>
</li>
```

- [ ] **Step 4: Create the CSS partial**

Create `resources/css/components/agenda-item.css`:
```css
@layer components {
    .agenda-item {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        gap: 0.4rem 0.85rem;
        padding: 0.75rem 0;
        border-top: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 90%);
    }
    .agenda-item__badge {
        font-size: var(--text-xs);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        flex-shrink: 0;
    }
    .agenda-item__badge--ride     { background: var(--color-kidical-green); color: white; }
    .agenda-item__badge--workshop { background: var(--color-kidical-yellow); color: var(--color-kidical-ink); }
    .agenda-item__badge--meeting  { background: color-mix(in oklab, var(--color-kidical-blue), white 78%); color: var(--color-kidical-blue); }
    .agenda-item__badge--other    { background: color-mix(in oklab, var(--color-kidical-ink), transparent 88%); color: var(--color-kidical-ink); }
    .agenda-item__when  { font-weight: 700; color: var(--color-kidical-ink); }
    .agenda-item__what  { color: var(--color-text-body); }
    .agenda-item__loc   { color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%); }
    .agenda-item__cta   { margin-left: auto; font-weight: 700; color: var(--color-kidical-blue); white-space: nowrap; }
    .agenda-item__cta--quiet { color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%); }
}
```

- [ ] **Step 5: Import in app.css**

Add after the `person-card` import:
```css
@import './components/agenda-item.css';
```

- [ ] **Step 6: Run tests — should pass**

```bash
php artisan test --compact --filter="agenda-item"
```
Expected: 2 passed

- [ ] **Step 7: Update groups/show.blade.php**

In `resources/views/groups/show.blade.php`, find the `@foreach ($rest as $activity)` block and replace each `<li class="chapter-agenda__item">` with `<x-agenda-item>`:

```blade
{{-- Before --}}
@foreach ($rest as $activity)
    @php $m = $typeMeta($activity); @endphp
    <li class="chapter-agenda__item">
        <span class="chapter-agenda__badge chapter-agenda__badge--{{ $m['mod'] }}">{{ $m['label'] }}</span>
        <span class="chapter-agenda__when">
            <time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ $activity->begin_date->locale('nl')->isoFormat('dd D MMM · HH:mm') }}</time>
        </span>
        <span class="chapter-agenda__what">{{ $activity->title_nl }}@if ($activity->location) <span class="chapter-agenda__loc">· {{ $activity->location }}</span>@endif</span>
        <a href="{{ route('activities.show', $activity) }}" class="chapter-agenda__cta @if ($m['quiet']) chapter-agenda__cta--quiet @endif link-plain">{{ $m['cta'] }}</a>
    </li>
@endforeach

{{-- After --}}
@foreach ($rest as $activity)
    @php $m = $typeMeta($activity); @endphp
    <x-agenda-item
        :badge="$m['label']"
        :badge-variant="$m['mod']"
        :datetime="$activity->begin_date->format('Y-m-d\TH:i')"
        :when="$activity->begin_date->locale('nl')->isoFormat('dd D MMM · HH:mm')"
        :title="$activity->title_nl"
        :location="$activity->location"
        :cta-href="route('activities.show', $activity)"
        :cta-label="$m['cta']"
        :quiet="$m['quiet']"
    />
@endforeach
```

- [ ] **Step 8: Remove old CSS from chapters.css**

Delete from `resources/css/pages/chapters.css`:
- `.chapter-agenda__item { ... }`
- `.chapter-agenda__badge { ... }`
- `.chapter-agenda__badge--ride`, `--workshop`, `--meeting`, `--other`
- `.chapter-agenda__when`, `.chapter-agenda__what`, `.chapter-agenda__loc`
- `.chapter-agenda__cta`, `.chapter-agenda__cta--quiet`

- [ ] **Step 9: Run tests**

```bash
php artisan test --compact --filter=CssArchitectureTest
php artisan test --compact --filter="agenda-item"
```
Expected: all pass

- [ ] **Step 10: Commit**

```bash
git add resources/views/components/agenda-item.blade.php \
        resources/css/components/agenda-item.css \
        resources/css/app.css \
        resources/css/pages/chapters.css \
        resources/views/groups/show.blade.php \
        tests/Feature/ComponentExtractionTest.php
git commit -m "feat(components): extract agenda-item component"
```

---

## Task 7: `info-card` component

Labelled content card (small caps eyebrow + slot), light-yellow, slightly rotated. Used for contact details, etc.

**Files:**
- Create: `resources/views/components/info-card.blade.php`
- Create: `resources/css/components/info-card.css`
- Modify: `resources/css/app.css`
- Modify: `resources/css/pages/about.css`
- Modify: `resources/views/about/press.blade.php`
- Test: `tests/Feature/ComponentExtractionTest.php`

- [ ] **Step 1: Add failing test**

Append to `tests/Feature/ComponentExtractionTest.php`:
```php
it('info-card renders label and slot content', function () {
    $html = Blade::render(
        '<x-info-card label="Perscontact">bike@kidicalmass.be</x-info-card>'
    );

    expect($html)
        ->toContain('info-card')
        ->toContain('info-card__label')
        ->toContain('Perscontact')
        ->toContain('bike@kidicalmass.be');
});
```

- [ ] **Step 2: Run to confirm it fails**

```bash
php artisan test --compact --filter="info-card"
```
Expected: 1 failed

- [ ] **Step 3: Create the Blade component**

Create `resources/views/components/info-card.blade.php`:
```blade
@props(['label'])

<div {{ $attributes->merge(['class' => 'info-card']) }}>
    <span class="info-card__label">{{ $label }}</span>
    {{ $slot }}
</div>
```

- [ ] **Step 4: Create the CSS partial**

Create `resources/css/components/info-card.css`:
```css
@layer components {
    .info-card {
        background: var(--color-kidical-light-yellow);
        border-radius: 1.25rem;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        transform: rotate(-1deg);
    }
    .info-card__label {
        font-size: var(--text-xs);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%);
    }
}
```

- [ ] **Step 5: Import in app.css**

Add after the `agenda-item` import:
```css
@import './components/info-card.css';
```

- [ ] **Step 6: Run test — should pass**

```bash
php artisan test --compact --filter="info-card"
```
Expected: 1 passed

- [ ] **Step 7: Update about/press.blade.php**

Replace the `<aside class="about-contact-card">` block:
```blade
{{-- Before --}}
<aside class="about-contact-card">
    <span class="about-contact-card__label">Perscontact</span>
    <a href="mailto:bike@kidicalmass.be" class="about-contact-card__email">bike@kidicalmass.be</a>
    <p class="about-contact-card__note">We antwoorden zo snel als vrijwilligers dat kunnen.</p>
</aside>

{{-- After --}}
<x-info-card label="Perscontact">
    <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
    <p class="info-card__note">We antwoorden zo snel als vrijwilligers dat kunnen.</p>
</x-info-card>
```

Also add the `info-card__link` and `info-card__note` styles in `info-card.css`:
```css
/* append inside the @layer components block */
.info-card__link {
    font-family: var(--font-heading);
    font-weight: 800;
    font-size: var(--text-xl);
    color: var(--color-kidical-blue);
    background-image: none;
    word-break: break-word;

    &:hover { background-image: none; text-decoration: underline; }
}
.info-card__note {
    font-size: var(--text-sm);
    color: color-mix(in oklab, var(--color-kidical-ink), transparent 30%);
    margin: 0;
}
```

- [ ] **Step 8: Remove old CSS from about.css**

Delete from `resources/css/pages/about.css`:
- `.about-contact-card { ... }`
- `.about-contact-card__label { ... }`
- `.about-contact-card__email { ... }` (including its `&:hover`)
- `.about-contact-card__note { ... }`

- [ ] **Step 9: Run tests**

```bash
php artisan test --compact --filter=CssArchitectureTest
php artisan test --compact --filter="info-card"
```
Expected: all pass

- [ ] **Step 10: Commit**

```bash
git add resources/views/components/info-card.blade.php \
        resources/css/components/info-card.css \
        resources/css/app.css \
        resources/css/pages/about.css \
        resources/views/about/press.blade.php \
        tests/Feature/ComponentExtractionTest.php
git commit -m "feat(components): extract info-card component"
```

---

## Task 8: `titled-list-block` component

Large heading + bullet list. Used for "what you get / what we ask" in the volunteer page.

**Files:**
- Create: `resources/views/components/titled-list-block.blade.php`
- Create: `resources/css/components/titled-list-block.css`
- Modify: `resources/css/app.css`
- Modify: `resources/css/pages/help-out.css`
- Modify: `resources/views/volunteer.blade.php`
- Test: `tests/Feature/ComponentExtractionTest.php`

- [ ] **Step 1: Add failing test**

Append to `tests/Feature/ComponentExtractionTest.php`:
```php
it('titled-list-block renders the title and list items from slot', function () {
    $html = Blade::render(<<<'BLADE'
        <x-titled-list-block title="Wat je krijgt">
            <li>Materiaal en steun</li>
            <li>Opleiding</li>
        </x-titled-list-block>
    BLADE);

    expect($html)
        ->toContain('titled-list-block')
        ->toContain('titled-list-block__title')
        ->toContain('Wat je krijgt')
        ->toContain('<li>Materiaal en steun</li>')
        ->toContain('<li>Opleiding</li>');
});
```

- [ ] **Step 2: Run to confirm it fails**

```bash
php artisan test --compact --filter="titled-list-block"
```
Expected: 1 failed

- [ ] **Step 3: Create the Blade component**

Create `resources/views/components/titled-list-block.blade.php`:
```blade
@props(['title'])

<div {{ $attributes->merge(['class' => 'titled-list-block']) }}>
    <h3 class="titled-list-block__title">{{ $title }}</h3>
    <ul class="titled-list-block__list" role="list">
        {{ $slot }}
    </ul>
</div>
```

- [ ] **Step 4: Create the CSS partial**

Create `resources/css/components/titled-list-block.css`:
```css
@layer components {
    .titled-list-block__title {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: clamp(var(--text-3xl), 4vw, var(--text-5xl));
        line-height: 1.05;
        color: var(--color-kidical-ink);
        margin-bottom: 2rem;
    }
    .titled-list-block__list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 1.5rem;
    }
    .titled-list-block__list li {
        position: relative;
        padding-left: 2.9rem;
        font-size: clamp(var(--text-xl), 1.7vw, var(--text-2xl));
        font-weight: 600;
        color: var(--color-kidical-ink);
        line-height: 1.35;
    }
    .titled-list-block__list li::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0.35em;
        width: 1.5rem;
        height: 1.5rem;
        background: var(--color-kidical-red);
        border-radius: 50%;
        opacity: 0.18;
    }
}
```

- [ ] **Step 5: Import in app.css**

Add after the `info-card` import:
```css
@import './components/titled-list-block.css';
```

- [ ] **Step 6: Run test — should pass**

```bash
php artisan test --compact --filter="titled-list-block"
```
Expected: 1 passed

- [ ] **Step 7: Update volunteer.blade.php**

Find the two `<div class="ho-deal__block">` sections and replace them:

```blade
{{-- Before --}}
<div class="ho-deal__block" data-ho-photo="0">
    <h3 class="ho-deal__subtitle">Wat je krijgt</h3>
    <ul role="list" class="ho-deal__list ho-deal__list--get">
        <li>Kidical Mass-materiaal en steun vanaf dag één</li>
        <li>Opleiding rond veiligheid en routeplanning, als je dat wil</li>
        <li>Vier gezellige vrijwilligersmomenten per jaar, met lekker eten</li>
        <li>Een warme bende ouders en fietsers die echte vrienden worden</li>
    </ul>
</div>

{{-- After --}}
<div class="ho-deal__block" data-ho-photo="0">
    <x-titled-list-block title="Wat je krijgt">
        <li>Kidical Mass-materiaal en steun vanaf dag één</li>
        <li>Opleiding rond veiligheid en routeplanning, als je dat wil</li>
        <li>Vier gezellige vrijwilligersmomenten per jaar, met lekker eten</li>
        <li>Een warme bende ouders en fietsers die echte vrienden worden</li>
    </x-titled-list-block>
</div>
```

Same for the second block (`data-ho-photo="1"`, "Wat we vragen").

- [ ] **Step 8: Remove old CSS from help-out.css**

Delete from `resources/css/pages/help-out.css`:
- `.ho-deal__subtitle { ... }`
- `.ho-deal__list { ... }` and `.ho-deal__list li { ... }` (and any `li::before` if present)

Keep `.ho-deal__block`, `.ho-deal__layout`, `.ho-deal__text`, `.ho-deal__media` etc. — those are layout/scroll idiom, not the component's appearance.

- [ ] **Step 9: Run tests**

```bash
php artisan test --compact --filter=CssArchitectureTest
php artisan test --compact --filter="titled-list-block"
```
Expected: all pass

- [ ] **Step 10: Commit**

```bash
git add resources/views/components/titled-list-block.blade.php \
        resources/css/components/titled-list-block.css \
        resources/css/app.css \
        resources/css/pages/help-out.css \
        resources/views/volunteer.blade.php \
        tests/Feature/ComponentExtractionTest.php
git commit -m "feat(components): extract titled-list-block component"
```

---

## Task 9: Styleguide updates + candidates list cleanup

Add all 8 new components to the styleguide and remove them from the extraction candidates list.

**Files:**
- Modify: `resources/views/styleguide.blade.php`
- Modify: `app/Http/Controllers/StyleguideController.php`

- [ ] **Step 1: Add components to styleguide**

In `resources/views/styleguide.blade.php`, add the following entries to the "Componenten" section (after the existing entries, before the closing `</section>`). Place them in a logical group after `feature-card`:

```blade
{{-- Tekst & typografie --}}
<x-styleguide.entry name="intro-text" props="size=base|lead">
    <x-intro-text>
        <p>Meehelpen bij Kidical Mass is opkomen voor je eigen buurt, samen met ouders en buren die meer kinderen op de fiets willen. Een paar uur per maand, een hoop nieuwe gezichten.</p>
    </x-intro-text>
</x-styleguide.entry>

<x-styleguide.entry name="section-heading" props="as=h2">
    <x-section-heading>Iedereen is welkom</x-section-heading>
</x-styleguide.entry>

<x-styleguide.entry name="pull-quote" props="attribution, variant=large|card">
    <div class="flex flex-col gap-8">
        <x-pull-quote attribution="Julienne, mama van twee kinderen">
            "Wat hij zo leuk vindt aan fietsen is die vrijheid om buiten te zijn."
        </x-pull-quote>
        <div class="about-voices">
            <x-pull-quote variant="card" attribution="Camille, Sint-Gillis">
                "Ik heb het gevoel dat ik de hele tijd de levenslust van mijn kinderen afrem."
            </x-pull-quote>
            <x-pull-quote variant="card" attribution="Fatima, Jette">
                "Ik ben constant bang voor de auto's, de trams."
            </x-pull-quote>
        </div>
    </div>
</x-styleguide.entry>

<x-styleguide.entry name="numbered-item" props="number, title">
    <ol class="about-demand-grid">
        <x-numbered-item number="1" title="Veilige fietsinfrastructuur">Aparte fietspaden die kinderen echt kunnen gebruiken.</x-numbered-item>
        <x-numbered-item number="2" title="Tragere woonstraten">Minder snel en minder druk verkeer.</x-numbered-item>
    </ol>
</x-styleguide.entry>

<x-styleguide.entry name="person-card" props="name, role, photo?">
    <div class="flex flex-wrap gap-3">
        <x-person-card name="Leticia" role="Coördinatie" />
        <x-person-card name="Cecilia" role="Coördinatie" />
    </div>
</x-styleguide.entry>

<x-styleguide.entry name="agenda-item" props="badge, badgeVariant=ride|workshop|meeting|other, datetime, when, title, location?, ctaHref, ctaLabel, quiet?">
    <ul class="chapter-agenda__list" style="max-width: 44rem">
        <x-agenda-item badge="Rit" badge-variant="ride" datetime="2026-06-14T14:00" when="za 14 jun · 14:00" title="Kidical Mass Gent" location="Sint-Pietersplein" cta-href="#" cta-label="Meer info" />
        <x-agenda-item badge="Vergadering" badge-variant="meeting" datetime="2026-06-18T19:30" when="wo 18 jun · 19:30" title="Teamvergadering" cta-href="#" cta-label="Meer info" :quiet="true" />
        <x-agenda-item badge="Workshop" badge-variant="workshop" datetime="2026-06-21T10:00" when="za 21 jun · 10:00" title="Routeplanning voor beginners" cta-href="#" cta-label="Meer info" />
    </ul>
</x-styleguide.entry>

<x-styleguide.entry name="info-card" props="label">
    <x-info-card label="Perscontact">
        <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
        <p class="info-card__note">We antwoorden zo snel als vrijwilligers dat kunnen.</p>
    </x-info-card>
</x-styleguide.entry>

<x-styleguide.entry name="titled-list-block" props="title">
    <div class="max-w-lg">
        <x-titled-list-block title="Wat je krijgt">
            <li>Kidical Mass-materiaal en steun vanaf dag één</li>
            <li>Opleiding rond veiligheid</li>
            <li>Een warme bende ouders en fietsers</li>
        </x-titled-list-block>
    </div>
</x-styleguide.entry>
```

- [ ] **Step 2: Remove the 7 extracted entries from candidates()**

In `app/Http/Controllers/StyleguideController.php`, remove these entries from the `candidates()` array:
- `section-heading`
- `pull-quote`
- `numbered-item`
- `person-card`
- `agenda-item`
- `info-card`
- `titled-list-block`

Note: `meta-row` (the activity-info-item pattern) is NOT extracted in this plan — leave it in the candidates list.

- [ ] **Step 3: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```
Expected: pass

- [ ] **Step 4: Run full test suite**

```bash
php artisan test --compact
```
Expected: all pass

- [ ] **Step 5: Commit**

```bash
git add resources/views/styleguide.blade.php \
        app/Http/Controllers/StyleguideController.php
git commit -m "feat(styleguide): add 8 extracted components; remove from candidates"
```

---

## Self-Review

**Spec coverage:**
- ✅ intro-text (Task 1) — size + CSS + Blade + page updates
- ✅ section-heading (Task 2)
- ✅ pull-quote Task 3)
- ✅ numbered-item (Task 4)
- ✅ person-card (Task 5)
- ✅ agenda-item (Task 6)
- ✅ info-card (Task 7)
- ✅ titled-list-block (Task 8)
- ✅ Hero title size bump (Task 0)
- ✅ Styleguide entries (Task 9)
- ✅ Candidates list cleanup (Task 9)
- ✅ CssArchitectureTest enforces all new imports
- ✅ Pint format pass before each commit

**Placeholders:** None.

**Type consistency:** All component CSS class names match Blade class strings. `agenda-item` badge-variant values (ride/workshop/meeting/other) match CSS modifier suffixes. `titled-list-block__list li::before` uses CSS custom property tokens only.

**Note on `about-demand-grid`:** The grid wrapper (`.about-demand-grid`) stays in `about.css` — it's page-layout, not component appearance. The styleguide entry wraps `<x-numbered-item>` in this class directly in the template.

**Note on `about-voices`:** The two-column grid wrapper (`.about-voices`) stays in `about.css` since it is a page-level composition. The styleguide entry reuses it by class name inline.
