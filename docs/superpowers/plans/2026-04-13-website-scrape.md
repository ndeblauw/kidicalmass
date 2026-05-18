# Website Scrape Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Crawl the full kidicalmass.be Wix SPA, save every page as clean markdown and all images to `docs/raw/website/`.

**Architecture:** A standalone Node.js `.cjs` script runs from `/tmp/` (no project dependencies modified). Playwright renders each page fully before extraction. `turndown` converts HTML to markdown. The crawler follows internal links breadth-first, keeping a visited-set to avoid loops.

**Tech Stack:** Node.js (CJS), Playwright (global install), turndown (installed in /tmp/), built-in `https`/`fs`/`path` modules.

---

## File Map

| File | Action | Purpose |
|------|--------|---------|
| `/tmp/scrape-website.cjs` | Create | The crawler script — not committed to the repo |
| `docs/raw/website/` | Create (auto) | Output directory for markdown files |
| `docs/raw/website/assets/` | Create (auto) | Downloaded images |

---

### Task 1: Install dependencies and create output directories

**Files:**
- Create: `/tmp/scrape-website.cjs` (skeleton only in this task)

- [ ] **Step 1: Install turndown in /tmp**

```bash
cd /tmp && npm install turndown
```

Expected output: `added N packages` with no errors.

- [ ] **Step 2: Verify Playwright is available**

```bash
node -e "require('playwright'); console.log('ok')"
```

Expected output: `ok`

- [ ] **Step 3: Create the output directories**

```bash
mkdir -p /Users/frederikvincx/Herd/kidicalmass/docs/raw/website/assets
```

Expected: no error, directories created.

- [ ] **Step 4: Verify the directory exists**

```bash
ls /Users/frederikvincx/Herd/kidicalmass/docs/raw/website/
```

Expected: `assets/`

---

### Task 2: Write the crawler script

**Files:**
- Create: `/tmp/scrape-website.cjs`

- [ ] **Step 1: Write the full script**

Use the Write tool to create `/tmp/scrape-website.cjs` with this content:

