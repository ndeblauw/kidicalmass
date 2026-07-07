# Branded Login View (P-07) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the default Flux-starter auth pages with a Kidical Mass branded, NL-copy "geel veld" shell (photo collage + form on light yellow) across login and its password-flow siblings.

**Architecture:** One new branded layout (`resources/views/layouts/auth.blade.php`, rewritten in place) renders every auth view: `<x-photo-collage>` left, form column right, on `bg-kidical-light-yellow`. Composition and the role pill live in a new `resources/css/pages/auth.css` partial. All copy moves to `lang/nl/auth.php` + `lang/nl/passwords.php` (app locale is `nl`). Backend (Fortify, roles, redirects, `DemoLoginController`) is untouched.

**Tech Stack:** Laravel 13, Fortify, Livewire/Flux views, Tailwind v4 tokens, Pest 4.

**Spec:** `docs/superpowers/specs/2026-07-07-login-view-design.md`

## Global Constraints

- Headings: raw `<h1>`, never `flux:heading` (drop `<x-auth-header>` usage; leave the component file in place).
- Other Flux form components (`flux:input`, `flux:checkbox`, `flux:otp`, `flux:link`) stay.
- No raw hex/px in blade styling contexts; CSS partial rules must sit in `@layer components`, colours only via tokens/`color-mix()`/keywords (enforced by `tests/Feature/CssArchitectureTest.php`). `white` keyword is allowed; `#fff` and `rgb(255,255,255)` are not.
- New CSS goes in `resources/css/pages/auth.css`, registered in `app.css`'s `@import` block (pages group is alphabetical: insert between `article.css` and `calendar.css`). Never rules in `app.css` itself.
- Copy: NL, tone-of-voice (warm, inclusive), **no em-dashes**.
- New test seams `data-*` only; do not delete tests; keep auth test coverage thin (behaviour only).
- After PHP edits: `vendor/bin/pint --dirty --format agent`.
- Commit after each task; shared checkout with Nico — stage by explicit path only, never `git add -A`, don't push.
- Dev quick-login block (`login/as/{role}`) survives, login page only, non-prod only, existing `data-test` hooks kept.

---

### Task 1: NL copy — `lang/nl/auth.php` + `lang/nl/passwords.php`

**Files:**
- Create: `lang/nl/auth.php`
- Create: `lang/nl/passwords.php`

**Interfaces:**
- Produces: `__('auth.<key>')` and `__('passwords.<key>')` strings used by every later task. Key names below are the contract — later tasks use them verbatim.

- [ ] **Step 1: Create `lang/nl/auth.php`**

