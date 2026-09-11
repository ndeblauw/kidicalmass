# Wayfinder: FR on staging, tickets for GitHub

The wayfinder map and its tickets for `ndeblauw/kidicalmass`, charted and grilled by Frederik and Nico on 2026-09-11. Nico posts them to GitHub from his machine.

How to post:

1. Create the labels `wayfinder:map`, `wayfinder:research`, `wayfinder:prototype`, `wayfinder:grilling`, `wayfinder:task`.
2. Create the map, then every ticket as a sub-issue of the map. The issue title is the heading without its number, the body is the text under "Body".
3. Wire each "Blocked by" as a native blocked-by relation.
4. For a closed ticket, post the text under "Resolution comment" as a comment, then close it.
5. In the map, replace each `LINK_nn` with the URL of ticket nn.

Frontier after posting: Add fr to the locale layer, Where French length breaks the layout, Pilot: extract Home, Build model localization, Review markers.

Dropped: "Staging: where it runs and who gets in" and "Stand up staging". Frederik and Nico handle staging outside the map.

---

# 00 · Wayfinder: FR on staging

- Label: `wayfinder:map`
- State: open

## Body

````markdown
## Destination

`/fr` runs on a staging site with the client's French copy and structure on every public page, so the client can review it in context. Unconfirmed claims carry a "To be confirmed" marker, and French content that outgrew its page sits in a marked layout proposal. Database content (activities, articles, groups, partner descriptions, team bios, quotes) shows French, machine-translated from the Dutch where no French existed. `/nl` keeps its current copy; blocks that exist only in French stay hidden there until translated.

## Notes

- This map carries execution. It overrides wayfinder's plan-only default: tickets build as well as decide, and the map is done when FR is reviewable on staging.
- French is the source language, structure included (client decision, 2026-09-11). Its new content comes from the client's own review of the NL copy. Don't change NL copy in substance; the NL translation is a later effort.
- This repo is public. Keep client copy, prices, names and contact details out of issues and comments. Link to the private source instead.
- HITL tickets are worked with Frederik (design, copy) and Nico (backend). Claim a ticket by assigning it before any work.
- Work lands on `main`. Pull the latest `main` before a session starts; Frederik and Nico commit to the same branch.
- Skills for build sessions: `laravel-best-practices`, `pest-testing`, `tailwindcss-development`, `fluxui-development`. CSS goes in partials, see `CLAUDE.md`.
- Tests are not rewritten in this effort. A test that breaks because of the locale work gets skipped with a comment saying so; the tests get refactored afterwards.
- The one-off donation returns, although the wiki closed it as dropped (D-9, 2026-07-03). Update D-9 once it is built.
- Related: #27 Language aspecs is Nico's broader i18n issue (EN, switcher, region detection). This map only covers FR on staging. Launch runway: `docs/wiki/build/30-launch-runway.md`, Lane 4 (FR locale).
- Starting point on 2026-09-11. `SetLocale::SUPPORTED` is `['nl']`. `lang/nl` holds about 245 flat keys, and roughly 400 to 500 strings are still hardcoded (home, activity and group pages, start a group, getting started, help out, partners, privacy, the public Livewire views, components). `_fr` columns exist on articles, activities, partners, press_articles and team_members, but only two accessors read them, and 43 places read `_nl` directly. Group names and ride locations are stored in Dutch only. There is no i18n package.

## Decisions so far

- [French copy mapped onto the site](LINK_04): 251 elements across 20 pages; 228 match, 17 restructured, 5 new, 1 without a home. 12 structural differences and 20 length hotspots. Detail in a private doc.
- [Translation key strategy for hardcoded strings](LINK_03): nested short keys, one lang file per view, named by position on the site (`home.hero.title`); shared UI words; long prose in per-locale partials; arrays for lists; public pages only; Home pilot, then one commit per page.
- [French copy vs newer NL copy](LINK_12): staging follows the French everywhere. The NL code turned out almost identical to the NL version the French was based on: 1 design-pass heading lost, 1 conflicting heading.
- [Locale-aware DB content and admin rules](LINK_06): per-model accessors over `_nl`/`_fr`/`_en` columns, falling back to French and then empty, decided per record; `quotes` and `team_members.role` get the same columns; the admin requires NL or FR; existing Dutch content gets machine-translated French.
- [Structural changes from the French copy that ship on staging](LINK_05): all of them, the French structure leads; unconfirmed claims marked "To be confirmed"; French-only blocks hidden on `/nl`; marked layout proposals where content outgrows a page; the one-off donation back in; partner types refactored with a migration.
- [Group names, slugs and prepositions in French](LINK_09): group names and ride locations per locale, machine-translated; group slugs in the municipality's own language; a helper for "d'"; region names untouched.
- [Pages where the French content outgrows the layout](LINK_15): Home, Organisation and Partners need a structural rethink, Steun ons stretches a component, the rest fits. Each of the four gets a build ticket with a marked layout proposal.

