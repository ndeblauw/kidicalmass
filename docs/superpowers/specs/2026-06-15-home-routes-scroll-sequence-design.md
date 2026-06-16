---
title: "Home routes → scroll-sequence"
tags: [design, home, scrollytelling, component, refactor]
sources: [resources/views/home.blade.php, resources/views/volunteer.blade.php, resources/css/pages/help-out.css]
phase: design
updated: 2026-06-15
---

# Home routes → scroll-sequence

## Probleem

De homepage sluit nu af met `home-routes`: een 3-koloms grid van `<x-route-card>`
("Nieuw hier?", "Help mee", "Vind je lokale groep") plus twee decoratieve signposts.
Het is een compacte "crossroads" van drie gelijkwaardige, korte kaartjes. Frederik wil
deze omzetten naar het patroon van `ho-deal` op `/help-out`: drie volwaardige **secties**
met elk een titel + langere uitleg, en één **sticky illustratie** die crossfade't naar de
volgende terwijl je scrollt.

Die scrollytelling-mechaniek bestaat al, maar zit pagina-specifiek vastgebakken op
`/help-out` (`.ho-deal*` in `resources/css/pages/help-out.css`, crossfade-JS inline in
`resources/views/volunteer.blade.php`). We trekken het patroon uit tot één herbruikbare
component en gebruiken die op zowel de home als `/help-out`.

## Beslissingen (vastgelegd in brainstorm)

1. **Doorlink:** elke sectie eindigt met een eigen CTA-knop naar zijn pagina.
2. **Uiterlijk illustratie:** kaal op wit. Geen tint-vlak, geen frame. De kleuridentiteit
   komt uit de (al gekleurde) illustraties zelf.
3. **Bouw:** gedeelde component extraheren; `/help-out` mee porten zodat `.ho-deal`
   verdwijnt.
4. **Volgorde + closing-CTA:** secties in funnel-volgorde **Nieuw → Vind groep → Help mee**.
   De home-closing-CTA verandert van "Vind je lokale groep" naar **"Word lid"**
   (`membership`), om de dubbele "Vind je groep"-uitgang te vermijden (taste-regel: geen
   dubbele content).

## Architectuur

### Gedeelde component: `<x-scroll-sequence>`

Een herbruikbare scrollytelling-eenheid. Bezit **alléén**: de 2-koloms layout (tekstkolom +
sticky media-kolom), het sticky-gedrag, de crossfade-op-scroll en het mobiel-stacken. Bezit
**niet** het uiterlijk van de losse media-items of blokken — die zijn geslot, zodat dezelfde
component past op twee verschillende invullingen.

**Interface (slot-gebaseerd, expliciete index):**

```blade
<x-scroll-sequence media-side="right">
    {{-- sticky media-kolom: N items, elk met data-seq-media="i"; item 0 krijgt is-active --}}
    <x-slot:media>
        <div class="scroll-sequence__media-item is-active" data-seq-media="0"> … </div>
        <div class="scroll-sequence__media-item"            data-seq-media="1"> … </div>
        …
    </x-slot:media>

    {{-- tekstkolom: N blokken, elk met data-seq-block="i" --}}
    <div class="scroll-sequence__block" data-seq-block="0"> titel + tekst + CTA </div>
    <div class="scroll-sequence__block" data-seq-block="1"> … </div>
    …
</x-scroll-sequence>
```

**Props:**
- `media-side` — `left` | `right` (default `right`). Bepaalt aan welke kant de sticky
  media-kolom staat. Home en `/help-out` gebruiken beide `right`; de prop houdt de
  component open voor variatie zonder nu extra varianten te bouwen.

**Crossfade-gedrag (Alpine, in de component zelf — geen `@push`):**
- `x-data` op de wrapper houdt het actieve index bij.
- Een `IntersectionObserver` in `x-init` observeert de `[data-seq-block]`-blokken met een
  smalle band rond het verticale midden (`rootMargin: '-45% 0px -45% 0px'`, zoals `ho-deal`).
- Bij intersectie leest hij de index en zet `is-active` op het overeenkomstige
  `[data-seq-media]`-item; de rest verliest `is-active`.
- Alpine i.p.v. de huidige inline `@push('scripts')`-aanpak, zodat de JS in de component
  woont en op beide pagina's werkt zonder duplicatie.

**Layout & responsive (CSS-partial `resources/css/components/scroll-sequence.css`,
`@layer components`):**
- **Desktop (lg+):** `grid-template-columns: 1fr 1fr` (gap ~4.5rem). Tekstblokken krijgen
  `min-height` ~78vh en worden verticaal gecentreerd, zodat één blok tegelijk leest. De
  media-kolom is `position: sticky` (top ~10.5rem). Media-items liggen op elkaar (`absolute
  inset:0`), `opacity: 0` → `is-active` = `opacity: 1`, met een zachte transition.
