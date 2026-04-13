# Website Scrape — Design Spec

**Date:** 2026-04-13
**Branch:** docs/llm-wiki
**Status:** Approved

---

## Overview

Crawl the full kidicalmass.be website (a Wix SPA) and save every page as clean markdown to `docs/raw/website/`, with all images downloaded to `docs/raw/website/assets/`. The scraped files become the verbatim source for subsequent wiki ingest.

---

## Output Structure

```
docs/raw/website/
  index.md                      ← kidicalmass.be/
  agenda.md                     ← /agenda
  volunteer.md                  ← /volunteer
  le-projet-het-project.md      ← /le-projet-het-project
  …                             ← one .md per crawled page, slug from URL path
  assets/
    <filename>.<ext>            ← all images downloaded from crawled pages
```

Each `.md` file has frontmatter followed by full page content as clean markdown:

```yaml
---
url: https://www.kidicalmass.be/agenda
scraped: 2026-04-13
---
```

Image references in the markdown are rewritten to point to `./assets/<filename>`.

---

## Crawler Script

**Location:** `/tmp/scrape-website.cjs` (standalone, not committed to the repo)
**Runtime:** Node.js with Playwright (globally installed) and `turndown` (installed locally in `/tmp/`)

### Behaviour

1. Start at `https://www.kidicalmass.be/`
2. Wait for `networkidle` on each page so Wix JS finishes rendering
3. Extract main content area — skip nav, cookie banners, footer chrome
4. Convert extracted HTML → markdown using `turndown`
5. Find all internal links on the page, add unseen ones to the crawl queue
6. Download all images encountered, save to `docs/raw/website/assets/`
7. Save one `.md` file per page to `docs/raw/website/`
8. Skip external links (Facebook, Google Docs, YouTube, etc.)
9. Log each page crawled to stdout

### Dependencies

- `playwright` — globally installed (`npm install -g playwright`)
- `turndown` — installed locally in `/tmp/node_modules/` before running the script

### Running the script

```bash
cd /tmp && npm install turndown
node /tmp/scrape-website.cjs
```

---

## Post-Crawl Wiki Ingest

The scrape is capture-only. Wiki ingest is a separate follow-up step:

1. Read each `docs/raw/website/*.md`
2. Create new wiki pages in `docs/wiki/` for pages not yet covered
3. Enrich existing wiki pages (e.g. site-audit.md) with verbatim content where relevant
4. Update `docs/wiki/index.md`
5. Append entries to `docs/wiki/log.md`

---

## Out of Scope

- Authenticated pages (none on this site)
- PDF or document downloads
- Video content (YouTube embeds skipped)
- Automated wiki ingest (manual, supervised follow-up)
