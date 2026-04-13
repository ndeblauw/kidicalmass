# LLM Wiki — Design Spec

**Date:** 2026-04-13
**Branch:** docs/llm-wiki
**Status:** Approved

---

## Overview

Set up a persistent, LLM-maintained wiki inside the KidicalMass project, covering both organisational knowledge (chapters, content strategy, brand, stakeholders) and technical knowledge (architecture, domain model, decisions). The wiki compounds over time — sources are ingested once, synthesised into structured pages, and kept current as new sources arrive.

---

## Directory Structure

```
docs/
  raw/                    # immutable source files (never modified by LLM)
    assets/               # downloaded images referenced by sources
    chunks/               # script-generated chunk files (auto-created by ingest script)
  wiki/                   # LLM-maintained markdown pages
    index.md              # catalog: every page with one-line summary and category
    log.md                # append-only chronological log of all wiki operations
    tone-of-voice.md      # migrated from docs/tone-of-voice.md
    ux/                   # migrated from docs/ux/
    …                     # pages grow from here
  superpowers/            # untouched — specs and plans live here
```

`docs/tone-of-voice.md` is moved to `docs/wiki/tone-of-voice.md`. The CLAUDE.md reference is updated accordingly.

---

## CLAUDE.md Schema Section

A `=== wiki rules ===` section is added to `CLAUDE.md` with the following conventions:

### Ingest workflow
1. Run `node scripts/ingest.cjs <file>` to extract and chunk the source.
2. Read chunks one at a time and discuss key takeaways.
3. Write a summary page in `docs/wiki/`.
4. Update `docs/wiki/index.md`.
5. Update relevant existing wiki pages (cross-references, contradictions, new data).
6. Append an entry to `docs/wiki/log.md`.

### Page conventions
- YAML frontmatter: `title`, `tags`, `sources` (list of raw filenames), `updated` (ISO date).
- Cross-links use standard markdown: `[Page Title](relative-path.md)`.
- One concept or entity per page.

### index.md format
Table with columns: `Page`, `Summary`, `Category`. Updated on every ingest and lint.

### log.md format
Append-only. Each entry prefixed `## [YYYY-MM-DD] <operation> | <title>` where operation is `ingest`, `query`, or `lint`. This makes entries greppable with `grep "^## \[" docs/wiki/log.md`.

### Query workflow
1. Read `docs/wiki/index.md` to find relevant pages.
2. Drill into relevant pages.
3. Synthesise answer with citations.
4. If the answer is valuable (comparison, analysis, new connection), file it back as a new wiki page.

### Lint workflow
Periodically check for: contradictions between pages, stale claims superseded by newer sources, orphan pages (no inbound links), concepts mentioned but lacking their own page, missing cross-references, data gaps.

---

## Ingestion Script

**Location:** `scripts/ingest.cjs`
**Usage:** `node scripts/ingest.cjs <file-path>`

### Behaviour
- Accepts `.md` and `.txt` files (plain text only — no PDF parsing).
- Chunks output into ~2000-word segments with ~200-word overlap to preserve context at boundaries.
- Writes chunks to `docs/raw/chunks/<source-filename>/chunk-01.md`, `chunk-02.md`, etc.
- Single-chunk output if source is < 2000 words (same path pattern for consistency).
- Prints summary to stdout: source file, total word count, number of chunks, output paths.

### Dependencies
- Node built-ins only (`fs`, `path`) — no npm dependencies.

---

## Migration

Existing docs migrated into `docs/wiki/` on this branch:
- `docs/tone-of-voice.md` → `docs/wiki/tone-of-voice.md`
- `docs/ux/` → `docs/wiki/ux/`

CLAUDE.md reference updated: `docs/tone-of-voice.md` → `docs/wiki/tone-of-voice.md`.

---

## Notion Content Migration

The existing Notion project page "Kidical Mass Belgium — Website Project" is ingested verbatim as the initial set of wiki pages. All sub-pages are fetched and converted to markdown, **excluding** the "Look & feel examples" page.

Sub-pages to migrate:
- Meeting Notes
- Desk Research Plan & Findings
- Service Design Overview
- Key Decisions — Events, Chapters, Volunteers, Sponsors
- Tone of Voice Guide — Kidical Mass Belgium
- Strategie & plan — Nieuwe website Kidical Mass Belgium
- UX planning
- Deep Site Audit — Current Wix Site

Each page is saved to `docs/raw/notion/<slug>.md` as the immutable source, and also written verbatim to `docs/wiki/<slug>.md` as the initial wiki page. The `docs/wiki/index.md` and `docs/wiki/log.md` are seeded with entries for each migrated page.

---

## Out of Scope

- Embedding-based search / RAG infrastructure (use `index.md` at this scale).
- Automated ingestion pipelines — all ingests are manual and supervised.
- Obsidian-specific tooling (Dataview, Marp) — optional, user's choice.
