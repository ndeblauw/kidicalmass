# Structure

> How does it fit together? Information architecture and sitemap.

## Navigation Model

Primary discovery is through **location/date-first search** (Events page + Home), not through chapter browsing. Chapters exist as a directory and individual pages, but are not the primary path for families trying to find a ride.

## Language Routing

Parallel URL paths: `/nl/`, `/fr/`, `/en/` — not a language switcher. Chapter pages render in the chapter's own language (FR chapters in FR, NL chapters in NL). National content is available in all three languages.

## Sitemap

```
kidicalmass.be (NL / FR / EN — routed per path)
│
├── Home (/)
│   └── Upcoming events near me, chapter map, movement stats, news preview
│
├── Events (/events)
│   ├── Calendar — filter by location, date, iCal subscription per region
│   └── Event detail (/events/[slug])
│       └── Date, time, meeting point, distance, Komoot route, chapter info
│
├── Chapters (/chapters)
│   ├── Overview — map + list of all chapters, "start a chapter" CTA
│   └── Chapter page (/chapters/[city])
│       ├── Local schedule (pulls from Events)
│       ├── Team
│       ├── Local partners
│       ├── Press coverage
│       ├── Downloads
│       └── Volunteer contact form (routed to local lead)
│
├── Contribute (/contribute)
│   ├── Roles + how to volunteer
│   ├── Contact form → routed to nearest chapter
│   └── Start a chapter (static for MVP; full intake flow deferred)
│
├── About (/about)
│   ├── Mission (/about/mission)
│   │   └── What Kidical Mass is, 3 axes, stats, inclusivity
│   └── Organisation (/about/organisation)
│       └── Governance, coordination duo, local group structure
│
├── News (/news)
│   └── Article (/news/[slug])
│
└── Partners (/partners)
    └── Active national sponsors/partners with logo display
```

## Admin (separate)

```
/admin — Filament panel
├── Coordination duo: full access across all chapters
└── Chapter lead: own chapter only (events, team, partners, press, downloads)
```

## Key Structural Decisions

- **No chapter as primary nav path** — families use Events (calendar + location filter), not Chapters, to find a ride. Chapters serve as a directory and local home page.
- **Volunteer contact form lives on chapter pages** — not a centralised form. The Contribute page explains roles and routes people to the right chapter.
- **Chapter pages are self-published** — chapter leads manage their own content within design constraints. No approval flow needed.
- **Chapters overview doubles as growth story** — the map of all chapters supports new chapter leads and grant applications.
- **Mission and Organisation stay structurally separate** — consistent with current site; content restructured and rewritten using the tone of voice guide.
- **News in main nav** — consistent with current site structure.
