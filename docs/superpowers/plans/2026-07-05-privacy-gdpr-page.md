# Privacy / GDPR Page (P-06) + Compliance Interventions Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the P-06 privacy stub with a complete, honest Dutch privacy & cookies page, and close the GDPR gaps around it: privacy notices on every data-collecting form, enforced retention for enquiry submissions, a privacy-enhanced YouTube embed (keeps the site banner-free), a fixed enquiry-mail config key, and public registration disabled per the documented invite-only decision.

**Architecture:** The privacy page is a static `Route::view` content page (route, footer link and `/cookies` 301 already exist) built with the standard `<x-layouts::site>` + `<x-page-hero size="compact">` pattern and inline Dutch copy, like every other content page. Form notices are one small reusable Blade component dropped under each submit button. Retention is Eloquent `MassPrunable` on `ContactForm` plus a daily `model:prune` schedule.

**Tech Stack:** Laravel 13, Livewire 4, Pest 4, Tailwind 4 (token-backed utilities + CSS partials).

## Context (read before starting)

- Site is NL-only, served at `/{locale}/...` (locale = `nl`). Route `privacy` → `/nl/privacy` already exists (`routes/web.php:145`); `/nl/cookies` 301s to it (`:146`); the footer already links it (`resources/views/layouts/site/footer.blade.php:78`).
- Personal data collected today: 3 live enquiry forms (`PartnerEnquiry`, `StartGroupEnquiry`, `ChapterVolunteerSignup`) + 1 built-but-unembedded (`ContactFormComponent`), all writing to the `contact_forms` table and mailing `config('kidicalmass.mail.communications')`. The newsletter form (`NewsletterSignup`) validates but has no backend yet (Nico's TODO; double opt-in required).
- Cookies set by the app: session cookie (`config('session.cookie')`, 120 min) + `XSRF-TOKEN`; `kcm_location` (365 days, user-chosen postcode/lat/lng, never stored server-side); `roze_welcome_{groupId}` (90 days, logged-in volunteers only). No tracking cookies, no analytics yet (Fathom/Plausible decided but not implemented). The one cookie-consent exposure is the **standard YouTube embed on home** (`resources/views/home.blade.php:14`), fixed in Task 5.
- No legal entity (vzw) is confirmed anywhere; public identity is "Kidical Mass België", contact `config('kidicalmass.contact.email')` = `bike@kidicalmass.be`. Entity + processor names are **client questions**, tracked as registry gaps in Task 7, not blockers.

## Global Constraints

- **Tone of voice** (`docs/tone-of-voice.md`): warm, local, committed; privacy page sits at the "notch more serious" register (like partner pages). **NEVER use em-dashes (—) in site copy.** All copy below is pre-written; do not paraphrase it.
- **Testing** (`docs/testing-conventions.md`): assert rendered text / `href` / `data-*` seams, never Tailwind utilities or long copy sentences. Do NOT delete tests without approval.
- **CSS architecture:** page templates carry composition utilities only (`grid`, `flex`, `gap-*`, `max-w-*`, `p-*`…); component appearance = token-backed utilities inside the component blade; page-scoped CSS goes in `resources/css/pages/<page>.css`, registered in the `@import` block of `resources/css/app.css` (enforced by `tests/Feature/CssArchitectureTest.php`). No raw hex/px.
- Headings are raw `<h1>`–`<h6>` (never `flux:heading`); dates use `<time datetime="ISO8601">`; `<dl><dt><dd>` for key-value metadata.
- Run `vendor/bin/pint --dirty --format agent` after every PHP edit, before each commit.
- **Shared checkout with Nico:** stage by explicit path (never `git add -A`), work directly on `main`, never push `main`.
- Run tests with `php artisan test --compact --filter=<Name>`.
- Known flake: `CalendarProximityTest` is order-dependent in the full suite; not a regression signal.

---

### Task 1: Privacy & cookies page (the legal text)

**Files:**
- Modify: `resources/views/privacy.blade.php` (replace stub entirely)
- Modify: `lang/nl/meta.php` (add `privacy` key)
- Create: `resources/css/pages/privacy.css`
- Modify: `resources/css/app.css` (register the partial in the `@import` block, ~line 240 next to the other `./pages/*` imports)
- Modify: `tests/Pest.php:85` (drop `/nl/privacy` from `$stubRoutes`)
- Create: `tests/Feature/PrivacyPageTest.php`

**Interfaces:**
- Consumes: `route('privacy')`, `config('kidicalmass.contact.email')`, `config('session.cookie')`, `config('location.cookie')`, `<x-layouts::site>`, `<x-page-hero>`.
- Produces: the live `/nl/privacy` page that Tasks 2 and 7 reference. No PHP symbols.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/PrivacyPageTest.php`:

```php
<?php

use function Pest\Laravel\get;

it('serves the privacy page with the GDPR essentials', function () {
    get('/nl/privacy')
        ->assertOk()
        // controller identity + contact route for data-subject requests
        ->assertSee(config('kidicalmass.contact.email'))
        // right to complain to the Belgian supervisory authority
        ->assertSee('gegevensbeschermingsautoriteit.be')
        // first-party cookies are named
        ->assertSee(config('session.cookie'))
        ->assertSee(config('location.cookie'))
        // retention promise is stated
        ->assertSee('12 maanden');
});

it('explains why there is no cookie banner', function () {
    get('/nl/privacy')
        ->assertOk()
        ->assertSee('cookiebanner');
});
```

- [ ] **Step 2: Run it to verify it fails**

Run: `php artisan test --compact --filter=PrivacyPageTest`
Expected: FAIL (the stub contains none of these strings except possibly none; both tests red).

- [ ] **Step 3: Add the meta description key**

In `lang/nl/meta.php`, after the `'newsletter' => ...` line, add:

```php
    'privacy' => 'Hoe Kidical Mass België omgaat met jouw gegevens: wat we verzamelen, waarom, hoe lang we het bewaren en welke cookies we gebruiken.',
```

- [ ] **Step 4: Replace the stub view**

Replace the full contents of `resources/views/privacy.blade.php` with the code below. Before saving, replace `2026-07-05` / `5 juli 2026` in the closing section with the actual date you execute this task. Do not reword the copy; it is written to satisfy GDPR Art. 13 (controller, purposes, legal bases, recipients, retention, rights, supervisory authority, no profiling) while staying in voice.

```blade
{{--
    Privacy & cookies (P-06). One page for both (route `cookies` 301s here).
    Copy is the legal text: GDPR Art. 13 items in tone-of-voice register
    "a notch more serious". Contact email + cookie names come from config so
    the page can never drift from reality. When cookieless analytics
    (Fathom/Plausible) goes live, add a line to the cookies section.
--}}
<x-layouts::site title="Privacy & cookies" :description="__('meta.privacy')">

    <x-page-hero
        eyebrow="Praktisch"
        title="Privacy & cookies"
        size="compact">
        <x-slot:lead>
            <p>Kort en eerlijk: dit doen we met jouw gegevens, en dit doen we er niet mee.</p>
        </x-slot:lead>

        <div class="privacy-page max-w-3xl mx-auto flex flex-col gap-12 py-12">

            <section class="flex flex-col gap-4">
                <h2>Wie we zijn</h2>
                <p>Kidical Mass België organiseert vrolijke fietsparades voor kinderen, overal in het land. Wij zijn verantwoordelijk voor de persoonsgegevens die je via deze website met ons deelt. Heb je een vraag over je gegevens? Mail ons op <a href="mailto:{{ config('kidicalmass.contact.email') }}">{{ config('kidicalmass.contact.email') }}</a>.</p>
            </section>

            <section class="flex flex-col gap-6">
                <h2>Welke gegevens we gebruiken, en waarom</h2>
                <p>We verzamelen enkel wat we nodig hebben, en enkel wanneer jij het ons geeft. Je bent nooit verplicht om iets te delen, al kunnen we je zonder e-mailadres natuurlijk niet antwoorden.</p>

                <div class="flex flex-col gap-2">
                    <h3>Als je ons een vraag stuurt</h3>
                    <p>Via de formulieren op deze site (meefietsen als vrijwilliger, een groep starten, partner worden of gewoon een vraag) delen we je naam, e-mailadres, eventueel je telefoonnummer en je bericht. Die gebruiken we alleen om je vraag te beantwoorden en op te volgen. Ze komen terecht bij het kernteam en, als je vraag over een lokale groep gaat, bij het team van die groep. Rechtsgrond: ons gerechtvaardigd belang om te antwoorden wanneer jij ons iets vraagt.</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3>Als je de nieuwsbrief volgt</h3>
                    <p>Schrijf je je in, dan bewaren we je e-mailadres en de groepen die je wil volgen. Je krijgt eerst een bevestigingsmail; pas als je daarin klikt, sta je op de lijst. Uitschrijven kan altijd via de link onderaan elke mail. Rechtsgrond: jouw toestemming, die je dus ook altijd weer kan intrekken.</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3>Als je een locatie kiest</h3>
                    <p>Kies je op de kalender of bij de lokale groepen een gemeente, dan onthouden we die keuze in een cookie op jouw toestel. Die locatie verlaat je browser niet: wij slaan ze niet op onze servers op.</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3>Als je vrijwilliger bent</h3>
                    <p>Vrijwilligers krijgen op uitnodiging een account met naam en e-mailadres, zolang ze actief zijn bij hun groep. Er is geen publieke registratie.</p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3>Technische gegevens</h3>
                    <p>Zoals elke website verwerken we kort wat technische gegevens (zoals je IP-adres en browsertype) in sessies en logbestanden, om de site veilig en werkend te houden. We doen niet aan profilering en nemen geen geautomatiseerde beslissingen over jou.</p>
                </div>
            </section>

            <section class="flex flex-col gap-4">
                <h2>Met wie we gegevens delen</h2>
                <p>Met zo weinig mogelijk mensen. Onze hostingprovider en e-maildienst verwerken gegevens in onze opdracht. Lokale teams zien enkel de aanvragen voor hun eigen groep. We verkopen of verhuren je gegevens nooit, aan niemand.</p>
            </section>

            <section class="flex flex-col gap-4">
                <h2>Hoe lang we gegevens bewaren</h2>
                <p>Formulierinzendingen verwijderen we uiterlijk 12 maanden nadat je vraag is afgehandeld, en sowieso uiterlijk 24 maanden na ontvangst. Je nieuwsbriefgegevens bewaren we tot je je uitschrijft. Vrijwilligersaccounts verwijderen we wanneer iemand stopt.</p>
            </section>

            <section class="flex flex-col gap-4">
                <h2>Jouw rechten</h2>
                <p>Je mag altijd weten welke gegevens we van jou hebben. Je kan ze laten verbeteren of verwijderen, de verwerking laten beperken, bezwaar maken of je toestemming intrekken. Eén mailtje naar <a href="mailto:{{ config('kidicalmass.contact.email') }}">{{ config('kidicalmass.contact.email') }}</a> volstaat; we antwoorden binnen de 30 dagen.</p>
                <p>Kom je er met ons niet uit, dan kan je terecht bij de Gegevensbeschermingsautoriteit via <a href="https://www.gegevensbeschermingsautoriteit.be" rel="noopener">gegevensbeschermingsautoriteit.be</a>.</p>
            </section>

            <section class="flex flex-col gap-4">
                <h2>Foto's op onze ritten</h2>
                <p>Op onze ritten worden foto's en video's gemaakt. Die gebruiken we om te tonen hoe leuk samen fietsen is: op deze site, in de nieuwsbrief en op sociale media. We gaan daar zorgvuldig mee om, zeker met beelden van kinderen. Sta jij of je kind herkenbaar op een foto die je liever niet online ziet? Mail ons en we halen de foto weg.</p>
            </section>

            <section class="privacy-cookies flex flex-col gap-6">
                <h2>Cookies</h2>
                <p>Deze site gebruikt geen tracking- of advertentiecookies. Daarom zie je hier ook geen cookiebanner. De cookies die we wel zetten, doen gewoon hun werk:</p>

                <dl class="flex flex-col gap-5">
                    <div class="flex flex-col gap-1">
                        <dt><code>{{ config('session.cookie') }}</code> en <code>XSRF-TOKEN</code></dt>
                        <dd>Houden je bezoek en de formulieren veilig aan de praat. Verdwijnen na 2 uur.</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt><code>{{ config('location.cookie') }}</code></dt>
                        <dd>Onthoudt de gemeente die je zelf koos bij de kalender of de lokale groepen. Blijft 1 jaar, en enkel op jouw toestel.</dd>
                    </div>
                    <div class="flex flex-col gap-1">
                        <dt><code>roze_welcome_*</code></dt>
                        <dd>Toont ingelogde vrijwilligers eenmalig een welkomstblok. Blijft 90 dagen.</dd>
                    </div>
                </dl>

                <p>Enkele externe diensten maken de site mee mogelijk. De video op de homepage laden we via youtube-nocookie.com, de privacyvriendelijke variant zonder trackingcookies. De kaarten tonen OpenStreetMap-tegels via CARTO, en de lettertypes komen van Bunny Fonts, dat geen cookies zet. Wanneer je browser die beelden ophaalt, ziet die dienst je IP-adres. Meer laten we niet door.</p>
                {{-- Analytics: als Fathom/Plausible live gaat, voeg hier één eerlijke zin toe. --}}
            </section>

            <section class="flex flex-col gap-4">
                <h2>Vragen of wijzigingen</h2>
                <p>Verandert er iets aan hoe we met gegevens omgaan, dan passen we deze pagina aan. Laatst bijgewerkt op <time datetime="2026-07-05">5 juli 2026</time>.</p>
            </section>

        </div>

    </x-page-hero>
</x-layouts::site>
```

- [ ] **Step 5: Add the page CSS partial**

Create `resources/css/pages/privacy.css` (match the `@layer` conventions of an existing sibling, e.g. `resources/css/pages/about.css`, before saving):

```css
/* Privacy & cookies (P-06): emphasis for the cookie list terms. */
@layer components {
    .privacy-cookies dt {
        font-weight: 600;
    }
}
```

Register it in `resources/css/app.css` inside the `@import './pages/...'` block (around line 240, keep the block's existing ordering style):

```css
@import './pages/privacy.css';
```

- [ ] **Step 6: Un-stub the route in the test datasets**

In `tests/Pest.php:85`, change:

```php
$stubRoutes = ['/nl/contact', '/nl/privacy'];
```

to:

```php
$stubRoutes = ['/nl/contact'];
```

This puts `/nl/privacy` under the finished-page honesty guard (`PublicStructureTest` asserts no `Stub`/`lorem`) and the tone checks.

- [ ] **Step 7: Run the tests**

Run: `npm run build` (new CSS partial must land in the Vite manifest), then:
`php artisan test --compact --filter=PrivacyPageTest`
Expected: PASS (2 tests).
Then: `php artisan test --compact --filter=PublicStructureTest` and `php artisan test --compact --filter=SiteHeadTest` and `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS (privacy now passes the finished-page guard; head has a description; partial is registered).

- [ ] **Step 8: Visual check**

Load `https://kidicalmass.test/nl/privacy` (Herd serves it; use the Boost `get-absolute-url` tool if unsure of the host) and confirm: compact blue hero, readable single column, cookie list legible, no unstyled artifacts. Do one screenshot pass only if something looks off.

- [ ] **Step 9: Commit**

```bash
git add resources/views/privacy.blade.php lang/nl/meta.php resources/css/pages/privacy.css resources/css/app.css tests/Pest.php tests/Feature/PrivacyPageTest.php
git commit -m "feat(privacy): real privacy & cookies page replaces the P-06 stub

- GDPR Art. 13 copy in NL: purposes, legal bases, recipients, retention, rights, GBA
- cookie inventory driven by config (session, kcm_location, roze_welcome_*)
- privacy route joins the finished-page guard datasets

Why: forms already collect names/emails; the page is GDPR-mandatory before launch.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 2: Privacy note under every data-collecting form

**Files:**
- Create: `resources/views/components/form-privacy-note.blade.php`
- Modify: `resources/views/livewire/partner-enquiry.blade.php` (after the submit `</x-cta-button>`, ~line 65)
- Modify: `resources/views/livewire/start-group-enquiry.blade.php` (after `</x-cta-button>`, ~line 70)
- Modify: `resources/views/livewire/chapter-volunteer-signup.blade.php` (after `</x-cta-button>`, ~line 83)
- Modify: `resources/views/livewire/contact-form-component.blade.php` (after the submit button, ~line 27)
- Modify: `resources/views/livewire/newsletter-signup.blade.php` (after `</x-cta-button>` inside the form, ~line 90)
- Create: `tests/Feature/FormPrivacyNoteTest.php`

**Interfaces:**
- Consumes: `route('privacy', ['locale' => app()->getLocale()])` (explicit locale param: these views render inside Livewire component tests where URL defaults from the `setlocale` middleware are absent, so a bare `route('privacy')` would throw).
- Produces: `<x-form-privacy-note>slot sentence</x-form-privacy-note>` used by all five forms; the slot is the purpose sentence, the component appends the privacy-page link.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/FormPrivacyNoteTest.php`:

```php
<?php

use App\Livewire\ChapterVolunteerSignup;
use App\Livewire\ContactFormComponent;
use App\Livewire\NewsletterSignup;
use App\Livewire\PartnerEnquiry;
use App\Livewire\StartGroupEnquiry;
use App\Models\Group;
use Livewire\Livewire;

it('shows a privacy note linking the privacy page on every enquiry form', function (string $component) {
    Livewire::test($component)
        ->assertSee('privacyverklaring')
        ->assertSee(route('privacy', ['locale' => app()->getLocale()]), false);
})->with([
    'contact' => ContactFormComponent::class,
    'partner' => PartnerEnquiry::class,
    'start a group' => StartGroupEnquiry::class,
    'newsletter' => NewsletterSignup::class,
]);

it('shows a privacy note on the chapter volunteer form', function () {
    Livewire::test(ChapterVolunteerSignup::class, ['group' => Group::factory()->create()])
        ->assertSee('privacyverklaring')
        ->assertSee(route('privacy', ['locale' => app()->getLocale()]), false);
});
```

Note: check `tests/Feature/ChapterVolunteerSignupTest.php` for how that component is actually mounted (parameter name/type for the group) and mirror it exactly; adjust the second test if it differs.

- [ ] **Step 2: Run it to verify it fails**

Run: `php artisan test --compact --filter=FormPrivacyNoteTest`
Expected: FAIL on `privacyverklaring` for all five.

- [ ] **Step 3: Create the component**

Create `resources/views/components/form-privacy-note.blade.php`. Appearance is token-backed utilities baked into the component (per the styling architecture); the slot carries the per-form purpose sentence:

```blade
{{-- One-line GDPR notice under a form's submit button: the purpose sentence
     comes in via the slot, the privacy-page link is appended. Explicit locale
     param so the view also renders outside a routed request (Livewire tests). --}}
<p {{ $attributes->merge(['class' => 'text-sm text-kidical-ink/60']) }}>
    {{ $slot }}
    <a href="{{ route('privacy', ['locale' => app()->getLocale()]) }}" class="underline">Meer weten? Lees onze privacyverklaring.</a>
</p>
```

Before saving, check a sibling component (e.g. `resources/views/components/intro-text.blade.php`) for the muted-text token actually in use; `text-kidical-ink/60` matches the pattern seen in `newsletter-signup.blade.php` (`text-kidical-ink/70`, `/75`). Use the project's token, never a raw value.

- [ ] **Step 4: Drop the note into all five forms**

Directly after the submit button block of each view, add (purpose sentence varies per form):

`resources/views/livewire/contact-form-component.blade.php` (after the submit button, inside the form):
```blade
<x-form-privacy-note>We gebruiken je gegevens alleen om je bericht te beantwoorden.</x-form-privacy-note>
```

`resources/views/livewire/partner-enquiry.blade.php` (after `</x-cta-button>` at ~line 65):
```blade
<x-form-privacy-note>We gebruiken je gegevens alleen om je voorstel te beantwoorden.</x-form-privacy-note>
```

`resources/views/livewire/start-group-enquiry.blade.php` (after `</x-cta-button>` at ~line 70):
```blade
<x-form-privacy-note>We gebruiken je gegevens alleen om samen jouw groep op te starten.</x-form-privacy-note>
```

`resources/views/livewire/chapter-volunteer-signup.blade.php` (after `</x-cta-button>` at ~line 83):
```blade
<x-form-privacy-note>We gebruiken je gegevens alleen om je aanmelding op te volgen.</x-form-privacy-note>
```

`resources/views/livewire/newsletter-signup.blade.php` (after `</x-cta-button>` at ~line 90, still inside the `<form>`):
```blade
<x-form-privacy-note>Je e-mailadres gebruiken we alleen voor de nieuwsbrief. Uitschrijven kan altijd met één klik.</x-form-privacy-note>
```

- [ ] **Step 5: Run the tests**

Run: `php artisan test --compact --filter=FormPrivacyNoteTest`
Expected: PASS (5 scenarios).
Then run the existing form suites to prove nothing broke:
`php artisan test --compact --filter="PartnerEnquiryTest|StartGroupEnquiryTest|ChapterVolunteerSignupTest"`
Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add resources/views/components/form-privacy-note.blade.php resources/views/livewire/partner-enquiry.blade.php resources/views/livewire/start-group-enquiry.blade.php resources/views/livewire/chapter-volunteer-signup.blade.php resources/views/livewire/contact-form-component.blade.php resources/views/livewire/newsletter-signup.blade.php tests/Feature/FormPrivacyNoteTest.php
git commit -m "feat(privacy): purpose note + privacy link under every data-collecting form

Why: GDPR Art. 13 requires informing people at the moment of collection.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 3: Enforce the retention promise (prune old enquiries)

The privacy page (Task 1) now promises: deleted 12 months after handling, 24 months max. Make that true.

**Files:**
- Modify: `app/Models/ContactForm.php`
- Modify: `routes/console.php`
- Create: `tests/Feature/ContactFormRetentionTest.php`

**Interfaces:**
- Consumes: `ContactForm` model (`handled_at` datetime cast, `LocalGroupScope` global scope), `ContactFormFactory` (exists in `database/factories/`).
- Produces: `ContactForm::prunable(): Builder` and a scheduled daily `model:prune`.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/ContactFormRetentionTest.php`:

```php
<?php

use App\Models\ContactForm;

it('prunes enquiries 12 months after handling and 24 months after receipt', function () {
    $handledOld = ContactForm::factory()->create(['handled_at' => now()->subMonths(13)]);
    $handledRecent = ContactForm::factory()->create(['handled_at' => now()->subMonths(2)]);
    $staleUnhandled = ContactForm::factory()->create(['created_at' => now()->subMonths(25)]);
    $freshUnhandled = ContactForm::factory()->create();

    $this->artisan('model:prune', ['--model' => ContactForm::class]);

    $remaining = ContactForm::withoutGlobalScopes()->pluck('id');

    expect($remaining)
        ->toContain($handledRecent->id)
        ->toContain($freshUnhandled->id)
        ->not->toContain($handledOld->id)
        ->not->toContain($staleUnhandled->id);
});
```

- [ ] **Step 2: Run it to verify it fails**

Run: `php artisan test --compact --filter=ContactFormRetentionTest`
Expected: FAIL (`model:prune` prunes nothing; old records remain).

- [ ] **Step 3: Make ContactForm mass-prunable**

In `app/Models/ContactForm.php`, add the trait and the prunable query. The query MUST bypass `LocalGroupScope`, otherwise the console prune run sees a scoped (possibly empty) table:

```php
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
```

```php
    use HasFactory, MassPrunable;

    /**
     * Retention promised on /privacy: gone 12 months after handling,
     * 24 months after receipt at the latest.
     */
    public function prunable(): Builder
    {
        return static::withoutGlobalScope(LocalGroupScope::class)
            ->where(function (Builder $query) {
                $query->where('handled_at', '<', now()->subMonths(12))
                    ->orWhere(function (Builder $query) {
                        $query->whereNull('handled_at')
                            ->where('created_at', '<', now()->subMonths(24));
                    });
            });
    }
```

- [ ] **Step 4: Schedule the prune**

In `routes/console.php`, add:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune')->daily();
```

- [ ] **Step 5: Run the tests**

Run: `php artisan test --compact --filter=ContactFormRetentionTest`
Expected: PASS.
Then `php artisan schedule:list` and confirm `model:prune` appears daily.
Also run `php artisan test --compact --filter=ConvertContactFormToUserTest` (nearest neighbour of the model) to confirm no regression.

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Models/ContactForm.php routes/console.php tests/Feature/ContactFormRetentionTest.php
git commit -m "feat(privacy): prune enquiry submissions per the published retention policy

- ContactForm is MassPrunable: handled > 12 months or unhandled > 24 months
- daily model:prune schedule

Why: the privacy page promises these retention periods; storage limitation (Art. 5) requires enforcing them.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 4: Fix the dead enquiry-mail config key

`ContactFormComponent` mails `config('kidicalmass.email.communications')`, a key that does not exist (the real key is `kidicalmass.mail.communications`, see `config/kidicalmass.php:11`). Submissions save to DB but the notification mail silently fails. Found during this audit; personal data sitting unanswered is a privacy problem too.

**Files:**
- Modify: `app/Livewire/ContactFormComponent.php:62`
- Create: `tests/Feature/ContactFormComponentTest.php`

**Interfaces:**
- Consumes: `App\Mail\ContactFormSubmitted`, `config('kidicalmass.mail.communications')`.
- Produces: nothing new; behaviour fix only.

- [ ] **Step 1: Check for other occurrences**

Run: `grep -rn "kidicalmass.email" app/ resources/ config/ | grep -v worktrees`
Expected: only `app/Livewire/ContactFormComponent.php:62`. Fix every hit the same way if more turn up.

- [ ] **Step 2: Write the failing test**

Create `tests/Feature/ContactFormComponentTest.php`:

```php
<?php

use App\Livewire\ContactFormComponent;
use App\Mail\ContactFormSubmitted;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('mails the communications inbox when the contact form is submitted', function () {
    Mail::fake();
    config(['kidicalmass.mail.communications' => 'comms@example.com']);

    Livewire::test(ContactFormComponent::class)
        ->set('name', 'Test Ouder')
        ->set('email', 'ouder@gmail.com')
        ->set('message', 'Wanneer is de volgende rit?')
        ->call('submit')
        ->assertSet('submitted', true);

    Mail::assertSent(ContactFormSubmitted::class, fn ($mail) => $mail->hasTo('comms@example.com'));
});
```

Note: the email validation rule is `email:rfc,dns,spoof`, so use a domain with real DNS (`gmail.com` above) or the validator rejects it.

- [ ] **Step 3: Run it to verify it fails**

Run: `php artisan test --compact --filter=ContactFormComponentTest`
Expected: FAIL (mail goes to a null address; the `try/catch` swallows the error, so `Mail::assertSent` finds no mail with that recipient).

- [ ] **Step 4: Fix the key**

In `app/Livewire/ContactFormComponent.php:62`, change:

```php
            Mail::to(config('kidicalmass.email.communications'))
```

to:

```php
            Mail::to(config('kidicalmass.mail.communications'))
```

- [ ] **Step 5: Run the test to verify it passes**

Run: `php artisan test --compact --filter=ContactFormComponentTest`
Expected: PASS.

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/ContactFormComponent.php tests/Feature/ContactFormComponentTest.php
git commit -m "fix(contact): send enquiry notification to the real communications config key

Why: kidicalmass.email.communications never existed, so contact submissions were saved but the alert mail silently failed.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 5: Privacy-enhanced YouTube embed on home

The home hero iframe loads `www.youtube.com/embed/...`, which sets tracking cookies on page load: the single thing on the site that would legally require a consent banner. `youtube-nocookie.com` (YouTube's privacy-enhanced mode) does not, and works with the existing IFrame Player API loop script unchanged. The privacy page copy (Task 1) already claims nocookie, so this task makes that claim true.

**Files:**
- Modify: `resources/views/home.blade.php:14`
- Modify: `tests/Feature/PublicStructureTest.php` (add one guard test)

**Interfaces:**
- Consumes: nothing from other tasks (but Task 1's copy references it; land both before wrap-up).
- Produces: nothing; behaviour fix only.

- [ ] **Step 1: Write the failing test**

Add to `tests/Feature/PublicStructureTest.php`:

```php
it('embeds the home hero video in privacy-enhanced (nocookie) mode', function () {
    get('/nl')
        ->assertOk()
        ->assertSee('www.youtube-nocookie.com/embed', escape: false)
        ->assertDontSee('www.youtube.com/embed', escape: false);
});
```

(Match the file's existing import style; it already uses `get()` and `escape: false`.)

- [ ] **Step 2: Run it to verify it fails**

Run: `php artisan test --compact --filter="nocookie"`
Expected: FAIL on `assertSee('www.youtube-nocookie.com/embed')`.

- [ ] **Step 3: Swap the embed domain**

In `resources/views/home.blade.php:14`, change the iframe `src` host from `https://www.youtube.com/embed/` to `https://www.youtube-nocookie.com/embed/` (query string and everything else stays identical). Leave the `https://www.youtube.com/iframe_api` script at line 169 untouched: that is the documented way to drive a nocookie embed and the script itself sets no cookies.

- [ ] **Step 4: Run the test to verify it passes**

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: PASS (new test green, existing guards untouched).

- [ ] **Step 5: Verify the loop still works in the browser**

Load `https://kidicalmass.test/nl` and confirm the hero video autoplays muted and loops (the seek-to-0 script drives the player; a broken API hookup would leave it parked on YouTube's paused overlay).

- [ ] **Step 6: Commit**

```bash
git add resources/views/home.blade.php tests/Feature/PublicStructureTest.php
git commit -m "fix(privacy): home hero video via youtube-nocookie

Why: the standard embed set tracking cookies on load, the only thing on the site that would force a consent banner.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 6: Disable public registration (enact the documented invite-only decision)

The wiki decides accounts are invite-only with no public register (`docs/wiki/design/20-structure.md:33`, `docs/wiki/design/01-concerns.md:64`), and the Task 1 privacy copy states "Er is geen publieke registratie". But `config/fortify.php:147` enables `Features::registration()`, so `GET/POST /register` are live: anyone can create an account, which contradicts the copy and collects data with no purpose (data minimisation). One-line rollback if Nico objects; flag it to him in the wrap-up message.

**Files:**
- Modify: `config/fortify.php:147`
- Modify: `resources/views/livewire/auth/login.blade.php:55` (guard the sign-up link)
- Create: `tests/Feature/Auth/PublicRegistrationDisabledTest.php` (put it wherever existing auth tests live; check `ls tests/Feature/Auth 2>/dev/null || grep -rln "login" tests/Feature | head -3` first and co-locate)

**Interfaces:**
- Consumes: Fortify feature flags.
- Produces: `/register` returns 404; login page renders without a register link.

- [ ] **Step 1: Check for tests that depend on registration**

Run: `grep -rn "'/register'\|\"register\"\|route('register')" tests/ app/ resources/views | grep -v worktrees`
Expected: only `resources/views/livewire/auth/login.blade.php:55`. If any test exercises `/register`, STOP and rewrite it to assert 404 as part of this task (do not delete tests without approval; modifying is fine, note it in the commit body).

- [ ] **Step 2: Write the failing test**

Create the test file (co-located with existing auth tests per the check above):

```php
<?php

use function Pest\Laravel\get;

it('does not expose public registration', function () {
    get('/register')->assertNotFound();
});

it('still serves the login page without a register link', function () {
    get('/login')
        ->assertOk()
        ->assertDontSee(url('/register'), escape: false);
});
```

- [ ] **Step 3: Run it to verify it fails**

Run: `php artisan test --compact --filter=PublicRegistrationDisabledTest`
Expected: FAIL (`/register` currently returns 200).

- [ ] **Step 4: Remove the feature and guard the link**

In `config/fortify.php:147`, delete the line:

```php
        Features::registration(),
```

In `resources/views/livewire/auth/login.blade.php:55`, wrap the sign-up link (and its surrounding sentence markup if it reads oddly alone) in a route guard:

```blade
@if (Route::has('register'))
    <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
@endif
```

Look at lines 50-58 first: if the link sits inside a "Don't have an account? Sign up" paragraph, wrap the whole paragraph so no dangling copy remains.

- [ ] **Step 5: Run the tests**

Run: `php artisan test --compact --filter=PublicRegistrationDisabledTest`
Expected: PASS.
Then run the auth suite: `php artisan test --compact tests/Feature/Auth` (or the nearest auth test path found in Step 1's check).
Expected: PASS.

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add config/fortify.php resources/views/livewire/auth/login.blade.php tests/Feature/Auth/PublicRegistrationDisabledTest.php
git commit -m "fix(auth): disable public registration per the invite-only decision

- Fortify registration feature off; /register 404s
- login view guards its sign-up link with Route::has('register')

Why: wiki decision (20-structure.md, concerns C-invite-only) says accounts are invite-provisioned; an open /register collected data with no purpose. One-line rollback if needed. Flag to Nico.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 7: Pipeline + wiki bookkeeping

Follow the four-part update from CLAUDE.md ("Updating the build pipeline"). No code.

**Files:**
- Modify: `docs/wiki/design/30-skeleton/00-page-registry.md` (P-06 row at :25, roll-up at :57)
- Modify: `docs/wiki/log.md` (append entry)
- Modify: `docs/wiki/build/30-launch-runway.md` (:47 initiative note)

**Interfaces:** none.

- [ ] **Step 1: Update the P-06 registry row**

Replace the P-06 row (line 25, keep all 12 columns) with:

```markdown
| P-06 | **Legal / GDPR** | `/privacy` | Utility | 🟢 | 3 | 🟠 | ⚪ | 🟠 | ⚪ | 🔴 | `[client]` confirm legal entity (vzw? address) + name hosting/mail processors with the coördinatieduo, then bump Conf. `[strategy]` add analytics line when Fathom lands; newsletter § assumes Nico's double-opt-in backend. Privacy page live: Art-13 copy, config-driven cookie list, form notes, retention pruning. |
```

(Wire and UI stay 🟠: Claude's render check tops out at 🟠; Frederik's own critique pass promotes them. Back was ❓, now ⚪: static page, no CMS.)

- [ ] **Step 2: Reconcile the roll-up prose**

In the roll-up below the table (~line 57), the sentence "Routed but not built: Contact + Legal views are stubs (Wire 🔴). Legal folded — `/cookies` 301s to `/privacy`." must now read that only Contact is a stub; keep the `/cookies` folding note. Match the surrounding style and keep any aggregate counts consistent with the new row.

- [ ] **Step 3: Update the launch-runway initiative**

In `docs/wiki/build/30-launch-runway.md`, the P-06 initiative (~line 47) and the risk line (~line 35) mention the privacy stub while forms collect personal data. Update both to reflect: privacy page live, form notices + retention in place; remaining = legal entity + processor names (client) and the Fathom line later. Match the board's existing notation.

- [ ] **Step 4: Append the log entry**

Append to `docs/wiki/log.md`:

```markdown
## [<today's date>] build | P-06 privacy page + GDPR interventions

Privacy & cookies page replaces the stub: full NL Art-13 copy (purposes, bases, recipients, retention, rights, GBA), config-driven cookie inventory, photo-consent section. Around it: privacy note component under all five data-collecting forms, ContactForm retention pruning (12m after handling / 24m cap) on a daily model:prune, contact-mail config-key fix, home hero swapped to youtube-nocookie (keeps the site banner-free), public /register disabled per the invite-only decision (flag to Nico). Open: vzw entity + processor names with the coördinatieduo; analytics line when Fathom lands; newsletter double-opt-in backend.
```

- [ ] **Step 5: Verify the dashboard still parses**

Run: `php artisan tinker --execute 'dump(app(App\Support\Build\BuildStatus::class)->report()["warnings"] ?? []);'`
Expected: empty warnings, P-06 stages parse as intended, no unexpected drift.

- [ ] **Step 6: Commit**

```bash
git add docs/wiki/design/30-skeleton/00-page-registry.md docs/wiki/log.md docs/wiki/build/30-launch-runway.md
git commit -m "docs(wiki): P-06 privacy page live in the registry + runway + log

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

## Out of scope (deliberately)

- **Fathom/Plausible install** (Nico owns it, per the launch runway). A blade comment in the cookies section marks where its one-line disclosure goes.
- **Newsletter backend / double opt-in** (Nico's TODO). The privacy copy describes the double-opt-in flow the UI already promises ("Kijk even in je mailbox"); the registry gap tracks that the backend must deliver it.
- **Consent checkboxes.** Not required: the enquiry forms run on legitimate interest with an at-collection notice (Task 2), and newsletter consent is the unbundled, informed act of submitting the form plus double opt-in. Extra checkboxes add friction without adding lawfulness.
- **vzw entity / registered address / processor names.** Client facts nobody in the repo knows; tracked as `[client]` registry gaps. The copy is written to be true without them.
- **DPA/verwerkersovereenkomst review with hosting + mail providers** at launch: operational, not code; lives on the launch runway.

## Wrap-up note for Frederik (whoever executes: include this in the final report)

Two judgment calls to sanity-check: (1) Task 6 turns off public `/register`; the wiki says invite-only but Nico should confirm nothing in his provisioning flow uses it. (2) Retention periods (12 months after handling, 24 months cap) are a reasonable default, not a legal mandate; shorten or lengthen at will, then update both the copy and `ContactForm::prunable()` together.