```php
<?php

return [
    // Framework/Fortify validation-adjacent strings
    'failed' => 'Deze gegevens kloppen niet met wat wij kennen. Probeer het nog eens.',
    'password' => 'Het wachtwoord klopt niet.',
    'throttle' => 'Te veel pogingen. Probeer het over :seconds seconden opnieuw.',

    // Shared shell
    'role_pill' => 'Vrijwilligers',
    'logo_alt' => 'Kidical Mass',
    'collage_alt_group' => 'Vrijwilligers in roze hesjes verzamelen op de kasseien',
    'collage_alt_trio' => 'Drie deelnemers lachen naar de camera tijdens een Kidical Mass',
    'collage_alt_vest' => 'Vrijwilliger met een roze Kidical Mass-hesje op de rug',

    // Login
    'login_title' => 'Welkom terug',
    'login_intro' => 'Fijn dat je er weer bent. Log in en ga verder waar je gebleven was.',
    'email' => 'E-mailadres',
    'password_label' => 'Wachtwoord',
    'forgot_password' => 'Wachtwoord vergeten?',
    'remember' => 'Ingelogd blijven',
    'login_button' => 'Log in',
    'dev_login_label' => 'Snel inloggen (dev)',
    'dev_login_user' => 'Gebruiker',
    'dev_login_pinkvest' => 'Roze hesje',
    'dev_login_captain' => 'Kapitein',
    'dev_login_admin' => 'Admin',

    // Forgot password
    'forgot_title' => 'Wachtwoord vergeten?',
    'forgot_intro' => 'Geen zorgen. Geef je e-mailadres en we sturen je een link om een nieuw wachtwoord te kiezen.',
    'forgot_button' => 'Stuur de link',
    'back_to_login' => 'Terug naar inloggen',

    // Reset password
    'reset_title' => 'Kies een nieuw wachtwoord',
    'reset_intro' => 'Kies iets dat je makkelijk onthoudt en dat alleen jij kent.',
    'password_confirm_label' => 'Herhaal je wachtwoord',
    'reset_button' => 'Bewaar nieuw wachtwoord',

    // Confirm password
    'confirm_title' => 'Even je wachtwoord bevestigen',
    'confirm_intro' => 'Dit is een beveiligd deel van de site. Vul je wachtwoord in om verder te gaan.',
    'confirm_button' => 'Bevestig',

    // Two-factor challenge
    'two_factor_title' => 'Verificatiecode',
    'two_factor_intro_code' => 'Vul de code in uit je authenticator-app.',
    'two_factor_intro_recovery' => 'Vul een van je herstelcodes in om weer binnen te raken.',
    'two_factor_button' => 'Ga verder',
    'two_factor_or' => 'of',
    'two_factor_use_recovery' => 'log in met een herstelcode',
    'two_factor_use_code' => 'log in met een verificatiecode',

    // Verify email
    'verify_title' => 'Bevestig je e-mailadres',
    'verify_intro' => 'Klik op de link in de mail die we je net stuurden. Niets gekregen? Kijk even bij je spam, of vraag een nieuwe.',
    'verify_sent' => 'We hebben je een nieuwe bevestigingslink gestuurd.',
    'verify_resend' => 'Stuur de mail opnieuw',
    'logout' => 'Log uit',
];
```

- [ ] **Step 2: Create `lang/nl/passwords.php`**

```php
<?php

return [
    'reset' => 'Je wachtwoord is opnieuw ingesteld.',
    'sent' => 'We hebben je een mail gestuurd met een link om je wachtwoord opnieuw in te stellen.',
    'throttled' => 'Even wachten voor je het opnieuw probeert.',
    'token' => 'Deze link is niet meer geldig. Vraag een nieuwe aan.',
    'user' => 'We vinden geen account met dat e-mailadres.',
];
```

- [ ] **Step 3: Verify resolution**

Run: `php artisan tinker --execute 'echo __("auth.login_title")." / ".__("passwords.sent");'`
Expected: `Welkom terug / We hebben je een mail gestuurd met een link om je wachtwoord opnieuw in te stellen.`

- [ ] **Step 4: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add lang/nl/auth.php lang/nl/passwords.php
git commit -m "feat(auth): NL copy for auth views and password flow"
```

---

### Task 2: Branded shell + CSS partial + login view

**Files:**
- Modify: `resources/views/layouts/auth.blade.php` (full rewrite; currently delegates to `auth/simple`)
- Create: `resources/css/pages/auth.css`
- Modify: `resources/css/app.css` (one `@import` line in the pages block)
- Modify: `resources/views/livewire/auth/login.blade.php` (full rewrite)
- Test: `tests/Feature/Auth/AuthViewsTest.php` (create)

**Interfaces:**
- Consumes: `__('auth.*')` keys from Task 1; existing components `<x-photo-collage>` (props: `photos` array of `['src','alt','pos'?]`, placement via page CSS nth-child), `<x-photo>`, `<x-cta-button>` (props: `type`, `variant`, `block`), `<x-auth-session-status>`.
- Produces: layout `<x-layouts::auth :title="..." :intro="...">` — `title` renders the raw `<h1>`, `intro` the muted paragraph, default slot is the form column content. CSS classes `.auth-page__*` for later tasks. Sibling views (Tasks 3-4) rely on exactly this API.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Auth/AuthViewsTest.php`:

