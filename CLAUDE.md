<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- filament/filament (FILAMENT) - v5
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- livewire/flux (FLUXUI_FREE) - v2
- livewire/flux-pro (FLUXUI_PRO) - v2
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`). Run `herd list` to discover all available commands.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

=== filament/filament rules ===

## Filament

- Filament is a Laravel UI framework built on Livewire, Alpine.js, and Tailwind CSS. UIs are defined in PHP via fluent, chainable components. Follow existing conventions in this app.
- Use the `search-docs` tool for official documentation on Artisan commands, code examples, testing, relationships, and idiomatic practices. If `search-docs` is unavailable, refer to https://filamentphp.com/docs.

### Artisan

- Always use Filament-specific Artisan commands to create files. Find available commands with the `list-artisan-commands` tool, or run `php artisan --help`.
- Inspect required options before running, and always pass `--no-interaction`.

### Patterns

Always use static `make()` methods to initialize components. Most configuration methods accept a `Closure` for dynamic values.

Use `Get $get` to read other form field values for conditional logic:

<code-snippet name="Conditional form field visibility" lang="php">
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

Select::make('type')
    ->options(CompanyType::class)
    ->required()
    ->live(),

TextInput::make('company_name')
    ->required()
    ->visible(fn (Get $get): bool => $get('type') === 'business'),

</code-snippet>

Use `Set $set` inside `->afterStateUpdated()` on a `->live()` field to mutate another field reactively. Prefer `->live(onBlur: true)` on text inputs to avoid per-keystroke updates:

<code-snippet name="Reactive field update" lang="php">
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

TextInput::make('title')
    ->required()
    ->live(onBlur: true)
    ->afterStateUpdated(fn (Set $set, ?string $state) => $set(
        'slug',
        Str::slug($state ?? ''),
    )),

TextInput::make('slug')
    ->required(),

</code-snippet>

Compose layout by nesting `Section` and `Grid`. Children need explicit `->columnSpan()` or `->columnSpanFull()`:

<code-snippet name="Section and Grid layout" lang="php">
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

Section::make('Details')
    ->schema([
        Grid::make(2)->schema([
            TextInput::make('first_name')
                ->columnSpan(1),
            TextInput::make('last_name')
                ->columnSpan(1),
            TextInput::make('bio')
                ->columnSpanFull(),
        ]),
    ]),

</code-snippet>

Use `Repeater` for inline `HasMany` management. `->relationship()` with no args binds to the relationship matching the field name:

<code-snippet name="Repeater for HasMany" lang="php">
use Filament\Forms\Components\Repeater;

Repeater::make('qualifications')
    ->relationship()
    ->schema([
        TextInput::make('institution')
            ->required(),
        TextInput::make('qualification')
            ->required(),
    ])
    ->columns(2),

</code-snippet>

Use `state()` with a `Closure` to compute derived column values:

<code-snippet name="Computed table column value" lang="php">
use Filament\Tables\Columns\TextColumn;

TextColumn::make('full_name')
    ->state(fn (User $record): string => "{$record->first_name} {$record->last_name}"),

</code-snippet>

Use `SelectFilter` for enum or relationship filters, and `Filter` with a `->query()` closure for custom logic:

<code-snippet name="Table filters" lang="php">
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

SelectFilter::make('status')
    ->options(UserStatus::class),

SelectFilter::make('author')
    ->relationship('author', 'name'),

Filter::make('verified')
    ->query(fn (Builder $query) => $query->whereNotNull('email_verified_at')),

</code-snippet>

Actions are buttons that encapsulate optional modal forms and behavior:

<code-snippet name="Action with modal form" lang="php">
use Filament\Actions\Action;

Action::make('updateEmail')
    ->schema([
        TextInput::make('email')
            ->email()
            ->required(),
    ])
    ->action(fn (array $data, User $record) => $record->update($data)),

</code-snippet>

### Testing

Testing setup (requires `pestphp/pest-plugin-livewire` in `composer.json`):

- Always call `$this->actingAs(User::factory()->create())` before testing panel functionality.
- For edit pages, pass `['record' => $user->id]`, use `->call('save')` (not `->call('create')`), and do not assert `->assertRedirect()` (edit pages do not redirect after save).

<code-snippet name="Table test" lang="php">
use function Pest\Livewire\livewire;

livewire(ListUsers::class)
    ->assertCanSeeTableRecords($users)
    ->searchTable($users->first()->name)
    ->assertCanSeeTableRecords($users->take(1))
    ->assertCanNotSeeTableRecords($users->skip(1));

</code-snippet>

<code-snippet name="Create resource test" lang="php">
use function Pest\Laravel\assertDatabaseHas;

livewire(CreateUser::class)
    ->fillForm([
        'name' => 'Test',
        'email' => 'test@example.com',
    ])
    ->call('create')
    ->assertNotified()
    ->assertHasNoFormErrors()
    ->assertRedirect();

assertDatabaseHas(User::class, [
    'name' => 'Test',
    'email' => 'test@example.com',
]);

</code-snippet>

<code-snippet name="Edit resource test" lang="php">
livewire(EditUser::class, ['record' => $user->id])
    ->fillForm(['name' => 'Updated'])
    ->call('save')
    ->assertNotified()
    ->assertHasNoFormErrors();

assertDatabaseHas(User::class, [
    'id' => $user->id,
    'name' => 'Updated',
]);

</code-snippet>

<code-snippet name="Testing validation" lang="php">
livewire(CreateUser::class)
    ->fillForm([
        'name' => null,
        'email' => 'invalid-email',
    ])
    ->call('create')
    ->assertHasFormErrors([
        'name' => 'required',
        'email' => 'email',
    ])
    ->assertNotNotified();

</code-snippet>

Use `->callAction(DeleteAction::class)` for page actions, or `->callAction(TestAction::make('name')->table($record))` for table actions:

<code-snippet name="Calling actions" lang="php">
use Filament\Actions\Testing\TestAction;

livewire(ListUsers::class)
    ->callAction(TestAction::make('promote')->table($user), [
        'role' => 'admin',
    ])
    ->assertNotified();

</code-snippet>

### Correct Namespaces

- Form fields (`TextInput`, `Select`, `Repeater`, etc.): `Filament\Forms\Components\`
- Infolist entries (`TextEntry`, `IconEntry`, etc.): `Filament\Infolists\Components\`
- Layout components (`Grid`, `Section`, `Fieldset`, `Tabs`, `Wizard`, etc.): `Filament\Schemas\Components\`
- Schema utilities (`Get`, `Set`, etc.): `Filament\Schemas\Components\Utilities\`
- Table columns (`TextColumn`, `IconColumn`, etc.): `Filament\Tables\Columns\`
- Table filters (`SelectFilter`, `Filter`, etc.): `Filament\Tables\Filters\`
- Actions (`DeleteAction`, `CreateAction`, etc.): `Filament\Actions\`. Never use `Filament\Tables\Actions\`, `Filament\Forms\Actions\`, or any other sub-namespace for actions.
- Icons: `Filament\Support\Icons\Heroicon` enum (e.g., `Heroicon::PencilSquare`)

### Common Mistakes

- **Never assume public file visibility.** File visibility is `private` by default. Always use `->visibility('public')` when public access is needed.
- **Never assume full-width layout.** `Grid`, `Section`, `Fieldset`, and `Repeater` do not span all columns by default.
- **Use `Select::make('author_id')->relationship('author', 'name')` for BelongsTo fields.** `BelongsToSelect` does not exist in v4.
- **`Repeater` uses `->schema()`, not `->fields()`.**
- **Never add `->dehydrated(false)` to fields that need to be saved.** It strips the value from form state before `->action()` or the save handler runs. Only use it for helper/UI-only fields.
- **Use correct property types when overriding `Page`, `Resource`, and `Widget` properties.** These properties have union types or changed modifiers that must be preserved:
  - `$navigationIcon`: `protected static string | BackedEnum | null` (not `?string`)
  - `$navigationGroup`: `protected static string | UnitEnum | null` (not `?string`)
  - `$view`: `protected string` (not `protected static string`) on `Page` and `Widget` classes

=== spatie/laravel-medialibrary rules ===

## Media Library

- `spatie/laravel-medialibrary` associates files with Eloquent models, with support for collections, conversions, and responsive images.
- Always activate the `medialibrary-development` skill when working with media uploads, conversions, collections, responsive images, or any code that uses the `HasMedia` interface or `InteractsWithMedia` trait.

</laravel-boost-guidelines>

# Documentation (Cascade)

Project knowledge lives in `docs/`, organised with the **Cascade** playbook. Read the wiki before substantive product work.

- **`docs/raw/`** — immutable sources (Wix scrape, assets). Cite, never edit. Internal interview/meeting notes are kept in Notion and out of git.
- **`docs/wiki/`** — the synthesis, one folder per phase: `discovery/` (kept in Notion — see its `00-`), `strategy/`, `design/`, `build/`. Plus cross-cutting `index.md`, `log.md`, `glossary.md`, `tone-of-voice.md` (voice).
- **Start at [`docs/wiki/index.md`](docs/wiki/index.md)** (catalogue) and the relevant phase's `00-*-plan.md` + `01-concerns.md`.

**Phases** run over time (Discovery → Strategy → Design → Build); only the active phase is fully decided. **Planes** run within Design (Scope → Structure → Skeleton → Surface); upper planes constrain lower — work top-down. Migration onto this structure is in progress: Strategy is fully re-shelved; Design/Discovery content still lives in flat files (see each `00-plan.md`).

**Concerns registers (`01-concerns.md`) are the keystone:** every open question is a stable-ID concern in `Open` / `Partly` / `Closed` state. A phase closes only when nothing is silently Open.

**Page conventions:** YAML frontmatter on every page (`title`, `tags`, `sources`, `phase`, `updated`). Filenames numbered in dependency order (`00` plan, `01` concerns, then `10`/`20`/…). Keep `index.md` and `log.md` current; history lives in `log.md` + git, not inline "was X now Y" notes.

# Testing — what to assert

Full rubric: [`docs/testing-conventions.md`](docs/testing-conventions.md). Read it before writing or editing tests. The short version:

- **Write fewer, behaviour-focused tests.** One test should prove one observable behaviour. Resist generating a test per class/method or a battery of near-identical variants — coverage is not the goal, catching real regressions is.
- **Assert what a user or browser can observe**, not how it's styled: rendered text (prefer `__('key')` over literal copy), attributes the browser acts on (`href`, `aria-*`, `srcset`, `data-*` hooks, form `name`/`type`), behaviour & state (conditional rendering, redirects, status codes, business logic), and stable semantic/BEM state hooks (`ride-row--featured`).
- **Never assert** Tailwind utility classes (`p-5`, `bg-kidical-red`), raw hex/px, Alpine/JS source strings, SVG path coordinates, or exact long copy sentences. A visual refactor must not break a green test, and a broken page must not keep one green.
- **No observable hook for a variant?** Add a `data-*` seam to the component that encodes *intent* (not styling), then assert the seam — don't reach for the utility class.
- **Altitude:** pure logic → unit test (no DB); "it renders" → one smoke (reuse the `public routes` dataset in `tests/Pest.php`, don't re-list routes); real user flows → a few feature tests. Don't test the framework or Fortify defaults — only app-specific behaviour layered on top.
- **Do NOT delete tests without approval** (Pest rule). Run `vendor/bin/pint --dirty --format agent` after PHP edits.

## Interface Copy

When writing any interface copy for the public site — labels, CTAs, headings, empty states, error messages, onboarding text, tooltips — always follow the Tone of Voice guide at `docs/tone-of-voice.md`.

The guide defines 4 voice qualities: joyful (not frivolous), warm and inclusive (genuinely), local and grounded, committed (not preachy). Register shifts by context — event pages are warm and concrete, partner pages are a notch more serious.

**The one-line test:** does this sound like someone who loves cycling with kids in their neighbourhood and wants you to come along? If not, rewrite.

## Public Site — Frontend Rules

- Headings: use raw `<h1>`–`<h6>`, never `flux:heading`.
  Why: `flux:heading` overrides `@layer base` typography with its own utility classes.

- Other Flux components (`flux:button`, `flux:badge`, `flux:icon.*`, `flux:separator`, `flux:navbar`, `flux:text`) are fine on the public site.

- Styling has three layers — put each decision in exactly one (test: am I styling a *thing* or *placing* things?):
  - **Tokens** (`@theme` + `@layer base`): colour, type scale, radius, shadow, link/heading defaults. Never a raw hex/px anywhere — use the token.
  - **Components** (`resources/views/components/*.blade.php`): a reusable unit's appearance **and** internal spacing, written as token-backed Tailwind utilities baked into the component markup (e.g. `<x-feature-card>` → `bg-white rounded-card shadow-card p-10`). This is the single source of truth for that unit's look; there is no `app.css` entry for it. Appearance utilities are expected here, but must reference tokens (`bg-kidical-*`, `rounded-card`, `shadow-card`), never raw values.
  - **Composition** (page Blade templates): how units are *placed* — section gaps, margins, grid/flex, alignment, widths, order. Keep: `grid`, `flex`, `gap-*`, `p-*`, `m-*`, `max-w-*`, `overflow-*`, `aspect-*`, `object-*`. Still strip appearance utilities (`bg-*`, `text-{color}`, `font-*`, `shadow-*`, `rounded-*`, …) and BEM layout scaffolding from page templates.
  Why: reusable appearance lives in the component (collision-proof, self-contained); page layout stays freely editable in the template. `app.css` stops growing per-page — new entries only for genuinely global styles (footer, nav, prose) or complex effects no single component owns. Worked example: `<x-feature-card>` (used on getting-started + about/mission). See `docs/superpowers/specs/2026-06-05-styling-architecture-design.md`.

- When CSS *does* live in a stylesheet (not yet absorbed into a component's `.blade.php`), it goes in a **role-based partial under `resources/css/`**, never piled into `app.css`:
  - **Reusable across pages** → `resources/css/components/<role>.css` (e.g. `cta-button.css`, `event-card.css`, `location-picker.css`).
  - **Appears on one page only** → `resources/css/pages/<page>.css` (e.g. `about.css`, `calendar.css`).
  - **Global shell** (footer/nav/page frame) → `resources/css/chrome.css`; **cross-cutting effects** (keyframes/reduced-motion) → `resources/css/effects.css` (imported last).
  - `app.css` holds ONLY `@theme` tokens, `@layer base`, and the `@import` block. Each partial keeps its rules in the same `@layer` they belong to.
  - Classification rule when unsure: default to `components/`.
  - Enforced by `tests/Feature/CssArchitectureTest.php` (partials must be registered; no raw hex/px in `.blade.php` components). Run `php artisan test --filter=CssArchitectureTest`. Design: `docs/superpowers/specs/2026-06-06-css-partials-architecture-design.md`.

- Typographic scale (size, weight, line-height) is defined once in `@layer base` in `app.css`. Never set these inline on headings.

- Metadata key-value pairs → `<dl><dt><dd>`.
- Dates → `<time datetime="ISO8601">`.
- Decorative icons → `aria-hidden="true"`.
- `<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">` — never hardcoded.

## Updating the build pipeline (page status)

The pipeline tracker is the **page registry (`P-nn`) table in [`docs/wiki/design/30-skeleton/00-page-registry.md`](docs/wiki/design/30-skeleton/00-page-registry.md)** — rendered read-only at the `/build` dashboard (`build.dashboard`, non-prod, unlinked). The dashboard *parses* this markdown; the table is the source of truth. Run `/pipeline` to do this guided; the steps below are what it follows.

- **Phase columns:** `UX · Conf · Wire · Assets · UI · Back · OK`. Stage emoji (parsed by `app/Support/Build/Stage.php`): 🔴 niet begonnen · 🟠 bezig · 🟢 goed · ⚪ n.v.t. · ❓ te beslissen. `Conf` = content-confidence `1–5`; `OK` is binary.
- **Stage meaning** (so a bump is honest): **Wire 🟢** only when the view actually renders and is visually verified — *and* Frederik has done his own critique + refine pass (Claude's render/tone check tops out at 🟠); **Assets** = media sourced (⚪ when the page needs none); **UI** = brand/surface pass; **Back 🟢** only when the data/CMS is wired *and verified live*, not merely coded; **OK** = client sign-off (don't set early).
- **One update touches four things, in order:**
  1. The **row** — change the stage emoji(s). Keep all 12 columns intact or the dashboard drops the row.
  2. The **Top gaps** cell — delete gaps now resolved; add a terse "X live" note (match existing style, tags `[content]`/`[asset]`/`[strategy]`/`[client]`/`[research]`).
  3. The **Roll-up** prose below the table — keep the aggregate counts/lists consistent with the row you changed.
  4. Append a **`## [YYYY-MM-DD] build | …`** entry to [`docs/wiki/log.md`](docs/wiki/log.md).
- **Verify before claiming done:** load `/build`, or run `app(App\Support\Build\BuildStatus::class)->report()` via tinker, and confirm the row's stages parse as intended, `warnings` is empty, and there's no unexpected `drift`.
