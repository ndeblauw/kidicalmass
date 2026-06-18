# Newsletter Signup Page (`/nl/nieuwsbrief`) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the dedicated newsletter signup page the existing teaser CTAs point at — a focused page with a smart "rides near me" default, an optional nearby-group picker, and a full double-opt-in journey (form → "check je inbox" → confirmed landing).

**Architecture:** Two static `Route::view` routes (`newsletter.show`, `newsletter.confirmed`). The signup page view owns the chip hero + two-column layout and embeds a Livewire component (`NewsletterSignup`) that holds the form state, location pre-fill (from the shared `kcm_location` cookie via `CurrentLocation::resolve()`), the nearby-group picker, and the in-place swap to the "check je inbox" state. A reusable `<x-envelope-chips>` component carries the hero motif. The confirmed landing is a separate static view (reached from the email link). Backend persistence stays stubbed — `subscribe()` validates + swaps state, with a single clearly-marked seam where Nico's Email-subscription model + double-opt-in mail will plug in.

**Tech Stack:** Laravel 12, Livewire 4 (class-based MFC), Blade, Tailwind v4, Pest 4, CSS partials architecture.

## Global Constraints

- **Copy:** Dutch, tone-of-voice compliant, warm/local. **No em-dashes anywhere** (Frederik flags them as an AI tell).
- **Headings:** raw `<h1>`–`<h6>`, never `flux:heading`.
- **CSS:** new `.css` partials MUST get an `@import './…'` line in `resources/css/app.css` or `CssArchitectureTest` fails. No raw hex/`px` inside `resources/views/components/**` (Tailwind arbitrary values / inline styles) — use tokens. Page-template files may use composition utilities (`grid`, `flex`, `gap-*`, `max-w-*`, `mx-auto`, `p*`); appearance lives in components or partials.
- **Route names are stable:** `newsletter.show`, `newsletter.confirmed`. Paths use NL vocab (`nieuwsbrief`, `nieuwsbrief/bevestigd`).
- **No backend this round:** no `Subscriber`/`Email subscription` model, no migration, no mail. `subscribe()` is the seam; mark it `// TODO(backend, Nico)`.
- **Pint:** run `vendor/bin/pint --dirty --format agent` before finalizing any task that touched PHP.
- **Shared checkout:** Nico commits concurrently. Stage by explicit path, never `git add -A`. See the dirty-tree note below.
- **Tests:** Pest. Run with `php artisan test --compact --filter=…`.

### ⚠️ Dirty-tree note (read before Task 5)

