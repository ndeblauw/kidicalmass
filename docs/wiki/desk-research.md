---
title: Desk Research Plan & Findings
tags: []
sources: [notion/desk-research.md]
updated: 2026-04-13
---

# 📚 Desk Research — Findings
---
## 1. The International Kidical Mass Ecosystem
**Origin:** Founded in Eugene, Oregon in 2008 by Shane Rhodes. First UK ride in London 2015. In Europe the movement is led from Germany.
**Global coordination:** The **Kidical Mass Aktionsbündnis** ([kinderaufsrad.org](http://kinderaufsrad.org/)) is the international hub — 1000+ local organisations across 20+ countries. They run two coordinated international action weeks per year (spring + autumn, around World Car Free Day in September). In May 2025, 125,000+ people participated across 550+ locations in ~20 countries.
**Digital model:** Deliberately decentralised. No shared CMS or chapter platform exists at the international level. They share an open-source document library and a self-registration map for action weeks. Every local group builds its own digital presence independently. **Belgium would be pioneering a structured chapter platform — no playbook to copy from internationally.**
**Key insight:** The international site ([kidsonbike.org](http://kidsonbike.org/)) uses a map widget where chapters self-register for action weeks via a form. That's the extent of "chapter management." The rest is WhatsApp and email.
---
## 2. The Liège Split — Natural Experiment
Liège has its own separate site: [kidicalmassliege.org](http://kidicalmassliege.org/) — built on Google Sites (free, zero-design). It is the only Belgian chapter with its own digital presence outside [kidicalmass.be](http://kidicalmass.be/).
**What the Liège site does:**
- Current ride info (date, meeting point, programme, route length)
- What / Who / Why
- Volunteer ask
- Local partner logos (GRACQ Liège, Urbagora, CNCD, Pro Vélo Liège — distinct from Brussels partners)
- Own Instagram + Facebook accounts
**No connection to **[kidicalmass.be](http://kidicalmass.be/)** visible on the Liège site.** The national site lists them but links away. They built something minimal themselves because the national site gave them no chapter space.
**The lesson:** Chapters will find alternatives if the platform doesn't serve them. Liège didn't ask for permission or help — they just made a Google Site. The new platform needs to make being *part* of the national site more valuable than going it alone.
---
## 3. Sponsor & Partner Documents
**Sponsor formulas** (read from public PDF):
**Implications for the build:**
- The website is a core deliverable to sponsors — logo placement is a contractual commitment
- A new site must maintain visible, reliable partner/sponsor sections (per chapter and national)
- Currently there's no automated or structured way to manage this — it's manual
**Partner charter** (read from public PDF) — key clauses:
- Partners must share values: child-friendliness, safety, sustainability, inclusivity
- Kidical Mass maintains full editorial independence — no sponsor can influence messaging
- Agreements are formalised per email or written contract
- GDPR compliance required from all partners
**Implication:** Partner management is currently entirely by email and goodwill. The platform could eventually include a partner portal or at minimum a structured partner directory per chapter.
---
## 4. Comparable Organisations — What They Teach Us
### Repair Café International — the closest structural parallel
Repair Café is the best comparable model. Same DNA: grassroots, volunteer-run, local chapters with central brand, grown from one city to a global movement.
**How they solved the chapter problem:**
- Central site ([repaircafe.org](http://repaircafe.org/)) is an international directory — searchable by country, province, state
- Local chapters register via a €49 one-off starter kit (includes manual, templates, brand assets)
- Each registered chapter gets listed on the global map/directory
- Community pages per country for cross-chapter contact
- No shared CMS — local chapters run their own sites or use the directory listing
**What this tells us:** Even at 3800+ locations, Repair Café doesn't give chapters individual managed pages — they just get a directory listing. Kidical Mass Belgium has an opportunity to go further by giving chapters actual editable pages. That's more ambitious but also more valuable.
### Pro Vélo — Belgian cycling org, useful bilingual model
Pro Vélo operates across Brussels + Wallonia + Flanders and has had to navigate Belgian regionalism. Key insight: they started as FR-focused (Brussels/Wallonia) and only added Flanders coordination later — similar trajectory to Kidical Mass. They use separate regional websites with a national coordination layer rather than one unified bilingual site.
**Bilingual lesson:** Their approach suggests that forcing FR and NL together on a single site creates friction. Separate language routes (/fr and /nl) with shared content models is the standard Belgian solution.
### GRACQ / Fietsersbond — the "two solitudes" of Belgian cycling advocacy
GRACQ is the French-speaking cyclists' advocacy org; Fietsersbond is the Flemish equivalent. They are sister organisations that cooperate but are structurally separate. Pro Vélo was actually founded by both of them together.
**Lesson for Kidical Mass:** Belgium's linguistic divide is institutional, not just a translation problem. The Wallonia chapters and Flanders chapters may develop distinct identities over time. The platform should treat FR and NL as genuine parallel structures, not just translations of each other.
### The international Kidical Mass model — no central platform exists
[Kinderaufsrad.org](http://kinderaufsrad.org/) (Germany) has 1000+ local groups and manages them through a self-registration form and action week map. There is no chapter CMS — everything is email + open-source doc library. **Belgium is ahead of the international movement** in wanting structured chapter pages. This is an opportunity to build something that could eventually be shared with other national networks.
---
## 5. What We Now Know About the Event Data Model
From reading the agenda page carefully, a Kidical Mass parade event consistently contains:
**Required fields:**
- Date
- Municipality / chapter name
- Meeting point (address or location name)
- Start time
- Route distance (km)
- Facebook event link (current dependency — will remain for a while)
**Optional / variable fields:**
- Theme (e.g. "Spooky Edition", "Fête de l'Iris")
- Special programme notes (music, animations)
- Partner event link
- Season notes (Summer break, Winterbreak)
**What's missing from the current agenda but always asked:**
- Map of route (currently in Google Maps or Komoot, linked from Facebook)
- End location
- Contact for the chapter
This is the minimum viable schema for the parade model in Laravel. Chapters self-publish; national calendar auto-aggregates.
---
## 6. The Sponsor/Finance Picture
The Growfunding page didn't render (JavaScript-heavy), but from the site and press coverage we know:
- They run recurring Spacefunding / crowdfunding campaigns on [Growfunding.be](http://growfunding.be/)
- Funders include: Bruxelles Mobilité, Clean Cities, Bruxelles Ville, Schaerbeek, Clean Cities Campaign, Alveus Coop, Cera, [Grafik.brussels](http://grafik.brussels/)
- Bank: VDK (Triodos-linked ethical bank) — BE72 8919 4405 3116
- Sponsor revenue tiers: €100–€2,500/year
- **No online donation flow on the website itself** — bank transfer only, which is a significant friction point
---
## 7. What Desk Research Has Confirmed vs What Remains Unknown
### Confirmed ✅
- The Facebook dependency is structural, not laziness — there's no alternative on the site
- The calendar is hand-maintained with no fallback — this is the #1 admin pain point
- No chapter has a managed page — Liège went its own way, Flanders doesn't exist yet
- Bilingual stacking is the wrong pattern — parallel routing (/fr, /nl) is the Belgian standard
- The international movement has no shared platform — Belgium can pioneer this
- Repair Café is the best structural analogue — directory + self-registration, but Kidical BE wants to go further with actual managed chapter pages
- The organisation is a vzw (non-profit) with GDPR obligations and formal partner agreements
### Still unknown ❓ (requires talking to people)
- How many hours/week does the coordination duo spend on website-adjacent admin?
- Do chapter leads feel the national site serves them at all?
- What's the volunteer dropout rate between "email sent" and "first ride done"?
- Did the Facebook-first approach happen by choice or by necessity (Wix calendar was too hard)?
- What is the actual relationship with Liège — deliberate split or just drift?
- Is Flanders a real near-term growth area or aspirational?
- What do sponsors currently complain about regarding their visibility on the site?
- Does the coordination duo have any technical capacity, or is it purely volunteer-managed?
---
## 8. Priority Open Tasks