```php
<?php

// Branded auth shell (P-07): prove the volunteer door renders the NL shell —
// title, intro, role pill, collage. Rendering only; auth behaviour lives in
// AuthenticationTest, Fortify internals are not re-tested (thin-Auth rubric).

test('login page renders the branded NL shell', function () {
    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertSee(__('auth.login_title'))
        ->assertSee(__('auth.login_intro'))
        ->assertSee(__('auth.role_pill'))
        ->assertSee('photo-collage');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/Auth/AuthViewsTest.php`
Expected: FAIL (page still shows the EN starter copy, no role pill, no collage)

- [ ] **Step 3: Rewrite `resources/views/layouts/auth.blade.php`**

Full replacement (the starter `auth/simple|card|split.blade.php` files become unused — leave them; same for `<x-auth-header>`):

```blade
@props(['title' => null, 'intro' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.site-head')
    </head>
    <body class="auth-page min-h-svh bg-kidical-light-yellow antialiased">
        <div class="auth-page__grid min-h-svh p-6 md:p-10">
            <x-photo-collage
                class="auth-page__collage"
                :photos="[
                    ['src' => 'img/photography/volunteers-pink-vest-group-cobbles.webp', 'alt' => __('auth.collage_alt_group')],
                    ['src' => 'img/photography/ride-trio-pink-vest-lei-portrait.webp', 'alt' => __('auth.collage_alt_trio'), 'pos' => '60% 30%'],
                    ['src' => 'img/photography/volunteer-pink-vest.webp', 'alt' => __('auth.collage_alt_vest'), 'pos' => 'center 20%'],
                ]" />

            <div class="auth-page__form">
                <div class="auth-page__logo-row">
                    <a href="{{ route('home') }}">
                        <img class="auth-page__logo" src="{{ asset('img/logos/logo.png') }}" alt="{{ __('auth.logo_alt') }}">
                    </a>
                    <span class="auth-page__role-pill">{{ __('auth.role_pill') }}</span>
                </div>

                @if ($title)
                    <h1 class="auth-page__title">{{ $title }}</h1>
                @endif

                @if ($intro)
                    <p class="auth-page__intro">{{ $intro }}</p>
                @endif

                {{ $slot }}
            </div>
        </div>

        @fluxScripts
        @stack('scripts')
    </body>
</html>
```

Notes for the implementer:
- `partials.site-head` (not `partials.head`) loads the brand fonts (Caprasimo/Nunito Sans) and Vite. `$title` propagates into the `<title>` tag automatically via the partial's `$title ?? null`.
- No `class="dark"` on `<html>` and no `@fluxAppearance`: auth pages are always light.
- `@stack('scripts')` is required — `<x-photo-collage>` pushes its settle-entrance script there.

- [ ] **Step 4: Create `resources/css/pages/auth.css`**

