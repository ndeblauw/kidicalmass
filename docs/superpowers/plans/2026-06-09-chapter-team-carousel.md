# Chapter Team Carousel Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax.

**Goal:** Replace the chapter page's initials-faces row with a full-width, swipeable carousel of flat illustrated polaroid cards.

**Architecture:** Page-local change to `groups/show.blade.php` (markup + a small Alpine `x-data` for paging) and `resources/css/pages/chapters.css` (new `chapter-team__*` carousel rules, drop the old faces rules). A deterministic name→illustration helper picks a brand SVG per member; the photo slot is an `<img>` so a real portrait swaps in later. Test extends `tests/Feature/GroupsTest.php`.

**Tech Stack:** Laravel 12 / Blade, Alpine.js (already loaded), Tailwind v4 tokens, CSS scroll-snap, Pest 4.

Spec: `docs/superpowers/specs/2026-06-09-chapter-team-carousel-design.md`.

---

### Task 1: Update the chapter team test for the carousel

**Files:**
- Modify: `tests/Feature/GroupsTest.php:278-289`

- [ ] **Step 1: Rewrite the team test to assert the carousel**

Replace the `chapter home shows team faces with first names and roles (lead + crew)` test body with:

```php
test('chapter team carousel shows member cards with first names and roles', function () {
    $group = Group::create(['shortname' => 'sb2', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);
    $sofie = User::factory()->create(['name' => 'Sofie Maes']);
    $group->users()->attach($sofie);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Wij zwaaien je welkom aan de start') // headline stays
        ->assertSee('chapter-team__card')                 // polaroid card rendered
        ->assertSee('Sofie')                              // first name on the card
        ->assertSee('trekker')                            // role as plain text
        ->assertSee('img/illustrations/')                 // illustration placeholder in the photo slot
        ->assertDontSee('Organiser')                      // never the cold chip
        ->assertDontSee('chapter-team__avatar');          // old initials avatar is gone
});
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `php artisan test --compact --filter='chapter team carousel'`
Expected: FAIL (sees `chapter-team__avatar` / no `chapter-team__card` yet).

---

### Task 2: Add the deterministic illustration helper + carousel markup

**Files:**
- Modify: `resources/views/groups/show.blade.php` (`@php` block ~40-53, and the band markup 162-205)

- [ ] **Step 1: Add the illustration picker to the `@php` block**

After the `$team` definition (`show.blade.php:42`), add:

```php
        // Brand-illustration placeholder per member (no portrait field yet — D-1).
        // Deterministic by name so a person keeps the same drawing across reloads.
        $teamIllustrations = [
            'waving-rider', 'relaxed-rider', 'cyclist-peace-sign', 'rider-with-flag',
            'volunteer-with-wrench', 'longtail-with-kid', 'cargo-bike-family',
        ];
        $illustrationFor = fn (string $name) => $teamIllustrations[crc32($name) % count($teamIllustrations)];
