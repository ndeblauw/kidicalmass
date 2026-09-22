# Missing translations

Pages that exist in one locale but not the other. The header language switch
greys out a locale that has no route for the current page (with a
"coming soon" tooltip) and `alternate_locale_url()` returns `null`, so no
`<link rel="alternate" hreflang>` is emitted for it.

Add an entry whenever a new locale-only page lands, and remove it once the
counterpart route exists.

## French pending (NL-only pages)

| Route name | NL path | Notes |
| --- | --- | --- |
| `groups.roze-hesjes` | `/nl/chapters/{group}/roze-hesjes` | Logged-in roze-hesje hub; renders its own member shell, so the switch is not shown here. |
| `groups.roze-hesjes.aan-de-slag` | `/nl/chapters/{group}/roze-hesjes/aan-de-slag` | Hub sub-page. |
| `groups.roze-hesjes.agenda` | `/nl/chapters/{group}/roze-hesjes/agenda` | Hub sub-page. |
| `groups.roze-hesjes.fotos` | `/nl/chapters/{group}/roze-hesjes/fotos` | Hub sub-page. |
| `groups.roze-hesjes.groep` | `/nl/chapters/{group}/roze-hesjes/groep` | Hub sub-page. |
| `groups.roze-hesjes.materiaal` | `/nl/chapters/{group}/roze-hesjes/materiaal` | Hub sub-page. |
| `groups.ride-preview` | `/nl/chapters/{group}/rit-in-voorbereiding` | Membership-gated draft-ride preview; renders the public header, so FR appears greyed there. |

## Redirect helpers (not pages)

These are 301 helpers, not user-facing pages, so they stay NL-shaped:

- `membership.legacy` (`/nl/membership` → `/nl/steun-ons`)
- `cookies` (`/nl/cookies` → `/nl/privacy`)
