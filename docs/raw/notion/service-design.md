Synthesis of all desk research. This is the picture of what Kidical Mass Belgium actually is as a service — who it serves, how it works, where it breaks, and what a new platform needs to do.
---
## What Kidical Mass Actually Is
On the surface it's a website. Underneath it's a **federated volunteer network** running a recurring public service — free, inclusive, child-friendly bike parades — across 20+ Belgian municipalities, in two languages, with no paid staff beyond a coordination duo.
The closest structural analogues are Repair Café (3800+ local groups, central brand, local autonomy) and a franchise without the money. The central team sets the brand, the safety standards, the values. Local chapter leads do the actual work — plan the route, recruit the pink vests, post on Facebook, show up on Sunday.
The website is not the product. **The parade is the product.** The website is just supposed to help people find it, join it, and run it.
---
## The Actors
Five distinct actors interact with this service. They have almost nothing in common except showing up on the same Sunday.
### 1. The Family — First-Timer
*"I heard about Kidical Mass from a friend. Can my 5-year-old do it? When is the next one near me?"*
- Discovers via Facebook, Instagram, word of mouth, or a friend
- Primary need: **next ride near me, date, time, meeting point**
- Secondary need: is it suitable for us? (child age, distance, pace)
- Tertiary need: what if we don't have a bike?
- Current experience: lands on [kidicalmass.be](http://kidicalmass.be/), finds a text list of dates with no times or locations, clicks through to Facebook to get actual info, possibly doesn't have Facebook
- **Core friction: the website doesn't answer the three questions they came with**
### 2. The Family — Regular
*"We go every month. We love it. Schaerbeek is our chapter."*
- Already knows how it works
- Needs: what's the next date, any theme or special programme?
- Likely follows the Facebook page directly, barely uses the website
- Might want to share it with friends → needs a shareable, standalone event page that isn't Facebook
- **Low friction currently, but Facebook-dependent**
### 3. The Pink Vest — Active Volunteer
*"I escort rides in Forest and occasionally Watermael. I got trained last season."*
- Recruited by email after contacting `bike@`
- Coordinates via WhatsApp group with other local volunteers
- Needs: ride schedule for their chapter, safety reminders, occasional training info
- Interacts with the website almost not at all — it played no role after their initial contact
- **The website is not part of their ongoing experience**
### 4. The Chapter Lead — Local Organiser
*"I run the Mons rides. I plan the route on Komoot, post on Facebook, show up, deal with it."*
- Responsible for 4–10 rides per year in their municipality
- Currently has: a WhatsApp group, a Facebook page/event, their own Komoot account
- To get on the national calendar: sends info to the coordination duo → they manually update the Wix page
- Has no CMS access, no chapter page, no way to publish anything to the national site
- **Completely dependent on Brussels to exist on the national site**
- The Liège chapter opted out entirely and built their own Google Site
- New Wallonia and Flanders chapters have no digital home at all
### 5. The Coordination Duo — Leticia & Cecilia
*"We hold the whole thing together. Communication, sponsors, training, website, press, WhatsApp, grants."*
- Two people doing the work of a small nonprofit communications team
- Current website duties: manually update the agenda for every new parade across all chapters, manage the Wix editor, handle all press contact, manage sponsor visibility, answer every inbound email from families, volunteers, press, and potential new chapters
- The website creates work rather than reducing it
- Every new chapter that joins means more manual updates
- **The platform's primary job is to give these two their time back**
---
## The Current Service Map
How a family actually experiences Kidical Mass today:
```javascript
DISCOVERY
Instagram post / friend recommendation / press article
        ↓
Lands on kidicalmass.be
        ↓
Finds a text-only date list (no time, no location)
        ↓
Clicks Facebook event link → leaves the site
        ↓
[If they have Facebook] Gets time + meeting point from event
[If they don't] Dead end. No info. Lost.
        ↓
Shows up Sunday → has a great time
        ↓
Wants to volunteer → emails bike@kidicalmass.be
        ↓
Waits. Gets a reply. Gets added to a WhatsApp group.
        ↓
Attends a meetup. Joins.
```
The site contributes to approximately one step in this journey (the date list), and does that step poorly. Facebook does the heavy lifting. WhatsApp runs the community.
---
## The Five Structural Problems
These are not bugs. They are the inevitable result of building a national federated service on a single-tenant Wix site.
### 1. No chapter layer exists
Every chapter is invisible on the national site. There is no Schaerbeek page, no Mons page, no Forest page. The only chapter with its own digital presence (Liège) built it independently and disconnected from the national site. As new chapters join — Ghent, Antwerp, Charleroi — this problem compounds.
### 2. The calendar is a single point of failure
One hand-edited text block on one Wix page. Every parade in every municipality goes through the coordination duo. 60+ events per year, each requiring a manual edit. No chapter can self-publish. No automation. No iCal export. All event detail lives on Facebook.
### 3. Bilingualism is stacked, not routed
Every page contains full FR text followed by full NL text. This is the wrong pattern for Belgium. It doubles page length, breaks SEO, and makes the site feel schizophrenic. It's also unmaintainable — when content changes, someone has to update both language blocks manually, consistently, every time.
### 4. Volunteer onboarding is an email black hole
The path from "I want to help" to "I am a pink vest" is: send an email → wait → get a reply → join a WhatsApp group → attend a meetup → show up. There is no structured intake, no role matching, no chapter routing, no onboarding checklist. The coordination duo handles every step manually.
### 5. Sponsor obligations are untracked
Logo placement on the website is a contractual commitment (from the sponsor formula PDF). There is no system for tracking which sponsors are active, what tier they're on, when their logos should appear, and whether they're getting what they paid for. This is managed entirely by memory and goodwill.
---
## The Chapter Pages That Already Exist (Hidden)
Two Wallonia chapters have actual pages on the national site — linked only from the Wallonie section, not the main nav, and invisible to most visitors. They are the closest thing to a proof of concept for what chapter leads actually want to publish. Both are rich and self-contained.
### Namur (`/5000`)
- Single upcoming event with full practical info: date, time, meeting point (Place du Théâtre), distance (±5km), duration (max 1h), age range, cost (free), music
- Volunteer call with specific roles listed: escort, communications, DJ vélo, local partners
- Dual contact: local email (`sindy.kinard@gmail.com`) AND the national `contact@kidicalmass.brussels`
- Local partners named: Masse Critique de Namur, Avello, Pro Vélo, Cycles et Sacoches
- Photo gallery from past events
- Local press coverage linked (RTBF, Vivavité)
- FR-only — no NL content at all
### Mons (`/7000`) — the most complete chapter page on the site
- Season schedule: 3 dates listed with times (Sundays 13h30)
- Meeting point named: Théâtre le Manège, with street address
- Standard practical info block (5–7km, all ages, free, max 1h, music, adult supervision)
- **Has its own domain: **`kidicalmass.mons.bike` — another independent digital presence, alongside Liège
- Own Facebook page (`facebook.com/monspointbike`) separate from the national page
- Dual contact: `kidicalmass@mons.bike` AND `bike@kidicalmass.be`
- Local partners very detailed: Avello Mons, Tata Bicyclette, Bike Repair Social Club, MARS (Mons Art de la Scène), Ville de Mons, local citizens
- **History section**: started 2025, 3 editions, first edition had ~120 participants, local mobility alderman attended
- Named organising team: Violette, Babas, Sébastien et Thibault
- Local downloads section (A5 flyer 2026, A5 flyer général, A4 poster) — links appear broken on Wix
- Aftermovie link (Instagram)
- Local press: La DH article linked
- FR-only — no NL
### What these two pages reveal
**On the chapter lead actor:** They are not waiting to be given a template. They know exactly what they want to publish — a schedule, a meeting point, their local partner ecosystem, their team, their history, their press coverage, their downloads. The content is rich and specific. The platform just needs to give them a structured way to do it without going through Brussels.
**On language:** Both Wallonia chapter pages are FR-only. There is no NL content on either. This confirms the chapter-level language model: chapters publish in their own language, the platform doesn't force bilingualism at chapter level. National-level content is bilingual; chapter-level content follows the chapter's linguistic context.
**On the domain proliferation pattern:** Mons has `kidicalmass.mons.bike`. Liège has `kidicalmassliege.org`. Brussels has both `kidicalmass.be` and `kidicalmass.brussels`. Every chapter that grows finds a way to establish its own digital identity outside the national site. This is not a bug — it's chapters filling a vacuum. The new platform needs to make a hosted chapter page (`kidicalmass.be/mons`) more attractive than a separate domain.
**On the event data model (confirmed):** Reading both pages against the event schema draft, all fields are confirmed present. The Mons page adds one important field not previously identified: **named organising team members**. Chapters want to put faces to the local project. This is a community signal, not just operational data.
**On partner management:** Both pages list very specific local partners distinct from national partners. The platform needs a local partner/sponsor section per chapter, separate from the national-level sponsor registry.
---
## The Three User Journeys That Must Work
If the new platform does nothing else, these three journeys must be seamless.
### Journey A: Find a ride near me