```

- [ ] **Step 2: Replace the faces block with the carousel**

Replace the `chapter-team-band__top` block (the `@if ($team->isNotEmpty())` branch, `show.blade.php:162-182`) — the crew `<figure>`, `chapter-team-band__intro`, headline and `chapter-team__faces` `<ul>` — with:

```blade
                @if ($team->isNotEmpty())
                    {{-- WIE DIT TREKT — full-width carousel of illustrated polaroid cards.
                         Headline + nav on top; crew photo at container width below. --}}
                    <div class="chapter-team__carousel"
                        x-data="{ page(dir) { const t = $refs.track; const card = t.querySelector('.chapter-team__card'); if (!card) return; const step = card.offsetWidth + parseFloat(getComputedStyle(t).columnGap || 0); t.scrollBy({ left: dir * step, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }); } }">
                        <div class="chapter-team__head">
                            <h2 class="chapter-team__headline">Wij zwaaien je welkom aan de start</h2>
                            <div class="chapter-team__nav">
                                <button type="button" class="chapter-team__btn" aria-label="Vorige teamleden" x-on:click="page(-1)">‹</button>
                                <button type="button" class="chapter-team__btn" aria-label="Volgende teamleden" x-on:click="page(1)">›</button>
                            </div>
                        </div>

                        <ul class="chapter-team__track" x-ref="track" role="region" aria-label="Team van {{ $gemeente }}">
                            @foreach ($team as $member)
                                <li class="chapter-team__card">
                                    <span class="chapter-team__photo">
                                        <img src="{{ asset('img/illustrations/'.$illustrationFor($member['name']).'.svg') }}" alt="" aria-hidden="true">
                                    </span>
                                    <span class="chapter-team__name">{{ explode(' ', trim($member['name']))[0] }}</span>
                                    <span class="chapter-team__role">{{ $member['role'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <figure class="chapter-team-band__media">
                        <img src="{{ asset('img/photography/volunteers/volunteers-pink-vests-with-flag.jpg') }}"
                            alt="Vrijwilligers in hesjes zwaaien blij met de Kidical Mass-vlag tijdens een rit" loading="lazy">
                    </figure>
```

(The existing `chapter-join` reveal block that follows it is unchanged. The `@else` empty-team branch is unchanged.)

- [ ] **Step 3: Run the test — markup present, styling next**

Run: `php artisan test --compact --filter='chapter team carousel'`
Expected: PASS (card class, name, role, illustration all render).

---

### Task 3: Style the carousel in chapters.css

**Files:**
- Modify: `resources/css/pages/chapters.css:205-259` (replace `chapter-team-band__media`, `__faces`, `__face`, `__avatar`, keep `__name`/`__role`/headline; add carousel rules)

- [ ] **Step 1: Replace the old faces rules with carousel rules**

Remove `.chapter-team__faces`, `.chapter-team__face`, `.chapter-team__avatar`. Keep `.chapter-team-band` (band shell) but the `__top` two-column grid is no longer used by the team branch — leave it for the empty-team branch (which still uses `__top` + `__media`). Add:

```css
    /* Full-width team carousel of flat illustrated polaroids. */
    .chapter-team__carousel { margin-bottom: 2.5rem; }
    .chapter-team__head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }
    .chapter-team__head .chapter-team__headline { margin-bottom: 0; }
    .chapter-team__nav { display: flex; gap: 0.6rem; flex-shrink: 0; }
    .chapter-team__btn {
        width: 3rem;
        height: 3rem;
        border-radius: 9999px;
        border: 3px solid var(--color-kidical-ink);
        background: var(--color-kidical-blue);
        color: white;
        font-size: 1.2rem;
        line-height: 1;
        display: grid;
        place-items: center;
        cursor: pointer;
        box-shadow: 3px 3px 0 var(--color-kidical-ink);
        transition: transform 0.1s, box-shadow 0.1s;
    }
    .chapter-team__btn:active {
        transform: translate(2px, 2px);
        box-shadow: 1px 1px 0 var(--color-kidical-ink);
    }

    .chapter-team__track {
        display: flex;
        gap: 1.9rem;
        column-gap: 1.9rem;
        list-style: none;
        margin: 0;
        padding: 1.4rem 0.25rem 1.7rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
    }
    .chapter-team__track::-webkit-scrollbar { display: none; }
    .chapter-team__card {
        flex: 0 0 auto;
        scroll-snap-align: start;
        width: 13.5rem;
        background: white;
        padding: 0.7rem 0.7rem 1.05rem;
        border-radius: 0.55rem;
        border: 2px solid var(--color-kidical-ink);
        box-shadow: 4px 5px 0 color-mix(in oklab, var(--color-kidical-ink), transparent 12%);
        text-align: center;
    }
    .chapter-team__card:nth-child(odd)  { transform: rotate(-3deg); }
    .chapter-team__card:nth-child(even) { transform: rotate(2.5deg); }
    .chapter-team__photo {
        display: grid;
        place-items: center;
        width: 100%;
        aspect-ratio: 1 / 1;
        border-radius: 0.25rem;
        overflow: hidden;
        margin-bottom: 0.7rem;
        background: var(--color-kidical-light-yellow);
    }
    .chapter-team__card:nth-child(5n+2) .chapter-team__photo { background: var(--color-kidical-light-blue); }
    .chapter-team__card:nth-child(5n+3) .chapter-team__photo { background: color-mix(in oklab, var(--color-kidical-red), white 80%); }
    .chapter-team__card:nth-child(5n+4) .chapter-team__photo { background: color-mix(in oklab, var(--color-kidical-green), white 78%); }
    .chapter-team__card:nth-child(5n)   .chapter-team__photo { background: color-mix(in oklab, var(--color-kidical-orange), white 80%); }
    .chapter-team__photo img { max-width: 88%; max-height: 88%; object-fit: contain; }
    .chapter-team__card .chapter-team__name { display: block; }
    .chapter-team__card .chapter-team__role { display: block; margin-top: 0.3rem; }

    @media (max-width: 640px) {
        .chapter-team__head { align-items: center; }
        .chapter-team__card { width: 11.5rem; }
    }
```

Adjust the kept `.chapter-team__role` rule so it reads as plain uppercase text (it already is muted; add `text-transform: uppercase; letter-spacing: 0.06em;` if not present).

- [ ] **Step 2: Build assets**

Run: `npm run build`
Expected: builds without error.

- [ ] **Step 3: Run the full group test file**

Run: `php artisan test --compact --filter=Group`
Expected: PASS (carousel test + empty-state + ordering tests).

- [ ] **Step 4: Visual check**

Screenshot `https://kidicalmass.test/nl/lokale-groepen/<a-group-with-team>` (or the styleguide route) and confirm: full-width carousel, tilt, flat shadow, blue outlined buttons paging one card, crew photo below, CTA below that.

---

### Task 4: Pint + commit

- [ ] **Step 1: Format**

Run: `vendor/bin/pint --dirty --format agent`

- [ ] **Step 2: Commit**

```bash
git add resources/views/groups/show.blade.php resources/css/pages/chapters.css tests/Feature/GroupsTest.php
git commit -m "feat(chapter): full-width illustrated team carousel"
```

---

## Self-Review

- **Spec coverage:** look ✓ (flat polaroid, tilt, subtle shadow, plain role, drawn buttons); layout ✓ (head row → carousel → container-width crew photo → CTA); placeholder ✓ (deterministic name→illustration `<img>`, swap point); data ✓ (`$team` unchanged); empty-team ✓ (untouched); CSS in pages/chapters.css ✓; tests ✓.
- **Placeholder scan:** none.
- **Type consistency:** `$illustrationFor`/`$teamIllustrations`, classes `chapter-team__{carousel,head,headline,nav,btn,track,card,photo,name,role}` consistent across Tasks 2-3 and the test.
