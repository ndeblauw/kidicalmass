---
description: Update the build-pipeline status for a page (P-nn) in the page registry
argument-hint: "[P-nn] [stage=value …]  e.g. P-13 wire=good back=good assets=nvt"
---

Update the Kidical Mass build pipeline. The canonical procedure lives in `CLAUDE.md` →
"Updating the build pipeline" — follow it exactly. The source of truth is the page
registry (`P-nn`) table in `docs/wiki/design/30-skeleton/00-page-registry.md`.

Arguments (optional): $ARGUMENTS

How to interpret the arguments:
- A target id (`P-13`). If omitted, infer it from what we just worked on; if still
  ambiguous, ask which page.
- Zero or more `stage=value` pairs. Stage keys: `ux, conf, wire, assets, ui, back, ok`.
  Values → emoji: `good/🟢`, `wip/bezig/🟠`, `none/todo/🔴`, `nvt/na/⚪`,
  `todecide/❓`; `conf` takes a digit `1–5`.
- If no stage pairs are given, propose the bumps yourself based on the actual state of
  the code/view/backend, and confirm them with me before editing.

Then carry out the full four-part update from CLAUDE.md:
1. Edit the row's stage emoji(s) — keep all 12 columns intact.
2. Prune resolved items from the **Top gaps** cell and add a terse "X live" note.
3. Reconcile the **Roll-up** prose beneath the table.
4. Append a `## [YYYY-MM-DD] build | …` line to `docs/wiki/log.md`.

**Honesty gate:** only set `Wire 🟢` if the view renders, is verified, **and Frederik has
done his own critique + refine pass** (your render/tone check tops out at 🟠). Only set
`Back 🟢` if the data path is wired & verified live — not merely coded. If a claimed stage
isn't actually verifiable yet, say so and leave it.

Finally, **verify**: run `app(App\Support\Build\BuildStatus::class)->report()` via
`php artisan tinker` (or load the `/build` dashboard), confirm the target row parses with
the intended stages, `warnings` is empty, and there's no unexpected `drift`. Report the
before→after stages in one line.
