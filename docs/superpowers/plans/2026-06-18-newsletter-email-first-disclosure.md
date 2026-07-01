# Newsletter Email-First with Progressive Group Disclosure — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make `/nl/nieuwsbrief` lead with email capture and hide the location/group picker behind a progressive-disclosure link, without a typed email ever being lost.

**Architecture:** Add a `reactive` mode to the shared `LocationPicker` so it dispatches a `location-selected` event instead of redirecting (no navigation = email survives). `NewsletterSignup` becomes disclosure-aware (`showGroups`, `pickedLocation`), listens for that event, and validates "at least one group" only when nearby chips are actually shown. The Blade view is reordered to email → optional groups → submit.

**Tech Stack:** Laravel 12, Livewire 4, Pest 4, Tailwind v4 (token-backed CSS partials).

## Global Constraints

- Public-site headings use raw `<h1>`–`<h6>`, never `flux:heading`. (Not touched here, but no new headings added.)
- All CSS lives in role-based partials; this feature's CSS goes in `resources/css/pages/nieuwsbrief.css` under `@layer components`. Tokens only — no raw hex/px (enforced by `CssArchitectureTest`).
- Copy follows `docs/tone-of-voice.md`. No em-dashes in site copy.
- NL copy strings, verbatim:
  - Disclosure link: `Ritten bij jou in de buurt kiezen`
  - Collapse link: `Toch geen groepen kiezen`
  - Deselect-all error: `Kies minstens één groep bij jou in de buurt.`
- Default (non-reactive) `LocationPicker` behaviour must stay identical — Kalender and Lokale groepen depend on its redirect.
- Backend persistence + double opt-in mail stay out of scope (`subscribe()` keeps its optimistic, non-persisting confirmation and the existing `TODO(backend, Nico)` comment).
- Shared working tree: stage by explicit path, never `git add -A`.

---

### Task 1: `LocationPicker` reactive mode

**Files:**
- Modify: `app/Livewire/LocationPicker.php`
- Test: `tests/Feature/Location/LocationPickerTest.php`

**Interfaces:**
- Consumes: nothing new.
- Produces:
  - `public bool $reactive = false;`
  - `public ?array $selected = null;` — shape `array{zip:string,lat:float,lng:float,name:string}|null`
  - Dispatches Livewire event `location-selected` with a single named param `payload` (the same array shape, or `null` on clear) **only when `$reactive === true`**.

- [ ] **Step 1: Write the failing tests**

Append to `tests/Feature/Location/LocationPickerTest.php`:

```php
it('dispatches location-selected and does not redirect in reactive mode', function () {
    Livewire::test(LocationPicker::class, ['reactive' => true])
        ->call('choose', '9000')
        ->assertDispatched('location-selected')
        ->assertNoRedirect();

    expect(Cookie::hasQueued(config('location.cookie')))->toBeTrue();
});

it('dispatches a null payload on clear in reactive mode without redirecting', function () {
    Livewire::test(LocationPicker::class, ['reactive' => true])
        ->call('clear')
        ->assertDispatched('location-selected')
        ->assertNoRedirect();
});

it('still redirects on choose in default (non-reactive) mode', function () {
    Livewire::test(LocationPicker::class)
        ->call('choose', '1090')
        ->assertRedirect();
});
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `php artisan test --compact --filter=LocationPickerTest`
Expected: the two reactive tests FAIL (they currently redirect / don't dispatch); the non-reactive test passes.

- [ ] **Step 3: Implement reactive mode**

In `app/Livewire/LocationPicker.php`, add the properties below the existing ones:

```php
    public bool $reactive = false;

    /** @var array{zip: string, lat: float, lng: float, name: string}|null */
    public ?array $selected = null;
```

Replace the `clear()` method with:

```php
    public function clear(): void
    {
        Cookie::queue(Cookie::forget(config('location.cookie')));

        if ($this->reactive) {
            $this->selected = null;
            $this->dispatch('location-selected', payload: null);

            return;
        }

        $this->redirect($this->currentUrl(), navigate: true);
    }