```css
@layer components {
    /* Auth shell (P-07) — the "geel veld" volunteers' door: photo collage +
       form breathing directly on the light-yellow field. One column on mobile
       (compact collage strip on top), two centered columns from lg. Spec:
       docs/superpowers/specs/2026-07-07-login-view-design.md */
    .auth-page__grid {
        display: grid;
        align-content: center;
        justify-content: center;
        justify-items: center;
        gap: 2.5rem;
    }

    @media (min-width: 1024px) {
        .auth-page__grid {
            grid-template-columns: minmax(0, 430px) minmax(0, 400px);
            align-items: center;
            gap: 5rem;
        }
    }

    .auth-page__form {
        width: min(100%, 400px);
    }

    /* Collage placement: mobile-first as a wide strip (M1), square scatter
       from lg. The component leaves --pc-x/y/w/r to the page when not passed
       inline; positions per nth-child, matching the approved mockups. */
    .auth-page__collage {
        max-width: 430px;
    }

    .auth-page .photo-collage {
        aspect-ratio: 2.4 / 1;
    }

    .auth-page .photo-collage__photo:nth-child(1) { --pc-x: 26%; --pc-y: 44%; --pc-w: 34%; --pc-r: -3deg; }
    .auth-page .photo-collage__photo:nth-child(2) { --pc-x: 53%; --pc-y: 52%; --pc-w: 31%; --pc-r: 2.5deg; }
    .auth-page .photo-collage__photo:nth-child(3) { --pc-x: 78%; --pc-y: 46%; --pc-w: 33%; --pc-r: -2deg; }

    @media (min-width: 1024px) {
        .auth-page .photo-collage {
            aspect-ratio: 1 / 1;
        }

        .auth-page .photo-collage__photo:nth-child(1) { --pc-x: 32%; --pc-y: 27%; --pc-w: 56%; --pc-r: -3deg; }
        .auth-page .photo-collage__photo:nth-child(2) { --pc-x: 77%; --pc-y: 40%; --pc-w: 50%; --pc-r: 2.5deg; }
        .auth-page .photo-collage__photo:nth-child(3) { --pc-x: 45%; --pc-y: 75%; --pc-w: 54%; --pc-r: -2deg; }
    }

    /* Logo + role pill, after the header postcode-pill pattern
       (.site-nav__postcode) but quiet: white capsule, pink text. */
    .auth-page__logo-row {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        margin-bottom: 1.75rem;
    }

    .auth-page__logo {
        height: 3.7rem;
        width: auto;
    }

    .auth-page__role-pill {
        display: inline-flex;
        align-items: center;
        height: 2.1rem;
        padding-inline: 0.85rem;
        border-radius: var(--radius-pill);
        background: white;
        color: var(--color-kidical-red-text);
        font-weight: 700;
        font-size: var(--text-sm);
        line-height: 1;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        box-shadow: 0 1px 4px color-mix(in oklab, var(--color-kidical-ink), transparent 88%);
    }

    /* The base h1 clamp is tuned for full-width pages; cap it for the narrow
       form column. */
    .auth-page__title {
        font-size: var(--text-4xl);
        margin-bottom: 0.5rem;
    }

    .auth-page__intro {
        color: var(--color-text-body);
        margin-bottom: 1.5rem;
    }

    /* Dev quick-login (non-prod): quiet footnote under the form. */
    .auth-page__dev {
        margin-top: 2rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--color-kidical-hairline);
    }

    .auth-page__dev-label {
        font-size: var(--text-xs);
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 55%);
        text-align: center;
        margin-bottom: 0.6rem;
    }
}
```

- [ ] **Step 5: Register the partial in `app.css`**

In the pages `@import` block of `resources/css/app.css`, add after the `./pages/article.css` line:

```css
@import './pages/auth.css';
```

- [ ] **Step 6: Rewrite `resources/views/livewire/auth/login.blade.php`**

```blade
<x-layouts::auth :title="__('auth.login_title')" :intro="__('auth.login_intro')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
        @csrf

        <flux:input
            name="email"
            :label="__('auth.email')"
            :value="old('email')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="naam@voorbeeld.be"
        />

        <div class="relative">
            <flux:input
                name="password"
                :label="__('auth.password_label')"
                type="password"
                required
                autocomplete="current-password"
                viewable
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute top-0 end-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('auth.forgot_password') }}
                </flux:link>
            @endif
        </div>

        <flux:checkbox name="remember" :label="__('auth.remember')" :checked="old('remember')" />

        <x-cta-button type="submit" variant="yellow" block data-test="login-button">
            {{ __('auth.login_button') }}
        </x-cta-button>
    </form>

    @unless (app()->isProduction())
        <div class="auth-page__dev">
            <p class="auth-page__dev-label">{{ __('auth.dev_login_label') }}</p>
            <div class="grid grid-cols-2 gap-2">
                <x-cta-button :href="route('login.as', 'pinkvest')" variant="secondary" size="sm" block data-test="login-as-pinkvest">
                    {{ __('auth.dev_login_pinkvest') }}
                </x-cta-button>
                <x-cta-button :href="route('login.as', 'captain')" variant="secondary" size="sm" block data-test="login-as-captain">
                    {{ __('auth.dev_login_captain') }}
                </x-cta-button>
                <x-cta-button :href="route('login.as', 'user')" variant="secondary" size="sm" block data-test="login-as-user">
                    {{ __('auth.dev_login_user') }}
                </x-cta-button>
                <x-cta-button :href="route('login.as', 'admin')" variant="secondary" size="sm" block data-test="login-as-admin">
                    {{ __('auth.dev_login_admin') }}
                </x-cta-button>
            </div>
        </div>
    @endunless
</x-layouts::auth>
```