## Not yet specified

- Extraction tickets for the remaining public views, sliced once the Home pilot shows how much one session holds.
- The FR import: script or by hand, and who reviews the result.
- Layout fixes, once the prototype shows where French breaks.
- FR for strings reviewers still meet on public pages outside the page copy: Flux component labels, dates, error pages.
- FR downloads (manifest, charters, flyers, colouring page): link FR files or leave NL files on staging.

## Out of scope

- Setting up staging (host, deploys, access, data, mail, domain) and admin access for the client. Frederik and Nico handle that outside this map.
- Redesigning pages around the new French content. Frederik explores that separately; this map only places the content as a marked proposal.
- Feedback channel and the validation checklist for the client (phase 2, client side).
- Translating FR back to NL and importing it into `lang/nl` (phase 3).
- Language switcher, hreflang, dynamic `og:locale`, browser-language redirect on `/` (phase 4, go-live).
- English as a locale and region-based language detection from #27. The `_en` columns get added but stay empty.
- Emails (group digest), backstage pages and validation messages in French. This effort covers public pages.
- Region names, a back-end construct that doesn't show on the site.
- Newsletter backend and MailerLite groups.
- Production hosting and cutover (Lane 1).
- Splitting `routes/web.php` into separate files (Nico, later).
- Rewriting tests for the locale work.
- The six open client decisions (bank account, contact inbox, legal structure, trailer permission, national partner list, deadline). Staging shows placeholders.
````

---

# 03 · Translation key strategy for hardcoded strings

- Label: `wayfinder:grilling`
- State: closed
- Blocked by:
  - 04 · French copy mapped onto the site

## Body

````markdown
## Question

How do we key the 400 to 500 strings that are still hardcoded in Blade views, public Livewire views, components and emails?

- File layout: per-page PHP groups like the existing `lang/nl/about.php` and `lang/nl/support.php`, or JSON with the Dutch sentence as key.
- Do keys follow the element labels in the FR copy document, so the import can match by label? The copy mapping ticket shows whether that's realistic.
- Long rich-text blocks with links and bold: one key with HTML, several keys, or a Blade partial per locale.
- Who extracts (Nico, Frederik, agent sessions) and in what page order. NL must render identically after each page.

Worked with Nico.
````

## Resolution comment

````markdown
## Resolution

Decided with Frederik and Nico, 2026-09-11.

- Short keys in PHP lang files. No JSON with the Dutch sentence as key: French is the source and the NL copy will be rewritten, so sentence keys would all break.
- Everything nested, named after the place on the site: `home.hero.title`. A block made of several parts numbers them: `home.hero.text_1`, `home.hero.text_2`. The existing 245 flat keys move to nested as well.
- One lang file per view. Real UI words (more info, close) live in a shared file and are reused everywhere. Everything else is duplicated per view, because one Dutch word can need a different French word in another context.
- Long prose goes in a Blade partial per locale, not in lang strings.
- Links and emphasis inside a sentence use placeholders, with the link text as its own key. No HTML in lang files.
- Lists whose length changes are arrays in the lang file. The French item count leads, and NL gets the same count when it is translated.
- Everything a person sees or hears is extracted, `alt`, `aria-label`, meta and og text included. Stored form values stay fixed codes with translated labels.
- This step covers public pages, their components and the public Livewire views. Emails, backstage and validation messages are out.
- Agent sessions do the extraction, one commit per page. Home is the pilot, and Nico reviews its key names before other pages follow.
- No test rewrites and no automated NL comparison in this effort. A test that fails because copy moved into a key gets `->skip()` with a comment saying it fails because of the translation extraction and will be refactored afterwards.
````