```javascript
const { chromium } = require('playwright');
const TurndownService = require('/tmp/node_modules/turndown');
const fs = require('fs');
const path = require('path');
const https = require('https');
const http = require('http');

const BASE_URL = 'https://www.kidicalmass.be';
const OUTPUT_DIR = '/Users/frederikvincx/Herd/kidicalmass/docs/raw/website';
const ASSETS_DIR = path.join(OUTPUT_DIR, 'assets');
const TODAY = new Date().toISOString().split('T')[0];

fs.mkdirSync(OUTPUT_DIR, { recursive: true });
fs.mkdirSync(ASSETS_DIR, { recursive: true });

const turndown = new TurndownService({ headingStyle: 'atx', bulletListMarker: '-' });

const visited = new Set();
const queue = ['/'];

function slugify(urlPath) {
  if (!urlPath || urlPath === '/') return 'index';
  return urlPath
    .replace(/^\//, '')
    .replace(/\/+$/, '')
    .replace(/\//g, '--')
    .replace(/[^a-z0-9-]/gi, '-')
    .toLowerCase();
}

function downloadFile(fileUrl, destPath) {
  return new Promise((resolve, reject) => {
    const proto = fileUrl.startsWith('https') ? https : http;
    const file = fs.createWriteStream(destPath);
    proto.get(fileUrl, (res) => {
      if (res.statusCode === 301 || res.statusCode === 302) {
        file.close();
        fs.unlinkSync(destPath);
        downloadFile(res.headers.location, destPath).then(resolve).catch(reject);
        return;
      }
      res.pipe(file);
      file.on('finish', () => file.close(resolve));
    }).on('error', (err) => {
      fs.unlink(destPath, () => {});
      reject(err);
    });
  });
}

function filenameFromUrl(imgUrl) {
  try {
    const u = new URL(imgUrl);
    const base = path.basename(u.pathname) || 'image';
    // Wix image URLs often have no extension — append .jpg as fallback
    return base.includes('.') ? base : base + '.jpg';
  } catch {
    return 'image-' + Date.now() + '.jpg';
  }
}

async function processImages(page) {
  const imgUrls = await page.evaluate(() =>
    Array.from(document.querySelectorAll('img[src]'))
      .map(img => img.src)
      .filter(src => src && src.startsWith('http'))
  );

  const map = {};
  for (const imgUrl of imgUrls) {
    const filename = filenameFromUrl(imgUrl);
    const destPath = path.join(ASSETS_DIR, filename);
    if (!fs.existsSync(destPath)) {
      try {
        await downloadFile(imgUrl, destPath);
        console.log(`  img: ${filename}`);
      } catch (e) {
        console.warn(`  img FAILED: ${imgUrl} — ${e.message}`);
      }
    }
    map[imgUrl] = `./assets/${filename}`;
  }
  return map;
}

async function extractLinks(page) {
  return page.evaluate((base) => {
    return Array.from(document.querySelectorAll('a[href]'))
      .map(a => {
        try {
          const href = a.getAttribute('href');
          if (!href) return null;
          if (href.startsWith('/')) return href.split('#')[0];
          const u = new URL(href, base);
          if (u.origin === base) return u.pathname.split('#')[0];
          return null;
        } catch { return null; }
      })
      .filter(Boolean);
  }, BASE_URL);
}

async function extractContent(page, imgMap) {
  // Remove nav, header, footer, cookie banners, and other chrome
  await page.evaluate(() => {
    const selectors = [
      'nav', 'header', 'footer',
      '[role="navigation"]', '[role="banner"]',
      '[data-testid="cookie-banner"]',
      '[class*="cookie"]', '[id*="cookie"]',
      '[class*="header"]', '[id*="header"]',
      '[class*="footer"]', '[id*="footer"]',
      '[class*="nav"]', '[id*="nav"]',
    ];
    selectors.forEach(sel => {
      document.querySelectorAll(sel).forEach(el => el.remove());
    });
  });

  let html = await page.evaluate(() => document.body.innerHTML);

  // Rewrite image src to local asset paths
  for (const [original, local] of Object.entries(imgMap)) {
    html = html.split(original).join(local);
  }

  return turndown.turndown(html);
}

(async () => {
  const browser = await chromium.launch();
  const context = await browser.newContext({ ignoreHTTPSErrors: true });
  const page = await context.newPage();

  while (queue.length > 0) {
    const urlPath = queue.shift();
    if (visited.has(urlPath)) continue;
    visited.add(urlPath);

    const fullUrl = BASE_URL + urlPath;
    console.log(`\nCrawling: ${fullUrl}`);

    try {
      await page.goto(fullUrl, { waitUntil: 'networkidle', timeout: 30000 });
    } catch (e) {
      console.warn(`  FAILED to load: ${e.message}`);
      continue;
    }

    // Discover links before removing chrome
    const links = await extractLinks(page);
    for (const link of links) {
      if (!visited.has(link) && !queue.includes(link)) {
        queue.push(link);
      }
    }

    // Download images
    const imgMap = await processImages(page);

    // Extract and convert content
    const markdown = await extractContent(page, imgMap);

    const slug = slugify(urlPath);
    const outPath = path.join(OUTPUT_DIR, `${slug}.md`);
    const content = `---\nurl: ${fullUrl}\nscraped: ${TODAY}\n---\n\n${markdown}\n`;
    fs.writeFileSync(outPath, content);
    console.log(`  saved: ${slug}.md`);
  }

  await browser.close();
  console.log(`\nDone. Pages crawled: ${visited.size}`);
  console.log(`Output: ${OUTPUT_DIR}`);
})();
```

- [ ] **Step 2: Verify the file was written**