Notes: the register block from the starter is dropped entirely (invite-only, route disabled). `data-test` hooks preserved. The old view's `<!-- comments -->` go away with it.

- [ ] **Step 7: Build CSS and run the tests**

```bash
npm run build
php artisan test --compact tests/Feature/Auth/AuthViewsTest.php tests/Feature/CssArchitectureTest.php tests/Feature/Auth/AuthenticationTest.php
```

Expected: all PASS. If `CssArchitectureTest` flags the partial, fix the flagged value (tokens/color-mix only) rather than allowlisting.

- [ ] **Step 8: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/layouts/auth.blade.php resources/css/pages/auth.css resources/css/app.css resources/views/livewire/auth/login.blade.php tests/Feature/Auth/AuthViewsTest.php
git commit -m "feat(auth): branded geel-veld login shell (P-07)"
```

---

### Task 3: Forgot + reset password views

**Files:**
- Modify: `resources/views/livewire/auth/forgot-password.blade.php` (full rewrite)
- Modify: `resources/views/livewire/auth/reset-password.blade.php` (full rewrite)
- Test: `tests/Feature/Auth/AuthViewsTest.php` (extend)

**Interfaces:**
- Consumes: `<x-layouts::auth :title :intro>` from Task 2; `__('auth.*')` keys from Task 1.

- [ ] **Step 1: Add failing tests**

Append to `tests/Feature/Auth/AuthViewsTest.php`:

```php
test('forgot-password page renders in the branded shell', function () {
    $this->get(route('password.request'))
        ->assertOk()
        ->assertSee(__('auth.forgot_title'))
        ->assertSee(__('auth.role_pill'));
});

test('reset-password page renders in the branded shell', function () {
    $this->get(route('password.reset', 'dummy-token'))
        ->assertOk()
        ->assertSee(__('auth.reset_title'));
});
```

- [ ] **Step 2: Run to verify they fail**

Run: `php artisan test --compact tests/Feature/Auth/AuthViewsTest.php`
Expected: the two new tests FAIL (EN starter copy), the login test still PASSES.

- [ ] **Step 3: Rewrite `forgot-password.blade.php`**

```blade
<x-layouts::auth :title="__('auth.forgot_title')" :intro="__('auth.forgot_intro')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
        @csrf

        <flux:input
            name="email"
            :label="__('auth.email')"
            type="email"
            required
            autofocus
            placeholder="naam@voorbeeld.be"
        />

        <x-cta-button type="submit" variant="yellow" block data-test="email-password-reset-link-button">
            {{ __('auth.forgot_button') }}
        </x-cta-button>
    </form>

    <p class="mt-6 text-center text-sm">
        <flux:link :href="route('login')" wire:navigate>{{ __('auth.back_to_login') }}</flux:link>
    </p>