---

# 04 · French copy mapped onto the site

- Label: `wayfinder:research`
- State: closed

## Body

````markdown
## Question

How does the French copy document map onto the site as it is built today?

Per page in the cleaned French copy, list:

- elements that match an existing Blade view and, where one exists, its translation key;
- elements that are new or restructured compared to the NL page;
- elements with no home on the site.

The findings go in a Google Doc next to the French copy document, because the copy is client material. This issue only links to it. No copy is quoted here.
````

## Resolution comment

````markdown
## Resolution

Mapped the cleaned French copy (432 paragraphs) against the current Dutch site: 20 pages and templates, plus navigation, footer and shared components.

The French was translated from an earlier version of the NL copy-review document. A follow-up comparison of all 454 elements in that document against the code found the current NL almost identical to it: one design-pass change the French lacks and one conflicting heading. See "French copy vs newer NL copy".

- 251 elements mapped. 228 match an existing view (and its translation key where one exists), 17 are restructured, 5 are new, 1 has no home in the current build.
- 12 structural differences need a decision: FAQ question counts, list counts, form field options, pricing structure.
- 20 length hotspots where French UI strings run clearly longer than Dutch: navigation labels, buttons, headings, calendar and region tabs.

The per-element table quotes client copy, so it lives in a private Google Doc (owner access only). Ask Frederik for access.
````

---

# 05 · Structural changes from the French copy that ship on staging

- Label: `wayfinder:grilling`
- State: closed
- Blocked by:
  - 04 · French copy mapped onto the site
  - 12 · French copy vs newer NL copy

## Body

````markdown
## Question

Which structural changes in the French copy do we build for staging?

The French copy reshapes several pages: more FAQ questions, a new section on Organisation, more Steun ons items with a title and explanation each, partner formulas, new choice values in the partner and contact forms. The full list comes from the copy mapping ticket.

- Build all of it, so the client validates the claims in context? Or hold back what is a content claim nobody has validated yet?
- Does `/nl` get the new structure now (with NL placeholders or hidden blocks), or keep its current structure until the NL translation lands?
- Form choice values touch stored data. Which changes need Nico?

Worked with Frederik, Nico for the forms.
````

## Resolution comment

````markdown
## Resolution

Decided with Frederik and Nico, 2026-09-11.

- The French content leads, and so does its structure. Added FAQ questions, extra tasks and new sections are new content from the client's own review of the NL copy, and all of it gets built.
- Claims the client still has to confirm (what support money pays for, partner prices, the third coordinator) get built and carry a highlighted "To be confirmed" in the UI. The marker comes off once the client confirms.
- Blocks that exist only in French stay hidden on `/nl` until their NL translation arrives.
- Where French content doesn't fit the current layout (Home and several other pages), the build session places it as well as it can and marks the block in the UI as a layout proposal for Frederik to revise. "Pages where the French content outgrows the layout" lists which pages that affects. A proper redesign is a separate exploration.
- The one-off donation comes back. Whether to accept one-off gifts is the client's call, not ours. Frederik double-checks how it is implemented before staging opens.
- Partner form: the new organisation types are added, with Dutch labels as well. All type codes may be refactored, with a data migration for existing enquiries.
- The children's route section on Organisation is built the way the French writes it.
- The third coordinator is added through the admin, with a photo from the client.
````

---

# 06 · Locale-aware DB content and admin rules

- Label: `wayfinder:grilling`
- State: closed

## Body

````markdown
## Question

How do models serve localized fields, and what does the admin require now that French is the source?

- Pattern: a shared trait or helper, per-model accessors like `Activity::getTitleAttribute` today, or a package. A package is a dependency change and needs approval.
- Fallback when the FR field is empty: show NL, or show nothing.
- Replacing the 43 direct `_nl` reads across 18 files, views and emails included.
- `quotes` (single `quote`/`attribution`) and `team_members.role` (single string) have no locale. New columns, JSON, or lang strings?
- The admin form requests require `title_nl` and BlueAdmin lists by `title_nl`. Does FR-first flip that, or allow either?