```bash
wc -l /tmp/scrape-website.cjs
```

Expected: a number above 100.

---

### Task 3: Run the crawler

**Files:**
- Populate: `docs/raw/website/*.md`
- Populate: `docs/raw/website/assets/*`

- [ ] **Step 1: Run the script**

```bash
node /tmp/scrape-website.cjs
```

Watch stdout. Each page prints `Crawling: <url>` then `saved: <slug>.md`. Let it run to completion. Expected final line: `Done. Pages crawled: N`.

- [ ] **Step 2: Check how many pages were saved**

```bash
ls /Users/frederikvincx/Herd/kidicalmass/docs/raw/website/*.md | wc -l
```

Expected: at least 4 (home, agenda, volunteer, mission). If 0 or 1, the Wix JS may not have rendered — see troubleshooting below.

- [ ] **Step 3: Check images were downloaded**

```bash
ls /Users/frederikvincx/Herd/kidicalmass/docs/raw/website/assets/ | wc -l
```

Expected: at least 5 images.

- [ ] **Step 4: Spot-check one page for content quality**

```bash
head -40 /Users/frederikvincx/Herd/kidicalmass/docs/raw/website/index.md
```

Expected: frontmatter block, then markdown with headings and readable text. If the output is mostly empty or just nav links, see troubleshooting.

---

### Task 4: Troubleshoot if content is thin (only if Step 3 spot-check fails)

Wix can be stubborn with `networkidle`. Try increasing the wait:

- [ ] **Step 1: Edit the goto call in the script to use a longer timeout and explicit wait**

In `/tmp/scrape-website.cjs`, replace:

```javascript
await page.goto(fullUrl, { waitUntil: 'networkidle', timeout: 30000 });
```

with:

```javascript
await page.goto(fullUrl, { waitUntil: 'networkidle', timeout: 60000 });
await page.waitForTimeout(3000);
```

- [ ] **Step 2: Clear output and re-run**

```bash
rm -rf /Users/frederikvincx/Herd/kidicalmass/docs/raw/website/*.md
node /tmp/scrape-website.cjs
```

- [ ] **Step 3: Spot-check again**

```bash
head -40 /Users/frederikvincx/Herd/kidicalmass/docs/raw/website/index.md
```

---

### Task 5: Commit the scraped content

- [ ] **Step 1: List all new files**

```bash
git -C /Users/frederikvincx/Herd/kidicalmass status --short docs/raw/website/
```

- [ ] **Step 2: Stage and commit**

```bash
cd /Users/frederikvincx/Herd/kidicalmass && git add docs/raw/website/ && git commit -m "$(cat <<'EOF'
docs: scrape kidicalmass.be website verbatim into docs/raw/website/

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>
EOF
)"
```

Expected: commit created listing the new `.md` files and assets.

---

## Self-Review

**Spec coverage:**
- ✅ Start at `/`, follow all internal links — Task 3
- ✅ Wait for `networkidle` — Task 2 (goto options)
- ✅ Skip nav/footer chrome — Task 2 (`extractContent`)
- ✅ HTML → markdown via turndown — Task 2
- ✅ Download images to `assets/` — Task 2 (`processImages`)
- ✅ Rewrite image refs to `./assets/` — Task 2 (`extractContent`)
- ✅ Skip external links — Task 2 (`extractLinks` filters to same origin)
- ✅ Frontmatter with `url` and `scraped` — Task 2 (content assembly)
- ✅ One `.md` per page, slug from URL path — Task 2 (`slugify`)
- ✅ Log each page to stdout — Task 2 (`console.log`)
- ✅ Post-crawl wiki ingest is out of scope — noted in spec, no task needed

**Placeholder scan:** None found.

**Type/name consistency:** `slugify`, `downloadFile`, `filenameFromUrl`, `processImages`, `extractLinks`, `extractContent` — all defined in Task 2 and used only in Task 2's script.