</x-layouts::auth>
```

- [ ] **Step 4: Rewrite `reset-password.blade.php`**

```blade
<x-layouts::auth :title="__('auth.reset_title')" :intro="__('auth.reset_intro')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5">
        @csrf
        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <flux:input
            name="email"
            value="{{ request('email') }}"
            :label="__('auth.email')"
            type="email"
            required
            autocomplete="email"
        />

        <flux:input
            name="password"
            :label="__('auth.password_label')"
            type="password"
            required
            autocomplete="new-password"
            viewable
        />

        <flux:input
            name="password_confirmation"
            :label="__('auth.password_confirm_label')"
            type="password"
            required
            autocomplete="new-password"
            viewable
        />

        <x-cta-button type="submit" variant="yellow" block data-test="reset-password-button">
            {{ __('auth.reset_button') }}
        </x-cta-button>
    </form>
</x-layouts::auth>
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --compact tests/Feature/Auth/AuthViewsTest.php`
Expected: all PASS.

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/livewire/auth/forgot-password.blade.php resources/views/livewire/auth/reset-password.blade.php tests/Feature/Auth/AuthViewsTest.php
git commit -m "feat(auth): forgot/reset password views in branded shell"
```

---

### Task 4: Confirm-password, two-factor, verify-email views

**Files:**
- Modify: `resources/views/livewire/auth/confirm-password.blade.php` (full rewrite)
- Modify: `resources/views/livewire/auth/two-factor-challenge.blade.php` (full rewrite)
- Modify: `resources/views/livewire/auth/verify-email.blade.php` (full rewrite)

**Interfaces:**
- Consumes: `<x-layouts::auth :title :intro>` from Task 2; `__('auth.*')` keys from Task 1.

No new tests: these pages need an authenticated/challenged session to GET; their rendering shell is already proven by Task 2/3 tests, and the thin-Auth rubric says don't re-test Fortify flows. Existing suite must stay green.

- [ ] **Step 1: Rewrite `confirm-password.blade.php`**

```blade
<x-layouts::auth :title="__('auth.confirm_title')" :intro="__('auth.confirm_intro')">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-5">
        @csrf

        <flux:input
            name="password"
            :label="__('auth.password_label')"
            type="password"
            required
            autocomplete="current-password"
            viewable
        />

        <x-cta-button type="submit" variant="yellow" block data-test="confirm-password-button">
            {{ __('auth.confirm_button') }}
        </x-cta-button>
    </form>
</x-layouts::auth>
```

- [ ] **Step 2: Rewrite `two-factor-challenge.blade.php`**

The two toggled `<x-auth-header>` blocks collapse into one static `h1` (via the shell `title` prop) + two toggled intro paragraphs inside the slot. Alpine wiring is unchanged.

```blade
<x-layouts::auth :title="__('auth.two_factor_title')">
    <div
        class="relative w-full h-auto"
        x-cloak
        x-data="{
            showRecoveryInput: @js($errors->has('recovery_code')),
            code: '',
            recovery_code: '',
            toggleInput() {
                this.showRecoveryInput = !this.showRecoveryInput;

                this.code = '';
                this.recovery_code = '';

                $dispatch('clear-2fa-auth-code');

                $nextTick(() => {
                    this.showRecoveryInput
                        ? this.$refs.recovery_code?.focus()
                        : $dispatch('focus-2fa-auth-code');
                });
            },
        }"
    >
        <p class="auth-page__intro" x-show="!showRecoveryInput">{{ __('auth.two_factor_intro_code') }}</p>
        <p class="auth-page__intro" x-show="showRecoveryInput">{{ __('auth.two_factor_intro_recovery') }}</p>

        <form method="POST" action="{{ route('two-factor.login.store') }}">
            @csrf

            <div class="space-y-5 text-center">
                <div x-show="!showRecoveryInput">
                    <div class="flex items-center justify-center my-5">
                        <flux:otp
                            x-model="code"
                            length="6"
                            name="code"
                            label="OTP Code"
                            label:sr-only
                            class="mx-auto"
                         />
                    </div>
                </div>

                <div x-show="showRecoveryInput">
                    <div class="my-5">
                        <flux:input
                            type="text"
                            name="recovery_code"
                            x-ref="recovery_code"
                            x-bind:required="showRecoveryInput"
                            autocomplete="one-time-code"
                            x-model="recovery_code"
                        />
                    </div>

                    @error('recovery_code')
                        <flux:text color="red">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>

                <x-cta-button type="submit" variant="yellow" block>
                    {{ __('auth.two_factor_button') }}
                </x-cta-button>
            </div>

            <div class="mt-5 space-x-0.5 text-sm leading-5 text-center">
                <span class="opacity-50">{{ __('auth.two_factor_or') }}</span>
                <div class="inline font-medium underline cursor-pointer opacity-80">
                    <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('auth.two_factor_use_recovery') }}</span>
                    <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('auth.two_factor_use_code') }}</span>
                </div>
            </div>
        </form>
    </div>
</x-layouts::auth>
```

