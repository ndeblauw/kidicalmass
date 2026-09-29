# be-postcodes.csv - sources and licences

`be-postcodes.csv` has one row per Belgian postcode.
`PostalCodeSeeder` loads it into the `postal_codes` table.
`build-be-postcodes.py` rebuilds it from the sources below.

| Column | Meaning | Source |
| --- | --- | --- |
| `zip` | Postcode | GeoNames (only postcodes GeoNames can place on the map) |
| `name` | Main place name as bpost spells it, in the local language | bpost |
| `name_nl`, `name_fr` | Official municipality name in Dutch and French (Brussel/Bruxelles, Luik/Liège) | Statbel |
| `localities` | Every name that should find the postcode, pipe-separated: municipality names in both languages, every bpost locality (deelgemeente/sous-commune), every GeoNames place name | Statbel, bpost, GeoNames |
| `latitude`, `longitude` | Coordinate of the main locality, else the first GeoNames place for the postcode | GeoNames |

## Sources

- **Statbel**, "Conversion Postal code_Refnis code" (version 1 January 2025), from https://statbel.fgov.be/nl/over-statbel/methodologie/classificaties/geografie.
  Maps each postcode to its municipality (or municipalities) with the Dutch and French name.
  Statbel publishes its open data for free reuse with attribution (Statbel open data licence, CC BY 4.0).
- **bpost**, postcode list sorted by postcode (`zipcodes_num_nl_2025.xls` and `zipcodes_num_fr_2025.xls`), from https://www.bpost.be/nl/postcodevalidatie-tool.
  Lists every locality per postcode and whether it is a deelgemeente, which gives Dutch names like Laken, Komen and Waasten that the other sources lack.
  bpost offers the list as a free public download for postcode validation; it names no formal open licence.
- **GeoNames**, postal codes for Belgium (`BE.zip`), from https://download.geonames.org/export/zip/.
  Provides the coordinates and the French place names.
  Licensed CC BY 4.0, credit to https://www.geonames.org.

## Open licence question: bpost

Checked on 2026-09-29: neither the bpost download page nor the file states a licence or terms of reuse.
The open-data portals that republish the same list (ODWB, Opendatasoft `georef-belgium-postal-codes`) mark it "other-open" or "custom licence" without a clear grant either.
bpost is the only source here for Dutch names of deelgemeenten (Laken, Komen, Waasten) and for which locality is the main one per postcode.
The repo is public, so this needs a decision from the project owners before merge: get bpost's written OK to redistribute, or rebuild without the bpost columns.
Without bpost, `name_nl`/`name_fr` still come from Statbel and `localities` keeps the Statbel and GeoNames names (both CC BY 4.0), but loses the Dutch deelgemeente names.

Retrieved 2026-09-29.
Rebuild when Belgian municipalities merge or bpost publishes a new list.