```

Replace the `persist()` method with:

```php
    protected function persist(string $zip, float $lat, float $lng, string $name): void
    {
        Cookie::queue(
            config('location.cookie'),
            json_encode(['zip' => $zip, 'lat' => $lat, 'lng' => $lng, 'name' => $name]),
            config('location.cookie_days') * 24 * 60,
        );

        if ($this->reactive) {
            $this->selected = ['zip' => $zip, 'lat' => $lat, 'lng' => $lng, 'name' => $name];
            $this->dispatch('location-selected', payload: $this->selected);

            return;
        }

        $this->redirect($this->currentUrl(), navigate: true);
    }
```

Update `render()` so the picker can show the just-picked place (the freshly-queued cookie is not readable this request):

```php
    public function render()
    {
        return view('livewire.location-picker', [
            'current' => $this->selected ?? CurrentLocation::resolve(),
            'suggestions' => $this->suggestions(),
        ]);
    }
```

- [ ] **Step 4: Run the tests to verify they pass**

Run: `php artisan test --compact --filter=LocationPickerTest`
Expected: PASS (all, including the existing redirect tests).

- [ ] **Step 5: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/LocationPicker.php tests/Feature/Location/LocationPickerTest.php
git commit -m "feat(location-picker): add reactive mode that dispatches instead of redirecting

Why: the newsletter signup needs to update nearby groups without a full
page navigation, which would wipe a typed-but-unsubmitted email.

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 2: `NewsletterSignup` disclosure state, event handling, conditional validation

**Files:**
- Modify: `app/Livewire/NewsletterSignup.php`
- Test: `tests/Feature/NewsletterSignupTest.php`

**Interfaces:**
- Consumes: `location-selected` event from Task 1 (named param `payload`, shape `array{zip,lat,lng,name}|null`).
- Produces (relied on by the view in Task 3):
  - `public bool $showGroups`
  - `public ?array $pickedLocation`
  - `revealGroups(): void`, `hideGroups(): void`
  - `#[On('location-selected')] setLocation(?array $payload): void`
  - `nearbyGroups()` resolves origin from `$pickedLocation` first, else the cookie.
  - Validation: `selectedGroups` requires `min:1` **only when `showGroups` is true and `nearbyGroups()` is non-empty**.

- [ ] **Step 1: Write the failing tests**

Replace the body of `tests/Feature/NewsletterSignupTest.php` with (keeps the passing cases, updates the stale "all-groups" case, adds the new behaviour):

```php
<?php

use App\Livewire\NewsletterSignup;
use App\Models\Group;
use App\Models\PostalCode;
use App\Models\User;
use Livewire\Livewire;

it('rejects an invalid email and stays on the form', function () {
    Livewire::test(NewsletterSignup::class)
        ->set('email', 'not-an-email')
        ->call('subscribe')
        ->assertHasErrors(['email' => 'email'])
        ->assertSet('submitted', false);
});

it('lets a cold visitor subscribe with email only, groups collapsed', function () {
    Livewire::test(NewsletterSignup::class)
        ->assertSet('showGroups', false)
        ->assertSee('Ritten bij jou in de buurt kiezen')
        ->assertDontSee('Bekijk alle groepen')
        ->set('email', 'ouders@example.be')
        ->call('subscribe')
        ->assertSet('submitted', true)
        ->assertSee('Kijk even in je mailbox');
});

it('reveals the picker when the disclosure link is clicked', function () {
    Livewire::test(NewsletterSignup::class)
        ->call('revealGroups')
        ->assertSet('showGroups', true);
});

it('populates and pre-selects nearby groups when a location is picked, keeping the email', function () {
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);
    $group = Group::create([
        'shortname' => 'gent',
        'name' => 'Kidical Mass Gent',
        'zip' => '9000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    Livewire::test(NewsletterSignup::class)
        ->set('email', 'ouders@example.be')
        ->dispatch('location-selected', payload: ['zip' => '9000', 'lat' => 51.0543, 'lng' => 3.7174, 'name' => 'Gent'])
        ->assertSet('showGroups', true)
        ->assertSet('selectedGroups', [$group->id])
        ->assertSet('email', 'ouders@example.be')
        ->assertSee('Groepen bij jou in de buurt');
});

it('blocks submit when nearby groups are shown but all are deselected', function () {
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);
    Group::create([
        'shortname' => 'gent',
        'name' => 'Kidical Mass Gent',
        'zip' => '9000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    Livewire::test(NewsletterSignup::class)
        ->set('email', 'ouders@example.be')
        ->dispatch('location-selected', payload: ['zip' => '9000', 'lat' => 51.0543, 'lng' => 3.7174, 'name' => 'Gent'])
        ->set('selectedGroups', [])
        ->call('subscribe')
        ->assertHasErrors('selectedGroups')
        ->assertSet('submitted', false);
});

it('collapsing groups clears the selection and allows an email-only submit', function () {
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);
    Group::create([
        'shortname' => 'gent',
        'name' => 'Kidical Mass Gent',
        'zip' => '9000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    Livewire::test(NewsletterSignup::class)
        ->set('email', 'ouders@example.be')
        ->dispatch('location-selected', payload: ['zip' => '9000', 'lat' => 51.0543, 'lng' => 3.7174, 'name' => 'Gent'])
        ->call('hideGroups')
        ->assertSet('showGroups', false)
        ->assertSet('selectedGroups', [])
        ->call('subscribe')
        ->assertSet('submitted', true);
});

it('does not require a group when revealed without any nearby chapters', function () {
    Livewire::test(NewsletterSignup::class)
        ->call('revealGroups')
        ->set('email', 'ouders@example.be')
        ->call('subscribe')
        ->assertSet('submitted', true);
});

it('greets a logged-in visitor instead of showing the form', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(NewsletterSignup::class)
        ->assertSee('Je bent al mee')
        ->assertDontSee('Je e-mailadres');
});
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `php artisan test --compact --filter=NewsletterSignupTest`
Expected: new cases FAIL (`showGroups`/`revealGroups`/`hideGroups`/`setLocation` undefined).

- [ ] **Step 3: Implement the component changes**

In `app/Livewire/NewsletterSignup.php`, add the import near the other `use` lines:

```php
use Livewire\Attributes\On;
```

Add the new properties below `public bool $submitted = false;`:

```php
    public bool $showGroups = false;

    /** @var array{zip: string, lat: float, lng: float, name: string}|null */
    public ?array $pickedLocation = null;
