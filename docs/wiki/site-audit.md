---
title: Deep Site Audit — Current Wix Site
tags: []
sources: [notion]
updated: 2026-04-13
---

Audited: March 2026 | All pages crawled from [kidicalmass.be](http://kidicalmass.be/)
---
## 🏗️ Platform & Tech Stack
---
## 📄 Page-by-Page Audit
### 🏠 Home (`/`)
Hero image links to a Facebook event. Full FR mission text + full NL mission text stacked. Manual march–april parade snippet. Growth stats 2020–2024. Wallonie section. Vlaanderen section. Spacefunding CTA. 3 news previews. Partner logos. Footer with 3 different contact emails.
**Issues:**
- No primary CTA for a first-time visitor — no "where do I start?"
- Hero links to Facebook, not an internal page
- Homepage calendar duplicates /agenda — divergence risk
- FR + NL mission fully stacked = very long scroll
- Growth stats hardcoded (2024 data shown in 2026)
- 3 contact emails on same page: `bike@kidicalmass.be`, `cecilia@kidicalmass.be`, `contact@kidicalmass.brussels` — note the domain inconsistency (.be vs .brussels)
- Newsletter = raw Google Form link — breaks brand experience
- Multiple donation/sponsor CTAs with no visual hierarchy
---
### 📅 Agenda (`/agenda`)
Full 2026 season calendar in plain text, grouped by month. Trilingual headers (MARCH - MARS - MAART). All events link to Facebook. July is completely empty. Poster request by email.
**Issues:**
- Entirely hand-typed — no database, no structured data
- All event detail lives on Facebook — users leave the site to get time + location
- No start time or location shown in calendar
- No filtering by chapter or municipality
- No iCal or Google Calendar export
- No past events archive
- July blank with no note for newcomers
- Poster distribution by manual email
- **Highest-maintenance page on the site — every new parade = manual edit**
---
### 🤝 Volunteer (`/volunteer`)
FR + NL call to action. 5 roles listed: pink vest escort, co-organiser, communicator, photographer, DJ. Expectations listed. CTA to email `bike@kidicalmass.be`.
**Issues:**
- Signup is by email only — no intake form, no structured onboarding
- No indication of what happens after you email (process opaque)
- FR and NL content fully duplicated — doubles page length
- Rules document lives on Google Docs (external dependency)
- Safety training video on YouTube (external)
- No chapter-level volunteer routing — all goes to one central email
- No role-specific path ("I want to be a photographer")
---
### 🎯 Mission (`/le-projet-het-project`)
FR + NL project description. 3 mission axes: Start (get kids cycling), Support (regular mobility), Spread (cultural promotion). Inclusivity section. Stats: 150 parades, 5500 participants, 120 volunteers, 16 communities.
**Issues:**
- Slug `/le-projet-het-project` is awkward and not SEO-friendly
- All stats hardcoded — already stale (reference 2024/2025)
- No visual breaks or infographics — pure dense text
- FR and NL fully duplicated
---
### 🏛️ Organisation (`/organisation`)
FR + NL governance explanation: 4x annual regional meetups, coordination duo, routes & safety policy. Static SVG organigram showing: coordination duo → local leads → 14 groups → thematic working groups.
**Issues:**
- Organigram is a static SVG — not accessible, not updatable without a redesign
- WhatsApp cited as primary comms tool but not integrated anywhere on site
- No list of which municipalities are active with links
- 14 groups named in diagram but not individually identified or linked
- No structural home for new chapters outside Brussels
---
### 🗺️ Wallonie (`/wallonie`)
Just a bullet list of 6 cities with past events (La Hulpe 2023, Tubize 2023, Mouscron 2024, Namur 2024, Mons 2025, Liège 2022). CTA to email. One stock photo.
**Issues:**
- Extremely sparse — no chapter contacts, no local agenda, no photos
- Dates suggest past events, not ongoing groups — status unclear
- Liège links to a separate external site (`kidicalmassliege.org`)
- No Flanders equivalent page despite Flanders being active
---
### 📋 Nos revendications (`/nos-revendications-onze-aanbevelingen`)
4 policy demands in FR + NL: (1) bike lanes on major roads, (2) family bike parking, (3) safe school environments, (4) 30 km/h zone enforcement.
**Issues:**
- FR and NL duplicated in full
- Plain text — no visual hierarchy or icons
- No downloadable version of the manifesto directly on this page
- URL is the longest and most unwieldy slug on the site
---
### 🏙️ Child Friendly City (`/what-we-want`)
Full manifesto text (Brussels-focused). Parent quotes. Statistics on children's outdoor time. Coalition demands. External study links. PDF download of full manifesto.
**Issues:**
- Very long essay-style page — no visual anchors
- Brussels-centric despite being a Belgium-wide site
- No NL translation visible — FR only
- PDF links to Wix CDN — fragile on migration
- Multiple external study links that will go stale
---
### 📰 News / Blog (`/my-blog`)
Wix blog. 4 visible posts: volunteer call (Mar 2026), season launch meetup (Feb 2026), end-of-year party (Nov 2025), 5th anniversary (May 2025). Posts mix FR + NL inline.
**Issues:**
- URL is `/my-blog` — the Wix default, completely unbranded
- No filtering by language, chapter, or category
- No author attribution
- FR and NL mixed within same post body
- No chapter-specific news — all national only
- Very low post frequency
---
### 📰 Press (`/press`)
Chronological list of coverage 2020–2025: RTBF, Vivavité, Het Laatste Nieuws, Bruzz, BX1, Het Nieuwsblad, La Dernière Heure, Politico. PDF press releases downloadable.
**Issues:**
- Plain text list of links — no logos, no excerpts, no visual treatment
- PDF press releases hosted on Wix CDN — fragile
- No language label on articles
- No dedicated press contact section or media kit download
---
### 📥 Downloads (`/downloads`)
2025: A5 flyer PDF + A2 poster PDF. 2024: ~13 image thumbnails with no labels and no download links (appears broken).
**Issues:**
- 2024 archive items are unlabelled thumbnails with no links — appears broken
- No organisation by type (flyer, poster, press release, brand assets)
- No FR vs NL distinction on print materials
- No brand guidelines or logo pack
- Sponsor formulas and partner charter PDFs are buried in the footer, not here
---
### 🎪 Grande Kidical Mass
Annual flagship event page. 2025: Sunday 4 May, Brussels 5th anniversary. Full programme, animations, practical info in FR + NL. Volunteer signup via separate Google Form.
**Issues:**
- Each year gets a new page — nav sub-menu grows annually with no archive pattern
- Volunteer link for this event goes to a Google Form — separate from the main volunteer system
- Route links to Google Maps — no embedded map
---
## 🧩 Cross-Cutting Problems
### 1. Bilingual structure is broken
Every page stacks FR and NL in full — no language toggle, no /fr/ and /nl/ routes. Every page is twice as long as needed.
### 2. Total Facebook dependency
The entire event ecosystem lives on Facebook.
### 3. Everything is manual
No database-driven content anywhere.
### 4. No chapter pages exist
13+ active local groups but not one has its own page.
### 5. Contact fragmentation
3 email addresses in use.
### 6. No structured volunteer onboarding
Volunteer path: read page → send email → wait.
### 7. Stats and content go stale immediately
Homepage, mission, and organisation pages all have hardcoded numbers referencing 2024 in March 2026.
---
## 📊 Active Chapters Identified
**Brussels (16/19 municipalities confirmed on agenda):**
Woluwe-Saint-Pierre...
**Wallonia (known past events, status varies):**
Mons, Namur, Mouscron, La Hulpe, Tubize, Liège (separate org at kidicalmassliege.org)
**Flanders:**
Mentioned as "work in progress" on the agenda — no pages or contacts
---
## 🔑 Key Signals for the Laravel Build
The site reveals 5 core structural needs:
1. **Event/parade database** — chapters self-publish rides, calendar auto-populates, no manual editing
1. **Chapter pages** — each municipality gets its own space
1. **Proper bilingual routing** — FR and NL as first-class citizens with clean URLs
1. **Volunteer intake system** — forms with role selection and chapter routing
1. **Self-serve content management** — news, downloads, stats per chapter, no central bottleneck