Worked with Nico.
````

## Resolution comment

````markdown
## Resolution

Decided with Frederik and Nico, 2026-09-11.

- Accessors per model, no package. Every translatable field gets three columns (`title_nl`, `title_fr`, `title_en`), and the `title` accessor returns the column for the current locale.
- Fallback: the current locale, then French, then empty. French is the source language, so it is the only fallback.
- The fallback is decided per record, not per field, so one record never mixes languages.
- All models use the same approach, `quotes` included. `quotes.quote`, `quotes.attribution` and `team_members.role` become `_nl` columns next to new `_fr` and `_en` columns, so the column names don't clash with the accessors.
- The 43 direct `_nl` reads are replaced per view, in that page's extraction commit. Emails and backstage keep theirs.
- The admin stays as it is for now, except that at least one of `_nl` or `_fr` is required.
- One slug per record, shared by all locales, for now.
- An agent session builds it, and Nico reviews.
- Existing Dutch content gets French through AI translation into the `_fr` columns (its own ticket), so `/fr` on staging isn't empty.
````

---

# 07 · Add fr to the locale layer

- Label: `wayfinder:task`
- State: open

## Body

````markdown
## Question

Add `fr` to the locale layer with French slugs, with nothing changing for NL visitors.

Decided with Frederik and Nico, 2026-09-11:

- Pull the latest `main` before starting. The work lands on `main`.
- French slugs. Each FR route sits directly below its NL route in `routes/web.php`, so the pair stays together. Nico splits routes into separate files later.
- NL routes keep their names (`activities.index`). The FR route gets a `fr.` prefix (`fr.activities.index`). A `localized_route()` helper picks the name for the current locale. Views switch to the helper per page, in the extraction commits.
- A missing FR translation key shows a visible marker on staging, so reviewers see what isn't translated yet. Production falls back to NL silently.
- `LocaleRoutingTest` asserts that `/fr` is a 404. Skip it with a comment that it fails because of the locale work and will be refactored afterwards.

Done when:

- `fr` is in `SetLocale::SUPPORTED`, and every public NL route has a French-slug twin directly below it;
- the French slugs come from a list the session proposes from the French navigation labels, approved by Frederik before the routes go in;
- `localized_route()` resolves both locales;
- `<html lang>` follows the locale, the error pages and `layouts/build` included;
- the missing-key marker appears on staging only;
- `/` still redirects to `/nl` (browser-language detection is phase 4), and the session has checked what adding `fr` changes in `LegacyRedirectController`, which already picks a preferred language from `SetLocale::SUPPORTED`.
````

---

# 08 · Where French length breaks the layout

- Label: `wayfinder:prototype`
- State: open
- Blocked by:
  - 04 · French copy mapped onto the site

## Body

````markdown
## Question

Where does French string length break the layout, and what do we do at each spot?

Prototype with the real French strings (from the copy mapping) at mobile and desktop widths:

- main navigation and its breakpoint;
- radius tabs on the calendar;
- region buttons with badges on Lokale groepen;
- button labels of six or seven words;
- headings that became full sentences.

Per spot, decide with Frederik: let it wrap, restyle, or ask the client for a shorter string.
````

---

# 09 · Group names, slugs and prepositions in French

- Label: `wayfinder:grilling`
- State: closed

## Body

````markdown
## Question

How do we handle French prepositions and articles around inserted names?

Strings like "de {groupe}" and "à {commune}" come out wrong for names that start with a vowel or carry an article (Le, La, Les). Options:

- a stored field per group or municipality with the composed form;
- rephrasing the strings so no article is needed, for example "Groupe : {nom}";
- a helper that picks the form from the name.

Start from the list of current group and municipality names in the database, to see how many are affected.

Worked with Frederik (copy) and Nico (data).
````

## Resolution comment

````markdown
## Resolution

Decided with Frederik and Nico, 2026-09-11.

- Group names get `name_nl`, `name_fr` and `name_en` columns with an accessor, like the other models. The machine translation fills in the municipality's official French name.
- A group's slug uses the municipality's name in its own language, converted to ASCII with hyphens for spaces.
- Before a French name that starts with a vowel, a helper turns "de" into "d'". "à" needs no change for the current names.
- Activity `location` gets `_nl`, `_fr` and `_en` columns as well, filled by the machine translation.
- Region names stay as they are. They are a back-end construct and don't show on the site.