```

Replace `mount()` with:

```php
    public function mount(): void
    {
        // If we already know where they are (shared kcm_location cookie), expand
        // the groups straight away and pre-select every nearby chapter (opt-out).
        // Cold visitors start collapsed: email first, groups behind a link.
        if (CurrentLocation::resolve() !== null) {
            $this->showGroups = true;
            $this->selectedGroups = $this->nearbyGroups()->pluck('id')->all();
        }
    }
```

Add these methods directly after `mount()`:

```php
    public function revealGroups(): void
    {
        $this->showGroups = true;
    }

    public function hideGroups(): void
    {
        $this->showGroups = false;
        $this->selectedGroups = [];
        $this->pickedLocation = null;
    }

    /**
     * The reactive location picker dispatches this without a page navigation, so
     * a typed email survives. Recompute nearby chapters from the passed coords
     * and pre-select them (opt-out default).
     *
     * @param  array{zip: string, lat: float, lng: float, name: string}|null  $payload
     */
    #[On('location-selected')]
    public function setLocation(?array $payload): void
    {
        $this->pickedLocation = $payload;
        $this->showGroups = true;
        $this->selectedGroups = $this->nearbyGroups()->pluck('id')->all();
    }
```

Replace `subscribe()` with:

```php
    public function subscribe(): void
    {
        $rules = ['email' => 'required|email'];

        // Only constrain group choice when chips are actually on screen: a
        // visitor who revealed the section but hasn't picked a location (no
        // nearby chapters) is still free to subscribe with email only.
        if ($this->showGroups && $this->nearbyGroups()->isNotEmpty()) {
            $rules['selectedGroups'] = 'array|min:1';
        }

        $this->validate($rules, [
            'selectedGroups.min' => 'Kies minstens één groep bij jou in de buurt.',
        ]);

        // TODO(backend, Nico): persist the e-mail + chosen scope (location /
        // selectedGroups) to the Email-subscription model and send the
        // double opt-in mail. Until that lands this is an optimistic,
        // non-persisting confirmation.

        $this->submitted = true;
    }
```

In `nearbyGroups()`, replace the first resolving line:

```php
        $location = CurrentLocation::resolve();
```

with:

```php
        $location = $this->pickedLocation ?? CurrentLocation::resolve();
```

and make the origin cast-safe (event payloads arrive as JSON numbers) — replace:

```php
        $origin = ['lat' => $location['lat'], 'lng' => $location['lng']];
