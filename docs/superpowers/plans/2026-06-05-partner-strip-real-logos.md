# Partner Strip Real Logos Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the random stock-photo "logos" in the site-wide partner recognition strip with real partner logos, with a graceful text-chip fallback so no stock photo can ever appear again.

**Architecture:** Curate real logo files under `public/img/partners/logos/raw/{slug}.{png|svg}` (slug = `Str::slug($partner->name)`). The `PartnerFactory` attaches the matching file to each partner's `logo` media collection at seed time, attaching nothing when no file exists. The strip blade renders an `<img>` when a logo exists and a quiet name chip when it doesn't, and excludes Brussel Mobiliteit from the loop (it is already the hardcoded lead logo).

**Tech Stack:** Laravel 12, Pest 4, Spatie media-library, Tailwind v4 / `app.css`, `curl` + ImageMagick/`sips` for logo acquisition.

---

## File Structure

- **Create:** `public/img/partners/logos/raw/{slug}.png|svg` — curated logo files (committed truth).
- **Modify:** `database/factories/PartnerFactory.php` — attach real logo by slug; drop random-image path.
- **Modify:** `resources/views/components/partners.blade.php` — chip fallback + exclude BM from loop.
- **Modify:** `resources/css/app.css` — add `.partner-strip__chip`.
- **Create:** `tests/Feature/PartnerStripComponentTest.php` — render assertions.

## Reference data — national partners (chapter = null)

| Name | slug | domain (Clearbit) | source |
|------|------|-------------------|--------|
| Brussel Mobiliteit | `brussel-mobiliteit` | — | already exists, hardcoded lead — **excluded from loop** |
| Pro Velo | `pro-velo` | provelo.org | fetch |
| Cyclo | `cyclo` | cyclo.org | fetch |
| Fietsersbond | `fietsersbond` | fietsersbond.be | fetch |
| GRACQ | `gracq` | gracq.org | fetch |
| Clean Cities | `clean-cities` | cleancitiescampaign.org | fetch |
| Heroes for Zero | `heroes-for-zero` | heroesforzero.be | fetch |
| Fietsbieb | `fietsbieb` | fietsbieb.be | fetch |
| BRUZZ | `bruzz` | bruzz.be | fetch |
| Growfunding | `growfunding` | growfunding.be | fetch |
| My Kids Bikes | `my-kids-bikes` | — (no url) | slice from wall |
| Succulente | `succulente` | — (no url) | slice from wall |
| Les Chercheurs d'Air | `les-chercheurs-dair` | — (no url) | slice from wall |
| Park Poetik | `park-poetik` | — (no url) | slice from wall |

> Note: `Str::slug("Les Chercheurs d'Air")` = `les-chercheurs-dair` (apostrophe dropped, **no** dash before "air"). Avello is a `bergen`-local partner (`group_id` set) and never appears on this national strip.

---

### Task 1: Acquire logo files

**Files:**
- Create: `public/img/partners/logos/raw/` (directory) + `{slug}.png|svg` files
- Create (throwaway): `/tmp/fetch-partner-logos.sh`

- [ ] **Step 1: Create the target directory**

Run:
```bash
mkdir -p public/img/partners/logos/raw
```

- [ ] **Step 2: Write the fetch script**

Create `/tmp/fetch-partner-logos.sh` (use the Write tool, then run with bash — do not heredoc):

```bash
#!/usr/bin/env bash
set -u
DEST="public/img/partners/logos/raw"
# slug:domain pairs for partners that have a website
PAIRS="
pro-velo:provelo.org
cyclo:cyclo.org
fietsersbond:fietsersbond.be
gracq:gracq.org
clean-cities:cleancitiescampaign.org
heroes-for-zero:heroesforzero.be
fietsbieb:fietsbieb.be
bruzz:bruzz.be
growfunding:growfunding.be
"
for pair in $PAIRS; do
  slug="${pair%%:*}"
  domain="${pair##*:}"
  out="$DEST/$slug.png"
  url="https://logo.clearbit.com/$domain?size=256&format=png"
  code=$(curl -sL -o "$out" -w "%{http_code}" "$url")
  size=$(wc -c < "$out" 2>/dev/null | tr -d ' ')
  if [ "$code" = "200" ] && [ "${size:-0}" -gt 1000 ]; then
    echo "fetched  $slug  (${size}B)"
  else
    rm -f "$out"
    echo "FAILED   $slug  (http $code, ${size:-0}B) -> needs scrape or wall-slice"
  fi
done
```