Open detail for the build: the Brussels communes are bilingual, so "own language" needs a pick per commune. The session proposes one and Frederik approves it in his review pass.
````

---

# 10 · Pilot: extract Home into nested keys

- Label: `wayfinder:task`
- State: open
- Blocked by:
  - 03 · Translation key strategy for hardcoded strings

## Body

````markdown
## Question

Extract Home into nested translation keys as decided in "Translation key strategy for hardcoded strings", and let Nico confirm the naming before other views follow.

Done when:

- the Home view reads its copy from `lang/nl/home.php`, nested and named by position on the site, with UI words from the shared file;
- components used only by Home are extracted in the same commit, into their own lang file;
- the hero heading keeps its per-word animation. It is one key, split into word spans at render time, because the French heading has a different number of words;
- `/nl` Home looks the same as before, checked by eye;
- every test that fails because copy moved into a key is skipped with a comment that it fails because of the extraction and will be refactored afterwards;
- Nico has reviewed the key names, and the resolution records what the convention gained along the way: where long prose moves to a per-locale partial, the name of the shared UI file, and how component files are named.
````

---

# 11 · Migrate existing lang keys to nested

- Label: `wayfinder:task`
- State: open
- Blocked by:
  - 10 · Pilot: extract Home into nested keys

## Body

````markdown
## Question

Move the existing flat keys (`about.mission_title`) to the nested convention (`about.mission.title`) confirmed in the Home pilot, and update the calls that use them.

Scope: about, common, contact, footer, meta, nav, partners and support, about 180 keys and 195 `__()` calls. Laravel's own files (auth, passwords, validation) keep their framework keys.

Done when no public view uses a flat key, `/nl` looks the same, and tests that fail only because of the rename are skipped with the same comment as in the pilot.
````

---

# 12 · French copy vs newer NL copy

- Label: `wayfinder:grilling`
- State: closed
- Blocked by:
  - 04 · French copy mapped onto the site

## Body

````markdown
## Question

Where the French copy and the newer NL copy in code say different things, which one does staging follow?

The French was translated from an older version of the NL copy doc. The first copy mapping suggested that design passes had since rewritten the NL copy of several pages directly in code. French is the source language, and NL gets translated back from it later, so whatever the French lacks disappears from NL too.

- Follow the French everywhere and accept that the design-pass changes are lost at translation time.
- Per page, list what the design passes changed and ask the client to fold that into the French before the import.
- Keep the newer page structure and wording choices, and take from the French only what it adds or corrects.

Uses the private copy mapping doc. Worked with Frederik; the outcome may need the client.
````

## Resolution comment

````markdown
## Resolution

Decided with Frederik and Nico, 2026-09-11: staging follows the French everywhere. Design-pass changes to the NL copy that the French doesn't carry are accepted as lost when NL is translated back from the French.

Frederik and Nico asked for a concrete overview of what that means: per page, the design-pass improvements the French lacks and the places where both sides changed the same element. It lives in a private Google Doc next to the copy mapping, because it quotes client copy.

The overview compared all 454 elements of the old NL document with the code, across 20 pages. The current NL copy is almost identical to that document, so following the French costs little of the design-pass work:

- 1 design-pass change the French lacks: the lead of the exits block on the About hub.
- 1 conflict: the news heading differs in the document, the code and the French. Under this decision the French heading wins.
- 26 places where only the French changed, 5 of them with a large content impact (home heading, the demands on Vision, the coordinators on Organisation, among others). Those are content claims for the client to validate in phase 2, not conflicts.

````

---

# 13 · Build model localization

- Label: `wayfinder:task`
- State: open
- Blocked by:
  - 06 · Locale-aware DB content and admin rules
  - 09 · Group names, slugs and prepositions in French

## Body

````markdown
## Question

Build the model localization decided in "Locale-aware DB content and admin rules".

Done when:

- every translatable field on activities (location included), articles, groups (name), partners, press_articles, team_members and quotes has `_nl`, `_fr` and `_en` columns, with `groups.name`, `activities.location`, `quote`, `attribution` and `role` renamed to `_nl`;
- group slugs use the municipality's name in its own language, in ASCII with hyphens for spaces, and a helper turns "de" into "d'" before French names that start with a vowel;
- each model has an accessor per field that returns the current locale's column, then French, then empty, with the language decided per record. The session proposes which field decides a record's language, and Nico confirms it in review;
- the admin requires at least one of `_nl` or `_fr` and otherwise stays as it is;
- existing data survives the migrations, checked on a copy of the local database;
- tests that break because of the renames are skipped with a comment that they fail because of the locale work and will be refactored afterwards.

Not in this ticket: the direct `_nl` reads in views. Those are replaced per page during extraction.
````

---

# 14 · Machine-translate Dutch database content to French

- Label: `wayfinder:task`
- State: open
- Blocked by:
  - 13 · Build model localization

## Body

````markdown
## Question

Translate the existing Dutch database content to French with an AI agent and store it in the `_fr` columns, so `/fr` on staging shows complete pages.

- Scope: every record with Dutch content and an empty `_fr` field, across activities (location included), articles, groups (name), partners, press_articles, team_members and quotes. Existing `_fr` values stay untouched.
- Group names and place names get the municipality's official French name, not a translation.
- Use the client's French copy as the reference for terms and register: the word for a ride, vous or tu.
- It runs against the local database. Getting the data onto staging is outside this map.
- The resolution lists which records got machine translation, so the client knows which French still needs a human review.

Driven by an agent; Frederik checks a sample in his review pass.
````

---

# 15 · Pages where the French content outgrows the layout

- Label: `wayfinder:research`
- State: closed
- Blocked by:
  - 04 · French copy mapped onto the site

## Body

````markdown
## Question

Which pages does the French content outgrow, now that the French structure leads?

Per public page, list the French content that doesn't fit the current layout: new sections, more items than a component was designed for, long text in a block built for a short line, content with no block at all. For each item, propose a best-effort placement the build session can use (which existing component or pattern, and where in the page order). Rank the pages by how much redesign they need.

The overview lives in a private Google Doc, because it quotes client copy. It feeds the build sessions and Frederik's separate redesign exploration.
````

## Resolution comment

````markdown
## Resolution

The overview is a private Google Doc, because it quotes client copy. It ranks pages by how much redesign they need.

- **Large**, where the page structure no longer fits:
  - Home: the French writes full sections and a long hero text, while the page is now a short scrollytelling layout with a video hero.
  - Organisation: a new section has no place, two lists grow, and a third coordinator changes the page's "duo" framing.
  - Partners: prices move from a PDF onto the page, the organisation type field goes from 4 to 6 options, and a new "what we offer partners" list appears.
- **Medium**, where a component gets stretched: Steun ons, whose list grows from 4 short lines to 7 items with a title and explanation each. The component copes, but the item format changes.
- **Small**, still fits: site-wide labels (navigation, footer, buttons, tabs) run longer in French, and the getting-started FAQ grows from 8 to 10 questions in an accordion with no fixed capacity.
- **Everything fits**: calendar, ride detail, help out, local groups, group page, start a group, newsletter, about hub, mission, vision, news, press, contact, privacy.

The large and medium pages each get a build ticket. The small items ride along with extraction and the length prototype.
````

---

# 16 · Frederik's review pass before staging opens

- Label: `wayfinder:task`
- State: open
- Blocked by:
  - 07 · Add fr to the locale layer
  - 10 · Pilot: extract Home into nested keys
  - 11 · Migrate existing lang keys to nested
  - 13 · Build model localization
  - 14 · Machine-translate Dutch database content to French
  - 17 · Home: French content as a layout proposal
  - 18 · Organisation: French content as a layout proposal
  - 19 · Partners: French content, prices and organisation types
  - 20 · Steun ons: support items and one-off donation
  - 21 · Review markers for unconfirmed claims and layout proposals
  - + future extraction tickets

## Body

````markdown
## Question

Frederik's review pass, before the client gets the staging link.

This map deliberately left these items for Frederik to check:

- blocks marked as layout proposals: keep, adjust, or hand over to the redesign exploration;
- how the one-off donation is implemented;
- the "To be confirmed" markers: every unconfirmed claim has one;
- a sample of the machine-translated database content, group names included;
- slugs for the Brussels communes;
- the missing-key marker shows where French is still missing, and only on staging.

Done when every item is checked or turned into a follow-up ticket.
````

---

# 17 · Home: French content as a layout proposal

- Label: `wayfinder:task`
- State: open
- Blocked by:
  - 07 · Add fr to the locale layer
  - 10 · Pilot: extract Home into nested keys
  - 21 · Review markers for unconfirmed claims and layout proposals

## Body

````markdown
## Question

Place the French content on Home as a layout proposal, as decided in "Structural changes from the French copy that ship on staging".

The French writes full sections and a long hero text; Home is now a short scrollytelling layout with a video hero. Start from the placement proposals in the private layout overview.

Done when:

- all French Home content is on `/fr`, placed as well as the current design allows;
- every block that departs from the current layout carries the layout-proposal marker;
- `/nl` Home is unchanged, with French-only blocks hidden there;
- the resolution names the blocks Frederik should look at first.
````

---

# 18 · Organisation: French content as a layout proposal

- Label: `wayfinder:task`
- State: open
- Blocked by:
  - 07 · Add fr to the locale layer
  - 10 · Pilot: extract Home into nested keys
  - 21 · Review markers for unconfirmed claims and layout proposals

## Body

````markdown
## Question

Place the French content on Organisation as a layout proposal.

The French adds a children's route section (built as written), grows the national tasks from 4 to 7 and the local-group tasks from 4 to 5, and mentions a third coordinator.

Done when:

- the new section and the longer lists are on `/fr`, with the layout-proposal marker wherever the layout departs from the current page;
- the copy about the coordinators carries "To be confirmed" (the third team card itself goes in through the admin);
- `/nl` Organisation is unchanged, with French-only blocks hidden there.
````

---

# 19 · Partners: French content, prices and organisation types

- Label: `wayfinder:task`
- State: open
- Blocked by:
  - 07 · Add fr to the locale layer
  - 10 · Pilot: extract Home into nested keys
  - 21 · Review markers for unconfirmed claims and layout proposals

## Body

````markdown
## Question

Place the French content on Partners as a layout proposal, and refactor the organisation types.

- The formula prices move from the PDF onto the page and carry "To be confirmed".
- A new "what we offer partners" list goes next to the existing "what we ask" section.
- The organisation type field goes from 4 to 6 options, with Dutch and French labels. Codes may be refactored, and a data migration maps existing enquiries to the new codes.

Done when the French content is on `/fr` with markers where the layout departs, the new options work in both languages, every existing enquiry keeps a valid type, and `/nl` hides the French-only blocks. An agent builds it; Nico reviews the migration.
````

---

# 20 · Steun ons: support items and one-off donation

- Label: `wayfinder:task`
- State: open
- Blocked by:
  - 07 · Add fr to the locale layer
  - 10 · Pilot: extract Home into nested keys
  - 21 · Review markers for unconfirmed claims and layout proposals

## Body

````markdown
## Question

Rebuild the support items on Steun ons for the French structure, and bring back the one-off donation.

- The list goes from 4 short lines to 7 items with a title and explanation each, and every item carries "To be confirmed". The view also handles the current NL items, which have no title yet.
- The one-off donation comes back as the French describes it. The bank account is still an open client decision, so it shows a placeholder.

Done when both are on `/fr`, `/nl` still shows its current list, and the resolution describes how the donation is implemented so Frederik can check it in his review pass.
````

---

# 21 · Review markers for unconfirmed claims and layout proposals

- Label: `wayfinder:task`
- State: open

## Body

````markdown
## Question

Build the two review markers the page tickets rely on: "To be confirmed" for unconfirmed claims, and "layout proposal" for blocks placed outside the current design.

- One Blade component per marker, so a build session wraps a block in one line without styling anything itself.
- Both are clearly highlighted, and one config flag switches them. The flag is on for staging, so the markers can all go off once the client confirms and Frederik approves, without touching every page.
- Check whether the missing-translation marker from "Add fr to the locale layer" can use the same flag.

Done when both markers render behind the flag and a page can use each in one line.
````