`resources/views/components/newsletter-cta.blade.php`, `newsletter-optin.blade.php`, and `resources/css/components/newsletter-cta.css` are **already modified in the working tree** (the team's in-progress "teaser" refactor — the inline forms were removed, leaving a CTA button with `href="#"`). As a result `tests/Feature/NewsletterCtaTest.php` and `NewsletterOptinTest.php` are **already red** (they assert the old form markup) — this is pre-existing, not caused by this build. Task 5 wires the teasers to the new route and reconciles those two test files. **Before committing Task 5, confirm with Frederik/Nico** that you may touch their in-progress components/tests; if not, ship only the two `href` edits and let them own the test reconciliation.

---

### Task 1: Reusable `<x-envelope-chips>` component

The three-envelope motif currently lives only inside the home newsletter band. Extract a clean, token-only, reusable version for the new hero (no seam positioning, no scroll-reveal — the hero is above the fold).

**Files:**
- Create: `resources/views/components/envelope-chips.blade.php`
- Create: `resources/css/components/envelope-chips.css`
- Modify: `resources/css/app.css` (add one `@import`)
- Test: `tests/Feature/EnvelopeChipsTest.php`

**Interfaces:**
- Produces: `<x-envelope-chips />` — a decorative (`aria-hidden`) inline-flex span emitting `.envelope-chips__chip--green|red|blue`.

- [ ] **Step 1: Write the failing test**

```php
<?php

use Illuminate\Support\Facades\Blade;

it('renders three envelope chips, one per brand colour', function () {
    $html = Blade::render('<x-envelope-chips />');

    expect($html)
        ->toContain('envelope-chips__chip--green')
        ->toContain('envelope-chips__chip--red')
        ->toContain('envelope-chips__chip--blue');

    expect(substr_count($html, 'envelope-chips__chip envelope-chips__chip--'))->toBe(3);
});

it('marks the decorative chips aria-hidden', function () {
    expect(Blade::render('<x-envelope-chips />'))->toContain('aria-hidden="true"');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=EnvelopeChips`
Expected: FAIL (component view not found).

- [ ] **Step 3: Create the component**

`resources/views/components/envelope-chips.blade.php`:

```blade
{{-- Three overlapping envelope chips in the brand colours. A decorative motif
     shared between the home newsletter band and the nieuwsbrief hero. --}}
<span {{ $attributes->class('envelope-chips') }} aria-hidden="true">
    @foreach (['green', 'red', 'blue'] as $tone)
        <span class="envelope-chips__chip envelope-chips__chip--{{ $tone }}">
            <svg viewBox="0 0 24 24" fill="none">
                <rect x="2.75" y="5" width="18.5" height="14" rx="2.5" stroke="currentColor" stroke-width="2"/>
                <path d="M4 7l8 5.5L20 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
    @endforeach
</span>
```

- [ ] **Step 4: Create the CSS partial**

`resources/css/components/envelope-chips.css`:

```css
@layer components {
    .envelope-chips {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }

    .envelope-chips__chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 4.5rem;
        height: 4.5rem;
        border-radius: var(--radius-chip);
        color: white;
        box-shadow: var(--shadow-card);
        transform: rotate(var(--chip-rotate, 0deg));
    }

    .envelope-chips__chip svg {
        width: 2rem;
        height: 2rem;
    }

    .envelope-chips__chip:not(:first-child) {
        margin-left: -0.65rem;
    }

    .envelope-chips__chip--green {
        --chip-rotate: -10deg;
        background: var(--color-kidical-green);
    }

    .envelope-chips__chip--red {
        --chip-rotate: 3deg;
        background: var(--color-kidical-red);
        width: 5rem;
        height: 5rem;
        z-index: 1;
    }

    .envelope-chips__chip--blue {
        --chip-rotate: 11deg;
        background: var(--color-kidical-blue);
    }
}
```

- [ ] **Step 5: Register the partial in `app.css`**

In `resources/css/app.css`, add alongside the other `components/` imports (near `@import './components/cta-button.css';`):

```css
@import './components/envelope-chips.css';
```

- [ ] **Step 6: Run tests (incl. the CSS architecture guard)**

Run: `php artisan test --compact --filter="EnvelopeChips|CssArchitecture"`
Expected: PASS (all green — the new partial is registered, no raw hex/px).

- [ ] **Step 7: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/envelope-chips.blade.php resources/css/components/envelope-chips.css resources/css/app.css tests/Feature/EnvelopeChipsTest.php
git commit -m "feat(newsletter): reusable envelope-chips hero motif"
```

---

### Task 2: Confirmed landing page (`newsletter.confirmed`)

The double-opt-in confirmation screen, reached by clicking the link in the email — so it is its own URL, not a state toggle. Static view.

**Files:**
- Modify: `routes/web.php` (one route inside the `{locale}` group)
- Create: `resources/views/newsletter/confirmed.blade.php`
- Modify: `tests/Feature/PublicPagesRenderTest.php` (add URI to smoke dataset)
- Test: `tests/Feature/NewsletterPageTest.php` (new file)

**Interfaces:**
- Produces: route `newsletter.confirmed` at `/{locale}/nieuwsbrief/bevestigd`.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/NewsletterPageTest.php`:

```php
<?php

use function Pest\Laravel\get;

it('welcomes the subscriber and offers next steps on the confirmed page', function () {
    get('/nl/nieuwsbrief/bevestigd')
        ->assertOk()
        ->assertSee('Je bent erbij')
        ->assertSee('Bekijk de kalender')
        ->assertSee('Vind je groep');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=NewsletterPage`
Expected: FAIL (404 / route not defined).

- [ ] **Step 3: Add the route**

In `routes/web.php`, inside the `Route::prefix('{locale}')->whereIn('locale', SetLocale::SUPPORTED)->middleware('setlocale')->group(...)` closure, next to the other `Route::view(...)` static pages (around the `getting-started`/`about` lines):

```php
Route::view('nieuwsbrief/bevestigd', 'newsletter.confirmed')->name('newsletter.confirmed');
```

- [ ] **Step 4: Create the view**

`resources/views/newsletter/confirmed.blade.php`:

```blade
<x-layouts::site title="Je bent erbij">
    <section class="flex flex-col items-center gap-6 text-center max-w-2xl mx-auto py-16">
        <x-envelope-chips />

        <h1>Je bent erbij!</h1>

        <p class="max-w-xl">
            Vanaf nu mis je niets meer. Eén keer per maand laten we je weten
            waar er bij jou in de buurt gefietst wordt.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <x-cta-button :href="route('activities.index')" variant="yellow" icon="arrow">Bekijk de kalender</x-cta-button>
            <x-cta-button :href="route('groups.index')" variant="secondary" icon="arrow">Vind je groep</x-cta-button>
        </div>
    </section>
</x-layouts::site>
```

- [ ] **Step 5: Add the page to the smoke dataset**

In `tests/Feature/PublicPagesRenderTest.php`, add `'/nl/nieuwsbrief/bevestigd'` to the `->with([...])` list of URIs.

- [ ] **Step 6: Run tests**

Run: `php artisan test --compact --filter="NewsletterPage|PublicPagesRender"`
Expected: PASS.

- [ ] **Step 7: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add routes/web.php resources/views/newsletter/confirmed.blade.php tests/Feature/NewsletterPageTest.php tests/Feature/PublicPagesRenderTest.php
git commit -m "feat(newsletter): confirmed landing page"
```

---

### Task 3: `NewsletterSignup` Livewire component

The form's brain: location pre-fill, email validation, the optional nearby-group picker, and the in-place swap to the "check je inbox" state. Authenticated visitors get the "Je bent al mee" short-circuit instead of the form.

**Files:**
- Create: `app/Livewire/NewsletterSignup.php`
- Create: `resources/views/livewire/newsletter-signup.blade.php`
- Test: `tests/Feature/NewsletterSignupTest.php`

**Interfaces:**
- Consumes: `App\Support\Location\CurrentLocation::resolve(): ?array{zip,lat,lng,name}`, `App\Support\Location\Proximity::distanceKm(array $from, array $to): float`, `App\Models\PostalCode` (`zip`,`latitude`,`longitude`), `App\Models\Group` (`scopeVisible`, `name`, `zip`).
- Produces: `<livewire:newsletter-signup />`. Public state: `email`, `zip`, `locationName`, `showGroupPicker`, `selectedGroups[]`, `submitted`. Actions: `subscribe()`, `toggleGroupPicker()`.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/NewsletterSignupTest.php`:

```php
<?php

use App\Livewire\NewsletterSignup;
use App\Models\Group;
use App\Models\User;
use Livewire\Livewire;

it('rejects an invalid email and stays on the form', function () {
    Livewire::test(NewsletterSignup::class)
        ->set('email', 'not-an-email')
        ->call('subscribe')
        ->assertHasErrors(['email' => 'email'])
        ->assertSet('submitted', false);
});

it('moves to the check-inbox state on a valid email', function () {
    Livewire::test(NewsletterSignup::class)
        ->set('email', 'ouders@example.be')
        ->call('subscribe')
        ->assertSet('submitted', true)
        ->assertSee('Kijk even in je mailbox')
        ->assertSee('ouders@example.be');
});

it('reveals visible groups in the picker on demand', function () {
    Group::create([
        'shortname' => 'gent',
        'name' => 'Kidical Mass Gent',
        'zip' => '9000',
        'invisible' => false,
        'started_at' => now(),
    ]);

    Livewire::test(NewsletterSignup::class)
        ->assertSet('showGroupPicker', false)
        ->call('toggleGroupPicker')
        ->assertSet('showGroupPicker', true)
        ->assertSee('Kidical Mass Gent');
});

it('greets a logged-in visitor instead of showing the form', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(NewsletterSignup::class)
        ->assertSee('Je bent al mee')
        ->assertDontSee('Je e-mailadres');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=NewsletterSignup`
Expected: FAIL (component class not found).

- [ ] **Step 3: Create the Livewire component class**

`app/Livewire/NewsletterSignup.php`:

```php
<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use App\Support\Location\Proximity;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

class NewsletterSignup extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public string $zip = '';

    public string $locationName = '';

    public bool $showGroupPicker = false;

    /** @var array<int, int> */
    public array $selectedGroups = [];

    public bool $submitted = false;

    public function mount(): void
    {
        $location = CurrentLocation::resolve();

        if ($location !== null) {
            $this->zip = $location['zip'];
            $this->locationName = $location['name'];
        }
    }

    public function toggleGroupPicker(): void
    {
        $this->showGroupPicker = ! $this->showGroupPicker;
    }

    public function subscribe(): void
    {
        $this->validate();

        // TODO(backend, Nico): persist the e-mail + chosen scope (zip /
        // selectedGroups) to the Email-subscription model and send the
        // double opt-in mail. Until that lands this is an optimistic,
        // non-persisting confirmation.

        $this->submitted = true;
    }

    /**
     * Visible groups, nearest first when we know where the visitor is.
     *
     * @return Collection<int, Group>
     */
    public function nearbyGroups(): Collection
    {
        $groups = Group::query()->visible()->whereNotNull('zip')->get();

        $location = CurrentLocation::resolve();

        if ($location === null) {
            return $groups->sortBy('name')->values();
        }

        $origin = ['lat' => $location['lat'], 'lng' => $location['lng']];

        $coords = PostalCode::query()
            ->whereIn('zip', $groups->pluck('zip')->filter()->unique()->all())
            ->get()
            ->keyBy('zip');

        return $groups
            ->map(function (Group $group) use ($coords, $origin): Group {
                $postalCode = $coords->get($group->zip);

                $group->setAttribute('distance_km', $postalCode
                    ? Proximity::distanceKm($origin, ['lat' => (float) $postalCode->latitude, 'lng' => (float) $postalCode->longitude])
                    : null);

                return $group;
            })
            ->sortBy(fn (Group $group): float => $group->distance_km ?? INF)
            ->take(8)
            ->values();
    }

    public function render()
    {
        return view('livewire.newsletter-signup', [
            'groups' => $this->showGroupPicker ? $this->nearbyGroups() : collect(),
        ]);
    }
}
```

- [ ] **Step 4: Create the component view**

`resources/views/livewire/newsletter-signup.blade.php`:

```blade
<div>
    @auth
        <div class="bg-kidical-light-blue rounded-card p-8 flex flex-col gap-4 items-start">
            <h2 class="text-kidical-ink">Je bent al mee</h2>
            <p class="text-kidical-ink/75">Je staat op de hoogte. Je nieuwsvoorkeuren beheer je in je profiel.</p>
            <x-cta-button variant="blue" :href="route('settings')">Beheer voorkeuren</x-cta-button>
        </div>
    @elseif ($submitted)
        <div class="flex flex-col gap-4 items-start">
            <h2>Kijk even in je mailbox</h2>
            <p>We stuurden een mailtje naar <strong>{{ $email }}</strong>. Klik op de link erin om je inschrijving te bevestigen.</p>
            <p class="text-kidical-ink/70">Niets ontvangen? Check je spam.</p>
        </div>
    @else
        <form wire:submit="subscribe" class="flex flex-col gap-6">
            <div class="flex flex-col gap-2">
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

            <div class="flex flex-col gap-2">
                <label for="newsletter-zip">Waar wil je op de hoogte blijven?</label>
                <input
                    id="newsletter-zip"
                    type="text"
                    wire:model="zip"
                    placeholder="Postcode of gemeente"
                    class="newsletter-signup__input"
                >
                @if ($locationName !== '')
                    <p class="text-kidical-ink/60">Ritten in en rond {{ $locationName }}.</p>
                @endif
            </div>

            <div class="flex flex-col gap-3">
                <button type="button" wire:click="toggleGroupPicker" class="newsletter-signup__reveal">
                    {{ $showGroupPicker ? 'Verberg groepen' : 'Of kies zelf groepen' }}
                </button>

                @if ($showGroupPicker)
                    <fieldset class="newsletter-signup__groups flex flex-col gap-2">
                        <legend class="sr-only">Kies groepen</legend>
                        @forelse ($groups as $group)
                            <label class="flex items-center gap-3">
                                <input type="checkbox" wire:model="selectedGroups" value="{{ $group->id }}">
                                <span>{{ $group->name }}</span>
                            </label>
                        @empty
                            <p class="text-kidical-ink/60">Nog geen groepen in de buurt gevonden.</p>
                        @endforelse
                    </fieldset>
                @endif
            </div>

            {{-- cta-button hardcodes type="button", so it cannot natively submit a
                 form; wire:click handles the click and the form's wire:submit
                 handles Enter. Both route to subscribe(). --}}
            <x-cta-button variant="blue" icon="arrow" wire:click="subscribe">Schrijf me in</x-cta-button>
        </form>
    @endauth
</div>
```

- [ ] **Step 5: Run tests**

Run: `php artisan test --compact --filter=NewsletterSignup`
Expected: PASS (all four).

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/NewsletterSignup.php resources/views/livewire/newsletter-signup.blade.php tests/Feature/NewsletterSignupTest.php
git commit -m "feat(newsletter): signup Livewire component with location prefill + group picker"
```

---

### Task 4: Signup page view (`newsletter.show`) + styling

The page itself: chip hero, two-column layout (form + reassurance aside), embedding the Livewire component. Plus the page CSS partial for the input/reveal/benefits appearance.

**Files:**
- Modify: `routes/web.php` (one route)
- Create: `resources/views/nieuwsbrief.blade.php`
- Create: `resources/css/pages/nieuwsbrief.css`
- Modify: `resources/css/app.css` (add one `@import`)
- Modify: `tests/Feature/PublicPagesRenderTest.php` (add URI)
- Modify: `tests/Feature/NewsletterPageTest.php` (add content + prefill tests)

**Interfaces:**
- Consumes: `<livewire:newsletter-signup />` (Task 3), `<x-envelope-chips />` (Task 1).
- Produces: route `newsletter.show` at `/{locale}/nieuwsbrief`.

- [ ] **Step 1: Write the failing tests**

Append to `tests/Feature/NewsletterPageTest.php`:

```php
use function Pest\Laravel\withCookie;

it('shows the signup page with hero, form and reassurance', function () {
    get('/nl/nieuwsbrief')
        ->assertOk()
        ->assertSee('Elke maand de nieuwste ritten in je bus')
        ->assertSee('Je e-mailadres')
        ->assertSee('Geen spam, beloofd');
});

it('pre-fills the saved location on the signup page', function () {
    withCookie('kcm_location', json_encode([
        'zip' => '1030', 'lat' => 50.8669, 'lng' => 4.3733, 'name' => 'Schaarbeek',
    ]))
        ->get('/nl/nieuwsbrief')
        ->assertOk()
        ->assertSee('Schaarbeek');
});
```

(Add `use function Pest\Laravel\withCookie;` at the top alongside the existing `get` import.)

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=NewsletterPage`
Expected: FAIL (the two new tests 404 / don't see content).

- [ ] **Step 3: Add the route**

In `routes/web.php`, next to the `newsletter.confirmed` line from Task 2:

```php
Route::view('nieuwsbrief', 'nieuwsbrief')->name('newsletter.show');
```

(Order does not matter against `bevestigd` — both are literal segments, no wildcard conflict.)

- [ ] **Step 4: Create the page view**

`resources/views/nieuwsbrief.blade.php`:

```blade
<x-layouts::site title="Nieuwsbrief">
    <section class="flex flex-col items-center gap-6 text-center max-w-3xl mx-auto pb-12">
        <x-envelope-chips />

        <h1>Elke maand de nieuwste ritten in je bus</h1>

        <p class="max-w-xl">
            Eén mail per maand met de ritten bij jou in de buurt. Zo weet je als
            eerste waar en wanneer er gefietst wordt.
        </p>
    </section>

    <div class="grid gap-10 md:grid-cols-[1.6fr_1fr] items-start max-w-4xl mx-auto pb-16">
        <div>
            <livewire:newsletter-signup />
        </div>

        <aside>
            <ul class="newsletter-signup-benefits">
                <li>Eén mail per maand</li>
                <li>Alleen ritten bij jou in de buurt</li>
                <li>Geen spam, beloofd</li>
                <li>Uitschrijven met één klik</li>
            </ul>
        </aside>
    </div>
</x-layouts::site>
```

- [ ] **Step 5: Create the page CSS partial**

`resources/css/pages/nieuwsbrief.css`:

```css
@layer components {
    .newsletter-signup__input {
        width: 100%;
        padding: 0.85rem 1.1rem;
        border: 2px solid color-mix(in oklab, var(--color-kidical-ink), transparent 85%);
        border-radius: 9999px;
        background: white;
        color: var(--color-kidical-ink);
    }

    .newsletter-signup__input:focus {
        outline: none;
        border-color: var(--color-kidical-blue);
    }

    .newsletter-signup__error {
        color: var(--color-kidical-red);
    }

    .newsletter-signup__reveal {
        align-self: flex-start;
        color: var(--color-kidical-blue);
        text-decoration: underline;
        text-underline-offset: 0.2em;
    }

    .newsletter-signup-benefits {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .newsletter-signup-benefits li {
        position: relative;
        padding-left: 1.75rem;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 20%);
    }

    .newsletter-signup-benefits li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: var(--color-kidical-green);
        font-weight: 700;
    }
}
```

- [ ] **Step 6: Register the partial in `app.css`**

In `resources/css/app.css`, alongside the other `pages/` imports:

```css
@import './pages/nieuwsbrief.css';
```

- [ ] **Step 7: Add the page to the smoke dataset**

In `tests/Feature/PublicPagesRenderTest.php`, add `'/nl/nieuwsbrief'` to the `->with([...])` URI list.

- [ ] **Step 8: Run tests**

Run: `php artisan test --compact --filter="NewsletterPage|PublicPagesRender|CssArchitecture"`
Expected: PASS.

- [ ] **Step 9: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add routes/web.php resources/views/nieuwsbrief.blade.php resources/css/pages/nieuwsbrief.css resources/css/app.css tests/Feature/NewsletterPageTest.php tests/Feature/PublicPagesRenderTest.php
git commit -m "feat(newsletter): signup page with chip hero + two-column layout"
```

---

### Task 5: Wire up the teaser CTAs + reconcile their tests

Point the three existing teaser CTAs at `route('newsletter.show')` and bring the two stale test files green. **Read the dirty-tree note in Global Constraints first** and confirm you may touch these in-progress files.

**Files:**
- Modify: `resources/views/components/newsletter-cta.blade.php`
- Modify: `resources/views/components/newsletter-optin.blade.php`
- Modify: `tests/Feature/NewsletterCtaTest.php`
- Modify: `tests/Feature/NewsletterOptinTest.php`

- [ ] **Step 1: Confirm the locale-route idiom + default locale**

These components are unit-tested with `Blade::render(...)` (no middleware), so a bare `route('newsletter.show')` would throw "missing locale". Pass the locale explicitly. Confirm the default locale is supported:

Run: `php artisan config:show app.locale` and check `app/Http/Middleware/SetLocale.php` (or wherever `SetLocale::SUPPORTED` is defined) — verify the default (`nl`) is in `SUPPORTED`.

Use the form `route('newsletter.show', ['locale' => app()->getLocale()])` in both components.

- [ ] **Step 2: Point the home band CTA at the page**

In `resources/views/components/newsletter-cta.blade.php`, replace:

```blade
        {{-- TODO: point at the dedicated newsletter sign-up page once it exists. --}}
        <x-cta-button href="#" variant="blue" icon="arrow">Schrijf me in</x-cta-button>
```

with:

```blade
        <x-cta-button :href="route('newsletter.show', ['locale' => app()->getLocale()])" variant="blue" icon="arrow">Schrijf me in</x-cta-button>
```

- [ ] **Step 3: Point the opt-in teaser CTA at the page**

In `resources/views/components/newsletter-optin.blade.php`, in the `@else` (guest) branch, replace:

```blade
            {{-- TODO: point at the dedicated sign-up page once it exists (placeholder #). --}}
            <x-cta-button variant="secondary" href="#" class="shrink-0">Schrijf je in</x-cta-button>
```

with:

```blade
            <x-cta-button variant="secondary" :href="route('newsletter.show', ['locale' => app()->getLocale()])" class="shrink-0">Schrijf je in</x-cta-button>
```

- [ ] **Step 4: Reconcile `NewsletterCtaTest.php` to the teaser markup**

Replace the whole file with assertions matching the current working-tree teaser (default heading `Krijg de nieuwste ritten in je mailbox`, chips, CTA linking to the page, no inline form):

```php
<?php

use Illuminate\Support\Facades\Blade;

it('renders the teaser on a yellow band with default copy and a CTA to the signup page', function () {
    $html = Blade::render('<x-newsletter-cta />');

    expect($html)
        ->toContain('Krijg de nieuwste ritten in je mailbox')
        ->toContain('bg-kidical-yellow')
        ->toContain('Schrijf me in')
        ->toContain('nieuwsbrief')
        ->not->toContain('href="#"');
});

it('renders a three-chip fleet straddling the seam, one per brand colour', function () {
    $html = Blade::render('<x-newsletter-cta />');

    expect($html)
        ->toContain('newsletter-cta__chip--green')
        ->toContain('newsletter-cta__chip--red')
        ->toContain('newsletter-cta__chip--blue');

    expect(substr_count($html, 'newsletter-cta__chip newsletter-cta__chip--'))->toBe(3);
});

it('arms the scroll-into-view reveal so the chips animate in', function () {
    expect(Blade::render('<x-newsletter-cta />'))
        ->toContain('is-ready')
        ->toContain('is-inview')
        ->toContain('IntersectionObserver');
});

it('lets the heading and lead be overridden', function () {
    $html = Blade::render('<x-newsletter-cta heading="Blijf op de hoogte" lead="Eén mail per maand." />');

    expect($html)
        ->toContain('Blijf op de hoogte')
        ->toContain('Eén mail per maand.');
});
```

- [ ] **Step 5: Reconcile `NewsletterOptinTest.php` to the teaser markup**

Replace the whole file (guest teaser = `Mis geen rit` + CTA to page; group localises the lead; auth = manage-preferences; the calendar/chapter route tests assert the teaser copy):

```php
<?php

use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Blade;

use function Pest\Laravel\get;

test('guest sees the teaser with a link to the signup page', function () {
    $html = Blade::render('<x-newsletter-optin />');

    expect($html)
        ->toContain('Mis geen rit')
        ->toContain('Eén mail per maand met de ritten bij jou in de buurt.')
        ->toContain('Schrijf je in')
        ->toContain('nieuwsbrief')
        ->not->toContain('href="#"');
});

test('group prop localises the teaser with the gemeente name', function () {
    $group = Group::create([
        'shortname' => 'sb',
        'name' => 'Kidical Mass Schaarbeek',
        'invisible' => false,
        'started_at' => now(),
    ]);

    $html = Blade::render('<x-newsletter-optin :group="$group" />', ['group' => $group]);

    expect($html)->toContain('Eén mail per maand met de ritten en het nieuws uit Schaarbeek.');
});

test('authenticated visitor sees a manage-preferences panel and no signup CTA', function () {
    $this->actingAs(User::factory()->create());

    $html = Blade::render('<x-newsletter-optin />');

    expect($html)
        ->toContain('Beheer voorkeuren')
        ->toContain(route('settings'))
        ->not->toContain('Schrijf je in');
});

test('the calendar page shows the opt-in teaser in the sidebar', function () {
    get('/nl/events')
        ->assertOk()
        ->assertSee('Mis geen rit');
});

test('chapter page shows the localised opt-in teaser, with and without a ride', function () {
    $author = User::factory()->create();
    $group = Group::create([
        'shortname' => 'sb',
        'name' => 'Kidical Mass Schaarbeek',
        'zip' => '1030',
        'invisible' => false,
        'started_at' => now(),
    ]);

    get(route('groups.show', ['locale' => 'nl', 'group' => $group]))
        ->assertOk()
        ->assertSee('Nog geen fietstocht gepland')
        ->assertSee('Mis geen rit')
        ->assertSee('uit Schaarbeek', escape: false);

    Activity::create([
        'title_nl' => 'Kidical Mass Schaarbeek', 'title_fr' => 'x',
        'content_nl' => 'x', 'content_fr' => 'x',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addWeek(), 'duration_minutes' => 60,
        'location' => 'Place Colignon', 'author_id' => $author->id,
    ])->groups()->attach($group);

    get(route('groups.show', ['locale' => 'nl', 'group' => $group]))
        ->assertOk()
        ->assertDontSee('Nog geen fietstocht gepland')
        ->assertSee('Mis geen rit')
        ->assertSee('uit Schaarbeek', escape: false);
});
```

> If the calendar route is `/nl/events` vs another slug, match the existing dataset in `PublicPagesRenderTest`. Adjust the `assertSee` "no ride" / "has ride" strings only if the chapter page uses different copy — verify against `resources/views/groups/show.blade.php`.

- [ ] **Step 6: Run the full affected suite**

Run: `php artisan test --compact --filter="Newsletter|EnvelopeChips|PublicPages|CssArchitecture"`
Expected: PASS (everything green).

- [ ] **Step 7: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/newsletter-cta.blade.php resources/views/components/newsletter-optin.blade.php tests/Feature/NewsletterCtaTest.php tests/Feature/NewsletterOptinTest.php
git commit -m "feat(newsletter): wire teaser CTAs to the signup page + reconcile tests"
```

---

## Post-build (not code)

- **Verify in the browser:** load `/nl/nieuwsbrief` (form, reveal, submit → "check je inbox"), `/nl/nieuwsbrief/bevestigd`, and the teaser CTAs on `/nl` + a chapter page. Set a location via the picker and confirm the gemeente pre-fills.
- **Page registry + log:** add a `P-nn` row for the newsletter page in `docs/wiki/design/30-skeleton/00-page-registry.md` and a `## [2026-06-17] build | …` entry in `docs/wiki/log.md` (run `/pipeline`). Mark `Back` as 🟠 placeholder (no subscription backend yet — Nico owns the model).

## Self-Review

- **Spec coverage:** focused page ✓ (Task 4), smart "near me" default + location pre-fill ✓ (Task 3 mount / Task 4 prefill test), optional nearby-group reveal ✓ (Task 3), full three-state journey ✓ (form + check-inbox in Task 3, confirmed in Task 2), home band + teasers link here ✓ (Task 5), backend stubbed ✓ (`subscribe()` seam), chip hero motif ✓ (Task 1), reassurance trio ✓ (Task 4 aside), auth short-circuit ✓ (Task 3).
- **Type consistency:** `subscribe()`/`toggleGroupPicker()`/`nearbyGroups()` and props `email`/`zip`/`locationName`/`showGroupPicker`/`selectedGroups`/`submitted` are used identically across the class, view, and tests. `route('newsletter.show')` / `route('newsletter.confirmed')` names match the routes.
- **Known seam (intentional, not a placeholder):** `subscribe()` does not persist — explicitly the agreed scope, marked `TODO(backend, Nico)`.
