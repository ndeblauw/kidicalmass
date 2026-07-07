# Build review mode — `/build/review` (2026-07-07)

## Purpose

Frederik's critique pass is the gate for `Wire`/`UI` 🟢, but doing it today means
tab-juggling pages and hand-editing the registry markdown. This adds a
**split review mode**: walk every P-nn page in one sitting, see the live page,
bump statuses, and drop feedback notes — with the registry markdown staying the
single source of truth.

## Flow

- Route `build/review/{pageId?}` (named `build.review`), sibling of `/build`,
  registered under the same non-prod gate as `build.dashboard`. Unlinked from
  the public site; linked from the `/build` dashboard header.
- **Left pane:** the live page in a same-origin iframe. Session cookies carry
  over, so auth-gated pages (P-07 login, P-09 roze-hesjes) render once logged
  in in that browser. A desktop/mobile width toggle constrains the iframe
  container.
- **Right sidebar:** the page's registry row with every stage column editable
  (`UX`, `Wire`, `Assets`, `UI`, `Back` cycle 🔴→🟠→🟢→⚪→❓; `Conf` is a 1–5
  input; `OK` toggles 🔴/🟢), a feedback textarea, and prev/next walking the
  P-nn rows in registry order. Save & next is the primary action.
- Pages whose slug is a template (`/events/[slug]`, `/chapters/[postal-code]`,
  P-09) get a representative instance URL resolved via `config/build.php`
  (e.g. first upcoming published event; Schaarbeek `1030`). P-21 admin and
  rows without a routable URL show a "no preview" placeholder in the pane.

## Writes (all on Save)

1. **Registry row** — new `App\Support\Build\RegistryWriter` locates the
   `| P-nn |` line in `docs/wiki/design/30-skeleton/00-page-registry.md`,
   splits on `|`, replaces only the changed stage cells, rejoins. It **refuses
   to write** (visible error, no partial write) if the line's column count
   doesn't match the expected 12-column shape. Top gaps cells are never
   touched.
2. **Feedback inbox** — notes append to
   `docs/wiki/design/30-skeleton/review-inbox.md` under
   `## [YYYY-MM-DD] P-nn Page name` headings. This file is the punch list a
   later Claude thread works through; it is working notes, not wiki prose.
3. **`docs/wiki/log.md`** — one `## [YYYY-MM-DD] build | review session`
   entry per day; a terse line appended per page touched (statuses changed,
   note left y/n).

## The honesty gap, made explicit

The CLAUDE.md pipeline procedure touches four things; this tool automates the
row + log only. **Top gaps cells and the Roll-up prose are curated writing and
are deliberately left alone**, so after a session the roll-up may lag the rows.
The UI shows a persistent "reconcile pending — run `/pipeline`" hint once any
row has been written; the reconcile step (Claude, later) folds the inbox
feedback + row changes into Top gaps and the Roll-up.

## Guard rails

- Route only registered in non-prod (same condition as `build.dashboard`)
  **and** `RegistryWriter` throws if `app()->environment('production')` —
  belt and braces around file writes.
- Writes are plain file edits on main, visible in `git diff`; nothing
  auto-commits (shared working tree with Nico).
- Stage emoji vocabulary comes from `App\Support\Build\Stage` — no second
  emoji list.

## Components

| Unit | Responsibility |
|---|---|
| `RegistryWriter` (`app/Support/Build/`) | Cell-level markdown row edits + inbox/log appends; refuses malformed rows; env guard |
| `BuildReview` Livewire component | Sidebar state, save action, prev/next, validation |
| `resources/views/build/review.blade.php` | Split layout, iframe, width toggle |
| `config/build.php` additions | Representative-instance URL map for template slugs |

## Testing

- Unit: `RegistryWriter` — replacing one cell round-trips the file
  byte-identical except that cell; malformed column count → refused, file
  untouched; inbox + log appends create-or-append correctly.
- Feature (Livewire): save a status change + note → registry row updated,
  inbox appended; navigation walks registry order.
- Per the testing rubric: assert behaviour and `data-*` seams, no utility
  classes; reuse existing route-smoke conventions.

## Out of scope

- Auto-editing Top gaps / Roll-up prose (reconciled via `/pipeline`).
- Auto-commit or push.
- Screenshot capture, diffing, multi-reviewer support.