- **Mobiel (<lg):** één verhalende kolom. De sticky media-kolom verdwijnt; elke sectie
  toont zijn **eigen** illustratie inline boven titel+tekst+CTA, geen crossfade (mirrort hoe
  `ho-deal` nu stackt). DOM-strategie voor het inline tonen per blok is een
  implementatiedetail voor het plan (bv. de media-items dubbel renderen, breakpoint-getoggled,
  óf per blok een inline illustratie).
- `prefers-reduced-motion: reduce` → geen transition/scale, item klapt direct om.

### Home-invulling

Vervangt de huidige `home-routes`-`<section>` in `resources/views/home.blade.php`:
- Verwijder het grid, de twee `<img>` signposts en de drie `<x-route-card>`.
- Eén `<x-scroll-sequence>` met drie media-items (kale illustraties op wit, `object-contain`)
  en drie blokken (titel + paragraaf + `<x-cta-button>`).

| # | Titel | Illustratie | CTA (label → route) |
|---|-------|-------------|---------------------|
| 1 | Nieuw hier? | `waving-rider.svg` | "Zo werkt een rit →" → `getting-started` |
| 2 | Vind je lokale groep | `longtail-with-kid.svg` | "Vind je groep →" → `groups.index` |
| 3 | Help mee | `volunteer-with-wrench.svg` | "Word vrijwilliger →" → `volunteer` |

**Concept-copy** (Frederik doet zijn eigen verfijnpass; tone-of-voice geldt, geen em-dashes):

1. **Nieuw hier?** — "Nog nooit meegefietst? Geen zorgen. Een Kidical Mass is een rustige,
   vrolijke fietsparade door je eigen buurt, op kindertempo, met de kruispunten veilig
   vrijgehouden. Je hoeft niets te kunnen en je hoeft je niet in te schrijven. Gewoon komen
   en meefietsen."
2. **Vind je lokale groep** — "Kidical Mass is geen organisatie ver weg, maar de mensen in
   jouw buurt. Overal in Vlaanderen en Brussel plannen lokale groepen hun eigen ritten. Vind
   de groep bij jou, en je weet meteen wanneer de volgende rit vertrekt en wie erachter zit."
3. **Help mee** — "Een rit ontstaat niet vanzelf. Achter elke parade staan ouders en buren
   die de route uittekenen, de boel aankondigen en in een roze hesje meefietsen. Een paar uur
   per maand, en je krijgt er een warme bende vrienden voor terug."

**Closing-CTA** (in `home.blade.php`): van `groups.index` / "Vind je lokale groep" naar
`membership` / "Word lid", met een passende heading (escalerende slot-vraag).

### `/help-out` port

`resources/views/volunteer.blade.php`: vervang de `.ho-deal`-`<section>` + de inline
crossfade-`@push('scripts')` door `<x-scroll-sequence>`. Media-items = de twee foto's
(behouden hun frame-uiterlijk: rounded + shadow + `object-cover`, want dat blijft hun eigen
look en blijft geslot in het media-item). Blokken = de twee `<x-titled-list-block>`. Verwijder
de `.ho-deal*`-regels uit `resources/css/pages/help-out.css`.

> Let op: het foto-frame (rounded/shadow/cover) is *media-item-eigen* uiterlijk en blijft dus
> in de help-out-markup; de gedeelde component bemoeit zich daar niet mee. Op de home zijn de
> media-items kaal (geen frame). Zo blijft de component puur layout + gedrag.

### Op te ruimen

- `resources/views/components/route-card.blade.php` — verwijderen (alleen op home gebruikt).
- `.ho-deal*`-regels in `resources/css/pages/help-out.css` — verwijderen (vervangen door de
  gedeelde partial).
- De twee signpost-`<img>` in `home-routes` — verwijderen.

## Tests

- **CssArchitectureTest** moet blijven slagen: nieuwe partial
  `resources/css/components/scroll-sequence.css` registreren in `app.css`; geen rauwe hex/px
  in de nieuwe Blade-component.
- **PublicStructureTest** (en bestaande home/help-out tests): de home moet nog links bevatten
  naar `getting-started`, `groups.index` en `volunteer`; `/help-out` moet z'n
  "Wat je krijgt"/"Wat we vragen"-inhoud + foto's behouden. Bijwerken waar selectors op de
  oude `.home-routes`/`route-card`/`.ho-deal`-structuur leunen.
- Een nieuwe test (of uitbreiding) bevestigt dat de home de drie sectie-titels + drie CTA's
  rendert in de afgesproken volgorde, en dat de closing-CTA naar `membership` wijst.
- Verifieer visueel (één screenshot-pass) dat de crossfade werkt op desktop en netjes stackt
  op mobiel, op zowel home als `/help-out`.

## Build-pipeline

Na afronding: home-rij (P-nn) bijwerken (Wire blijft 🟠 tot Frederik's eigen
critique+refine-pass), `/help-out` ongewijzigd qua status maar noteren dat de mechaniek nu
gedeeld is. Log-entry in `docs/wiki/log.md`. (Via `/pipeline`.)
