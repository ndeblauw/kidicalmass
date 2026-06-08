---
title: Commit workflow — how work lands on main
tags: [build, process]
sources: [wiki]
phase: build
updated: 2026-06-08
---

# Commit workflow — how work lands on `main`

The goal: a collaborator (Nico) opening `git log --first-parent main` should read **one line per unit of work**, not a transcript of every keystroke. We get there without giving up frequent local commits (the checkpoint mechanism that keeps parallel agents from clobbering each other).

**The principle:** commit freely *during* a thread; collapse to one curated commit *when the thread wraps*.

## During a thread — commit as often as you like

These commits are disposable checkpoints. Terse is fine (`wip: …`). They exist to:

- give agents safe restore points so parallel edits don't collide;
- let you bail out of a bad direction cheaply.

Nothing about this step changes. Keep your habit.

## At `/wrap` — collapse, then curate

One new step replaces "make a few commits on main": squash the thread's unpushed commits into **one** curated commit (occasionally 2-3 if the thread genuinely did separable things).

### Sequential work (the common case) — squash unpushed commits

You work on `main` directly. At wrap:

```sh
# 1. SAFETY GUARD — every unpushed commit must be yours.
git log origin/main..HEAD --format='%an'
#   • all "Frederik Vincx"  → safe to squash (continue)
#   • any Nico commit, or nothing unpushed already pushed → ABORT squash,
#     just write good messages on top instead. Never reset across Nico's work.

# 2. Collapse: un-commit the thread, keep every change staged.
git reset --soft origin/main

# 3. Re-commit as ONE curated message (or 2-3 by path — see grain rule).
git commit
```

`git reset --soft` only ever touches *your* unpushed commits — it cannot rewrite Nico's history, because his commits are either already pushed (below `origin/main`) or, if local, caught by the guard in step 1. Do **not** push; Frederik or Nico pushes manually.

### Parallel fan-out (occasional) — branch + `merge --squash`

When you *deliberately* spin up parallel agents, each runs in its own worktree + branch (own checkout, own branch, no collisions). Each one wraps with:

```sh
git switch main
git merge --squash <thread-branch>
git commit            # one curated message
git branch -D <thread-branch>
```

`--squash` is shared-tree-safe: it adds your branch's diff as a new commit on top of `main` and never rewrites anyone else's commits.

## The grain rule — default to one, smart-split when warranted

- **Default:** one commit per thread.
- **Split into 2-3** only when the thread touched clearly separable units (unrelated paths *and* scopes — e.g. a CSS refactor plus an unrelated config bugfix). Stage by path; show the grouping and confirm before committing.
- Never split a single conceptual change just because it touched many files.

## The curated message — where the value moves

Subject is the line Nico scans. Body bullets are what's inside when he drills in.

```
feat(components): extract 8 reusable components from about & calendar

- intro-text, section-heading, pull-quote, numbered-item
- person-card, info-card, agenda-item, titled-list-block
- registered in styleguide, removed from extraction candidates

Why: de-duplicate markup repeated across about/mission and calendar pages.
```

**Prefixes** (conventional commits, already in use): `feat` · `fix` · `refactor` · `style` · `docs` · `test` · `chore`. Add a scope in parens: `feat(calendar): …`. Keep the subject imperative and under ~70 chars.

## Guardrails (unchanged, restated)

- **Never `git add -A`** — Nico edits concurrently in the same checkout. Stage by explicit path.
- **Never push** — Frederik or Nico pushes manually.
- **Never `reset`/`rebase` across pushed or Nico-authored commits** — the step-1 guard enforces this.
- After wrapping, **eyeball the result**: `git log --first-parent origin/main..HEAD --oneline` should read as the clean changelog you intended.

## At `/wrap` — KidicalMass follow-ups

The global `/wrap` command handles the collapse-and-curate above generically, then defers project-specific post-commit steps to here:

- **Don't push.** Frederik or Nico pushes manually.
- **Update the build pipeline if a page advanced.** If the thread moved a page's Wire/Assets/UI/Back stage, follow the `/pipeline` conventions: update the `P-nn` row in `../design/30-skeleton/00-page-registry.md`, the Top-gaps cell, the roll-up prose, and append a `## [YYYY-MM-DD] build | …` entry to [`log.md`](../log.md). Skip if nothing shipped. Wire 🟢 needs Frederik's own critique pass — don't bump it for him.