- [ ] **Step 3: Run the fetch script**

Run:
```bash
bash /tmp/fetch-partner-logos.sh
```
Expected: a line per partner reading `fetched <slug>` or `FAILED <slug> ...`. Note which slugs FAILED — they move to Step 5 (scrape) or Task 1b (wall-slice).

- [ ] **Step 4: Visually inspect each fetched logo**

Read each `public/img/partners/logos/raw/*.png` (use the Read tool — it renders images). For each, judge: is it the right brand mark, not a generic favicon or placeholder? Per the spec's Q2 decision, **keep mediocre logos** (wrong crop / coloured background) but record them as `flagged-mediocre` for the coverage report. Delete only files that are clearly wrong (404 image, unrelated icon) and treat those slugs as `failed`.

- [ ] **Step 5: Scrape fallback for FAILED url'd partners**

For any url'd partner that failed Clearbit, fetch the homepage and locate the logo:
```bash
# example for a failed slug; replace URL
curl -sL "https://www.provelo.org" | grep -ioE '<img[^>]+(logo|brand)[^>]*>|<svg[^>]*logo' | head
```
Download the discovered logo URL into `public/img/partners/logos/raw/{slug}.{png|svg}` with `curl -sL -o`. If nothing usable is found, treat the slug as `failed`.

- [ ] **Step 6: Commit the fetched logos**

```bash
git add public/img/partners/logos/raw
git commit -m "feat(partners): add fetched national partner logos"
```

---

### Task 1b: Slice no-url logos from the 2024 wall

**Files:**
- Source: `public/img/partners/partner-logos-2024.png`
- Create: `public/img/partners/logos/raw/{my-kids-bikes,succulente,les-chercheurs-dair,park-poetik}.png` (only those present on the wall)

- [ ] **Step 1: View the wall to locate logos**