```

with:

```php
        $origin = ['lat' => (float) $location['lat'], 'lng' => (float) $location['lng']];
```

Leave `render()` as-is — it already passes `'groups' => $this->nearbyGroups()` and `'location'`.

- [ ] **Step 4: Run the tests to verify they pass**

Run: `php artisan test --compact --filter=NewsletterSignupTest`
Expected: PASS (all cases).

- [ ] **Step 5: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/NewsletterSignup.php tests/Feature/NewsletterSignupTest.php
git commit -m "feat(newsletter): email-first signup with progressive group disclosure

- showGroups/pickedLocation state; reveal/hide actions
- listen for location-selected to recompute nearby chapters without reload
- require a group only when nearby chips are shown

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 3: View reorder + disclosure UI + CSS + page test

**Files:**
- Modify: `resources/views/livewire/newsletter-signup.blade.php`
- Modify: `resources/css/pages/nieuwsbrief.css`
- Test: `tests/Feature/NewsletterPageTest.php`

**Interfaces:**
- Consumes: `showGroups`, `revealGroups`, `hideGroups`, `selectedGroups`, `$groups`, `$location` from Task 2; `<livewire:location-picker :reactive="true">` from Task 1.
- Produces: the rendered form. Order is **email field → groups disclosure → submit button** (CTA stays the terminal action; the disclosure link sits directly under the email field).

- [ ] **Step 1: Update the failing page tests**

In `tests/Feature/NewsletterPageTest.php`, the cold-page test currently asserts the removed `Bekijk alle groepen` copy. Replace the `it('shows the signup page with hero, form and reassurance', ...)` block with:

```php
it('shows the signup page with hero, form and reassurance', function () {
    get('/nl/nieuwsbrief')
        ->assertOk()
        ->assertSee('Elke maand de nieuwste ritten in je bus')
        ->assertSee('Je e-mailadres')
        ->assertSee('Geen spam, uitschrijven met één klik')
        ->assertSee('Ritten bij jou in de buurt kiezen')
        ->assertDontSee('Bekijk alle groepen');
});
```

The `it('reflects the saved location and shows nearby chapters immediately', ...)` block already asserts the expanded state with a cookie — leave it, it now also proves `showGroups` defaults true when a cookie is present.

- [ ] **Step 2: Run the page tests to verify the cold case fails**

Run: `php artisan test --compact --filter=NewsletterPageTest`
Expected: the cold-page test FAILS (view still renders the old markup / no disclosure link yet).

- [ ] **Step 3: Rewrite the view's unauthenticated branch**

Replace the entire `@else` … `@endauth` region of `resources/views/livewire/newsletter-signup.blade.php` (everything from line 14 `@else` down to `@endauth`) with:

```blade
    @else
        {{-- Email first: the essential input. Group/location selection is optional
             refinement, revealed on demand. The reactive picker dispatches
             location-selected instead of navigating, so this email survives. --}}
        <form wire:submit="subscribe" class="flex flex-col gap-6">
            <div class="newsletter-signup__email">
                <label for="newsletter-email">Je e-mailadres</label>
                <input
                    id="newsletter-email"
                    type="email"
                    wire:model="email"
                    autocomplete="email"
                    placeholder="jouw@email.be"
                    class="newsletter-signup__input"
                >
                @error('email') <p class="newsletter-signup__error">{{ $message }}</p> @enderror
            </div>

            <div class="newsletter-signup__groups">
                @if ($showGroups)
                    <div class="newsletter-signup__reveal newsletter-signup__location">
                        <livewire:location-picker :reactive="true" :compact="true" wire:key="newsletter-location" />
                    </div>

                    @if ($groups->isNotEmpty())
                        <fieldset class="newsletter-signup__reveal">
                            <legend class="newsletter-signup__groups-legend">Groepen bij jou in de buurt</legend>
                            <p class="newsletter-signup__groups-hint">
                                We sturen je standaard de ritten van deze groepen. Klik een groep weg die je niet wil volgen.
                            </p>
                            <div class="newsletter-signup__chips">
                                @foreach ($groups as $group)
                                    <label class="newsletter-signup__chip">
                                        <input type="checkbox" wire:model="selectedGroups" value="{{ $group->id }}" class="sr-only">
                                        <span>{{ $group->gemeente }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedGroups') <p class="newsletter-signup__error">{{ $message }}</p> @enderror
                        </fieldset>
                    @endif

                    <button type="button" wire:click="hideGroups" class="newsletter-signup__disclosure">
                        Toch geen groepen kiezen
                    </button>
                @else
                    <button type="button" wire:click="revealGroups" class="newsletter-signup__disclosure">
                        Ritten bij jou in de buurt kiezen
                    </button>
                @endif
            </div>

            {{-- cta-button hardcodes type="button", so it cannot natively submit a
                 form; wire:click handles the click and the form's wire:submit
                 handles Enter. Both route to subscribe(). self-start keeps it
                 sized to its label instead of stretching to the column. --}}
            <x-cta-button variant="blue" icon="arrow" wire:click="subscribe" class="self-start">Schrijf me in</x-cta-button>
        </form>
    @endauth
```

- [ ] **Step 4: Add the disclosure + reveal CSS**

In `resources/css/pages/nieuwsbrief.css`, inside the existing `@layer components { … }` block, add these rules (place them just after the `.newsletter-signup__groups-hint` rule):

```css
    .newsletter-signup__disclosure {
        align-self: flex-start;
        padding: 0;
        border: 0;
        background: none;
        color: var(--color-kidical-blue);
        text-decoration: underline;
        text-underline-offset: 0.2em;
        cursor: pointer;
    }

    .newsletter-signup__reveal {
        animation: newsletter-signup-reveal 0.2s ease-out both;
    }

    @keyframes newsletter-signup-reveal {
        from {
            opacity: 0;
            transform: translateY(-0.25rem);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .newsletter-signup__reveal {
            animation: none;
        }
    }
```

- [ ] **Step 5: Run the page tests + CSS architecture test**

Run: `php artisan test --compact --filter="NewsletterPageTest|CssArchitectureTest"`
Expected: PASS (cold page shows the disclosure link; cookie page shows expanded chips; no raw hex/px introduced).

- [ ] **Step 6: Build assets and verify the full newsletter suite**

Run: `npm run build && php artisan test --compact --filter="Newsletter|LocationPicker"`
Expected: build succeeds; all newsletter + location-picker tests PASS.

- [ ] **Step 7: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/livewire/newsletter-signup.blade.php resources/css/pages/nieuwsbrief.css tests/Feature/NewsletterPageTest.php
git commit -m "feat(newsletter): reorder to email-first with disclosure UI + reveal motion

- email field first, optional groups behind a quiet link, submit last
- reactive location picker; quiet collapse link; subtle reveal animation
- update page test for the new disclosure copy

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

## Self-Review

**Spec coverage:**
- Email-first ordering → Task 3 (view reorder, CTA last).
- Three states (cold / revealed / known-location) → Task 2 (`mount`, `revealGroups`, `setLocation`) + Task 3 (view branches).
- Reactive picker / no-navigation / email survives → Task 1 + Task 2 test "keeps the email".
- Cookie persists site-wide → Task 1 keeps `Cookie::queue` in both modes (asserted via `hasQueued`).
- Conditional "at least one group" validation → Task 2 `subscribe()` + tests (deselect-all blocks; revealed-without-chapters does not).
- Collapse escape hatch → Task 2 `hideGroups()` + test.
- Copy strings → Global Constraints, used verbatim in Tasks 2 & 3.
- Styling (plain links, only CTA loud, reduced-motion reveal, tokens only) → Task 3 CSS.
- Default picker unchanged for other pages → Task 1 non-reactive test asserts redirect.
- Tests enumerated in spec → all present across Tasks 1–3.

**Placeholder scan:** none — every code step shows complete code; the only `TODO` is the pre-existing backend marker, intentionally retained and called out in Global Constraints.

**Type consistency:** event name `location-selected` and param `payload` (shape `array{zip,lat,lng,name}|null`) match between Task 1 (dispatch) and Task 2 (`setLocation`). Method names `revealGroups`/`hideGroups`/`setLocation`/`subscribe` and props `showGroups`/`pickedLocation`/`selectedGroups` are consistent across Tasks 2 and 3. Picker props `reactive`/`compact` match between Task 1 and the Task 3 view.

**Resolved ambiguity:** "disclosure link below the email" is implemented as email → groups disclosure → submit (CTA remains the terminal action), rather than placing content below the submit button.