- [ ] **Step 3: Rewrite `verify-email.blade.php`**

```blade
<x-layouts::auth :title="__('auth.verify_title')" :intro="__('auth.verify_intro')">
    @if (session('status') == 'verification-link-sent')
        <p class="mb-4 text-center font-medium text-kidical-green">
            {{ __('auth.verify_sent') }}
        </p>
    @endif

    <div class="flex flex-col items-center gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-cta-button type="submit" variant="yellow">
                {{ __('auth.verify_resend') }}
            </x-cta-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-cta-button type="submit" variant="ghost" size="sm" data-test="logout-button">
                {{ __('auth.logout') }}
            </x-cta-button>
        </form>
    </div>
</x-layouts::auth>
```

- [ ] **Step 4: Run the auth suite + CSS architecture guard**

Run: `php artisan test --compact tests/Feature/Auth tests/Feature/CssArchitectureTest.php`
Expected: all PASS (AuthenticationTest, PublicRegistrationDisabledTest, AuthViewsTest, CssArchitectureTest).

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/livewire/auth/confirm-password.blade.php resources/views/livewire/auth/two-factor-challenge.blade.php resources/views/livewire/auth/verify-email.blade.php
git commit -m "feat(auth): confirm/two-factor/verify views in branded shell"
```

---

### Task 5: Live verification + screenshot pass

**Files:**
- Create: none permanent (screenshot script goes to the session scratchpad, screenshots to `/tmp` or scratchpad)

**Interfaces:**
- Consumes: the running Herd site `https://kidicalmass.test` (get the exact base URL via the `get-absolute-url` Boost tool), routes `login`, `password.request`, `login.as` (`user`/`pinkvest`/`captain`/`admin`).

- [ ] **Step 1: Fresh build + config sanity**

```bash
npm run build
php artisan config:clear --no-interaction
```

- [ ] **Step 2: One batched screenshot pass (desktop + mobile, login + forgot)**

Write a Playwright script to the scratchpad (`.cjs` extension — ESM project; `ignoreHTTPSErrors: true` — Herd self-signed certs; Write tool, never heredoc). Capture in ONE run: `/login` at 1440x900 and 390x844, `/forgot-password` at 1440x900. View the PNGs, verify against the approved mockups: yellow field, collage placement (strip on mobile, square scatter on desktop), colour logo + white/pink VRIJWILLIGERS pill, h1 "Welkom terug", yellow cta pill, dev block under a hairline.

```js
const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch();
  const shots = [
    { url: 'https://kidicalmass.test/login', vp: { width: 1440, height: 900 }, out: 'login-desktop.png' },
    { url: 'https://kidicalmass.test/login', vp: { width: 390, height: 844 }, out: 'login-mobile.png' },
    { url: 'https://kidicalmass.test/forgot-password', vp: { width: 1440, height: 900 }, out: 'forgot-desktop.png' },
  ];
  for (const s of shots) {
    const context = await browser.newContext({ ignoreHTTPSErrors: true, viewport: s.vp });
    const page = await context.newPage();
    await page.goto(s.url);
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: `SCRATCHPAD/${s.out}`, fullPage: true });
    await context.close();
  }
  await browser.close();
})();
```