Read `public/img/partners/partner-logos-2024.png` (the Read tool renders it). Identify which of the four no-url partners (My Kids Bikes, Succulente, Les Chercheurs d'Air, Park Poetik) are present and note each logo's pixel bounding box (x, y, width, height). Also note the wall's full pixel dimensions:
```bash
sips -g pixelWidth -g pixelHeight public/img/partners/partner-logos-2024.png
```

- [ ] **Step 2: Crop each present logo**

For each located logo, crop with ImageMagick (offset is `+x+y`):
```bash
magick public/img/partners/partner-logos-2024.png -crop WxH+X+Y +repage public/img/partners/logos/raw/<slug>.png
```
If `magick` is unavailable, use `sips --cropOffset Y X` / `--cropToHeightWidth H W` instead. Verify each crop by reading the output PNG.

- [ ] **Step 3: Record coverage**

Produce the **coverage report** — a short list printed to the user, one line per national partner (excluding BM): `fetched` / `sliced` / `flagged-mediocre` / `failed`. `failed` partners have no file and will render the text chip (Task 3). This report is the hand-off list of logos Frederik should later replace by hand.

- [ ] **Step 4: Commit the sliced logos**

```bash
git add public/img/partners/logos/raw
git commit -m "feat(partners): slice no-url partner logos from 2024 wall"
```

---

### Task 2: Rewire PartnerFactory to attach real logos

**Files:**
- Modify: `database/factories/PartnerFactory.php`
- Test: `tests/Feature/PartnerStripComponentTest.php` (created here, extended in Task 3)

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/PartnerStripComponentTest.php`:

```php
<?php

use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('attaches the real logo file to a partner whose slug has a logo', function () {
    // pro-velo.png is committed in Task 1; assumes the file exists.
    expect(file_exists(public_path('img/partners/logos/raw/pro-velo.png')))->toBeTrue();

    $partner = Partner::factory()->create([
        'name' => 'Pro Velo',
        'group_id' => null,
        'show_logo' => true,
        'visible' => true,
    ]);

    expect($partner->getFirstMediaUrl('logo'))->not->toBe('');
    expect($partner->getFirstMediaUrl('logo'))->not->toContain('picsum');
});

it('attaches no logo when no matching file exists', function () {
    $partner = Partner::factory()->create([
        'name' => 'Totally Fake Org That Has No Logo',
        'group_id' => null,
        'show_logo' => true,
        'visible' => true,
    ]);

    expect($partner->getFirstMediaUrl('logo'))->toBe('');
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter=PartnerStripComponentTest`
Expected: FAIL — the factory currently attaches a random `picsum` image, so the second test ("no logo") fails (a logo IS attached), and the first may attach a stock photo whose URL won't contain `picsum` only by luck. Confirm red.

- [ ] **Step 3: Rewrite `attachImage` in `PartnerFactory.php`**

Replace the `configure()` + `attachImage()` methods (lines ~43-55) and remove the now-unused `AttachesMediaFromCache` trait, `MediaSeeder` import, and `Concerns\AttachesMediaFromCache` import. The class becomes:

```php
<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    public function definition(): array
    {
        $companyNames = [
            'Cyclo', 'Farm', 'MonkeyDonkey', 'GRC', 'Ride', 'REM Brussel',
            'Citizens Action', 'Heroes for Zero', 'Kids Beschik', 'Ketje',
            'Pro Velo', 'My Kids Bikes', 'Velokanik', 'Fiets FEB',
            'EUCyclo', 'Velophil', 'Angel of Care', 'Gracy',
            'Fietsersbond', 'Bike4Brussels', 'Brussels Mobiliteit',
        ];

        $name = $companyNames[array_rand($companyNames)] ?? fake()->company();

        return [
            'name' => $name,
            'url' => fake()->url(),
            'description_nl' => fake()->paragraphs(2, true),
            'description_fr' => fake()->paragraphs(2, true),
            'show_logo' => fake()->boolean(90),
            'visible' => fake()->boolean(95),
            'group_id' => Group::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Partner $partner): void {
            $this->attachLogo($partner);
        });
    }

    /**
     * Attach the curated logo file matching the partner's slug, if one exists.
     * No file -> no logo media (the strip renders a name chip instead). This
     * guarantees a stock photo can never be attached as a logo.
     */
    protected function attachLogo(Partner $partner): void
    {
        $slug = Str::slug($partner->name);
        $matches = File::glob(public_path("img/partners/logos/raw/{$slug}.*"));

        if (empty($matches)) {
            return;
        }

        try {
            $partner->addMedia($matches[0])->preservingOriginal()->toMediaCollection('logo');
        } catch (\Exception $e) {
        }
    }
}
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php artisan test --compact --filter=PartnerStripComponentTest`
Expected: PASS (both tests green).

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add database/factories/PartnerFactory.php tests/Feature/PartnerStripComponentTest.php
git commit -m "feat(partners): attach curated logo by slug, drop stock-photo logos"
```

---

### Task 3: Strip blade — chip fallback + exclude Brussel Mobiliteit

**Files:**
- Modify: `resources/views/components/partners.blade.php`
- Modify: `resources/css/app.css`
- Test: `tests/Feature/PartnerStripComponentTest.php` (extend)

- [ ] **Step 1: Write the failing tests (append to the test file)**

Append to `tests/Feature/PartnerStripComponentTest.php`:

```php
it('renders a name chip for a logo-less partner instead of a gap', function () {
    Partner::factory()->create([
        'name' => 'Totally Fake Org That Has No Logo',
        'url' => null,
        'group_id' => null,
        'show_logo' => true,
        'visible' => true,
    ]);

    $this->blade('<x-partners />')
        ->assertSee('partner-strip__chip', false)
        ->assertSee('Totally Fake Org That Has No Logo');
});

it('never renders a stock photo url in the strip', function () {
    Partner::factory()->count(3)->create([
        'group_id' => null,
        'show_logo' => true,
        'visible' => true,
    ]);

    $this->blade('<x-partners />')->assertDontSee('picsum');
});

it('renders Brussel Mobiliteit exactly once', function () {
    Partner::factory()->create([
        'name' => 'Brussel Mobiliteit',
        'group_id' => null,
        'show_logo' => true,
        'visible' => true,
    ]);

    $html = $this->blade('<x-partners />')->value ?? (string) $this->blade('<x-partners />');
    expect(substr_count((string) $html, 'Brussel Mobiliteit'))->toBe(1);
});
```

> If `$this->blade(...)->value` is not available in this Pest/Laravel version, capture HTML with `(string) view(...)` is not possible for an anonymous component — instead use `Illuminate\Support\Facades\Blade::render('<x-partners />')` to get the raw string for the count assertion. Adjust the last test to:
> ```php
> $html = Blade::render('<x-partners />');
> expect(substr_count($html, 'Brussel Mobiliteit'))->toBe(1);
> ```
> with `use Illuminate\Support\Facades\Blade;` at the top.

- [ ] **Step 2: Run to verify failure**

Run: `php artisan test --compact --filter=PartnerStripComponentTest`
Expected: FAIL — the chip class does not exist yet, and BM currently renders via the hardcoded lead **plus** the loop (count = 2).

- [ ] **Step 3: Update the component query + loop**

Edit `resources/views/components/partners.blade.php`. Change the query to reject BM, and replace the loop body to render a chip when there is no logo URL.

Query block (replace the `$partners = ...->get();` statement):
```php
$partners = \App\Models\Partner::query()
    ->whereNull('group_id')
    ->where('visible', true)
    ->where('show_logo', true)
    ->get()
    ->reject(fn ($p) => \Illuminate\Support\Str::slug($p->name) === 'brussel-mobiliteit');
```

Loop block (replace the existing `@foreach($partners as $partner) ... @endforeach`):
```blade
@foreach($partners as $partner)
    @php $logoUrl = $partner->getFirstMediaUrl('logo', 'partner'); @endphp
    @if($logoUrl)
        @if($partner->url)
            <a href="{{ $partner->url }}" target="_blank" rel="noopener noreferrer" title="{{ $partner->name }}" class="partner-strip__logo-link">
                <img src="{{ $logoUrl }}" alt="{{ $partner->name }}" class="partner-strip__logo">
            </a>
        @else
            <img src="{{ $logoUrl }}" alt="{{ $partner->name }}" class="partner-strip__logo">
        @endif
    @else
        @if($partner->url)
            <a href="{{ $partner->url }}" target="_blank" rel="noopener noreferrer" class="partner-strip__chip">{{ $partner->name }}</a>
        @else
            <span class="partner-strip__chip">{{ $partner->name }}</span>
        @endif
    @endif
@endforeach
```

- [ ] **Step 4: Add the chip styles to `app.css`**

Insert after the `.partner-strip__logo--bm { ... }` rule (around line 1204):
```css
.partner-strip__chip {
    display: inline-flex;
    align-items: center;
    height: 2.25rem;
    padding-inline: 0.75rem;
    font-size: var(--text-sm);
    font-weight: 600;
    white-space: nowrap;
    color: rgba(255, 255, 255, 0.85);
    background-color: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 0.375rem;
    background-image: none;
}

a.partner-strip__chip {
    transition: opacity 0.2s;

    &:hover {
        opacity: 0.75;
        color: rgba(255, 255, 255, 0.85);
        background-image: none;
    }
}
```

- [ ] **Step 5: Run to verify pass**

Run: `php artisan test --compact --filter=PartnerStripComponentTest`
Expected: PASS (all tests green).

- [ ] **Step 6: Build assets + commit**

```bash
npm run build
vendor/bin/pint --dirty --format agent
git add resources/views/components/partners.blade.php resources/css/app.css tests/Feature/PartnerStripComponentTest.php public/build
git commit -m "feat(partners): name-chip fallback + de-dupe Brussel Mobiliteit in strip"
```

---

### Task 4: Re-seed and verify in the browser

**Files:** none (verification only)

- [ ] **Step 1: Re-seed partners**

Run:
```bash
php artisan migrate:fresh --seed
```
Expected: seeding completes without error.

- [ ] **Step 2: Verify the strip renders real logos**

Take a screenshot of any public page (the strip sits in the site layout, above the footer) using the project's `scripts/screenshot.cjs` helper or a `/tmp/*.cjs` Playwright script (HTTPS self-signed → `ignoreHTTPSErrors: true`; `.cjs` extension). Confirm: real logos render, no nature/city stock photos, logo-less partners show a quiet chip, Brussel Mobiliteit appears once.

- [ ] **Step 3: Full test run**

Run: `php artisan test --compact`
Expected: green (no regressions in the existing suite).

- [ ] **Step 4: Present the coverage report**

Show Frederik the final coverage report from Task 1b Step 3 (fetched / sliced / flagged-mediocre / failed), so he knows which logos to hand-replace.

---

## Notes for the executor

- **Shared working tree:** Nico commits concurrently in this checkout. Stage only the explicit paths listed in each commit; never `git add -A`; do not push `main`.
- **Public repo:** logos are real partner brand marks the org already displays (the partners page commits a logo wall), consistent with existing practice.
- **Monochrome is out of scope.** Store full-colour originals in `raw/` only; a `mono/` pass is a separate future exploration.