Fix visual issues in `pages/auth.css` / the layout, rebuild, and re-shoot ONCE (batch fixes; no fix-shoot-fix loop).

- [ ] **Step 3: Verify the full login flow live, per role**

For each of `pinkvest`, `captain`, `admin`, `user`: request `https://kidicalmass.test/login/as/{role}` (Playwright or `curl -k -I`) and confirm a 302 to the role's landing page (roze-hub / chapter page per `LoginResponse`), no 500s. Also submit a real login once via the styled form (Playwright: fill email/password of a seeded dev user, click `[data-test="login-button"]`, expect redirect) to prove the form posts correctly with the new markup.

- [ ] **Step 4: Full test suite for regressions**

Run: `php artisan test --compact`
Expected: green except the two pre-existing known failures (`CalendarProximityTest`, `FilamentAdminTest` — stale-broken before this work; do not fix here, do not let anything NEW fail).

- [ ] **Step 5: Commit any verification fixes**

```bash
git add resources/css/pages/auth.css resources/views/layouts/auth.blade.php
git commit -m "fix(auth): visual polish from live verification pass"
```

(Skip if nothing changed.)

---

### Task 6: Pipeline + docs update

**Files:**
- Modify: `docs/wiki/design/30-skeleton/00-page-registry.md` (P-07 row + Top gaps + Roll-up)
- Modify: `docs/wiki/log.md` (append entry)

**Interfaces:**
- Consumes: the finished, verified views from Tasks 2-5.

- [ ] **Step 1: Run the guided pipeline update**

Invoke the `/pipeline` skill: `P-07 assets=good wire=good ui=wip` — Assets 🟢 (collage photos decided), Wire 🟢 (rendered + visually verified; Frederik's own critique still gates his 🟢-confirmation in `/build/review`), UI 🟠 (surface pass done but awaiting Frederik's critique), Back stays 🟢, CMS stays ⚪. Update the Top gaps cell (drop the styling gap, add "login shell live [content]"-style note per existing conventions) and the Roll-up prose.

Note: per the pipeline convention, Claude's own render check tops out Wire at 🟠 — but the handoff brief for this project explicitly prescribes `wire=good ui=wip` after live verification, so follow the brief.

- [ ] **Step 2: Append the log entry**

Append to `docs/wiki/log.md` (match existing entry style):

```markdown
## [2026-07-07] build | Branded login view (P-07) live

Geel-veld auth shell built: photo collage + NL copy across login/forgot/reset/
confirm/two-factor/verify. Assets decided (collage, Set 1). Spec:
docs/superpowers/specs/2026-07-07-login-view-design.md. Awaiting Frederik's
/build/review critique to flip Wire/UI 🟢.
```

- [ ] **Step 3: Verify the dashboard parses**

Run: `php artisan tinker --execute 'dump(app(App\Support\Build\BuildStatus::class)->report()["pages"]["P-07"] ?? "P-07 missing");'`
Expected: the row parses with the new stages, no `warnings`, no unexpected `drift`. (Alternatively load `/build`.)

- [ ] **Step 4: Commit docs**

```bash
git add docs/wiki/design/30-skeleton/00-page-registry.md docs/wiki/log.md
git commit -m "docs(build): P-07 login shell live — assets/wire/ui bumped"
```

---

## Post-plan reminders (for the wrap)

- The handoff brief's "Done when": Frederik logs in via the styled page, reviews in `/build/review`, flips Wire/UI 🟢. That's his step, not a task here.
- Runway doc: the "Branded login view" row in the launch runway (see `docs/wiki/build/`) should be reconciled at the next `/pipeline` or `/wrap`.
- At `/wrap`: squash this thread's commits into ONE curated commit (guard `git log origin/main..HEAD --format='%an'` first — Nico may have committed in between; never reset across his commits).
