"""Rebuild be-postcodes.csv from the three open sources listed in be-postcodes.SOURCES.md.

Usage (stdlib only; the bpost and Statbel spreadsheets must first be exported to
UTF-8 CSV, e.g. `soffice --headless --convert-to csv:"Text - txt - csv (StarCalc)":44,34,76 file.xls`):

    python3 build-be-postcodes.py BE.txt conversion.csv zipcodes_num_nl.csv zipcodes_num_fr.csv > be-postcodes.csv

Output: one row per postcode that GeoNames places on the map, with the main
municipality in Dutch and French (Statbel), every locality and municipality name
that should find it (pipe-separated), and a coordinate for the main locality.
"""

import csv
import sys
import unicodedata


def norm(value):
    value = value.replace('\u2019', "'")
    return unicodedata.normalize('NFKD', value).encode('ascii', 'ignore').decode().lower().strip()


def fix(value):
    # The bpost sheets are cp1252; some exports turn the oe ligature into a raw control byte.
    return value.replace('\x9c', 'œ').strip()


geonames_path, conversion_path, bpost_nl_path, bpost_fr_path = sys.argv[1:5]

geonames = {}
for row in csv.reader(open(geonames_path, encoding='utf-8'), delimiter='\t'):
    geonames.setdefault(row[1], []).append((row[2], float(row[9]), float(row[10])))

municipalities = {}
for row in list(csv.reader(open(conversion_path, encoding='utf-8')))[1:]:
    municipalities.setdefault(row[0], []).append({'nl': row[2].strip(), 'fr': row[3].strip()})

localities = {}
main_names = {}
for path in (bpost_nl_path, bpost_fr_path):
    for row in list(csv.reader(open(path, encoding='utf-8')))[1:]:
        zip_code, place, is_section, main = row[0], fix(row[1]), row[2], fix(row[3])
        localities.setdefault(zip_code, []).append(place)
        if is_section in ('Neen', 'Non'):
            main_names.setdefault(zip_code, []).append((place, main))

writer = csv.writer(sys.stdout, lineterminator='\n')
writer.writerow(['zip', 'name', 'name_nl', 'name_fr', 'localities', 'latitude', 'longitude'])

for zip_code in sorted(geonames):
    options = municipalities.get(zip_code)
    if not options:
        continue

    mains = main_names.get(zip_code, [])
    main_keys = {norm(main) for _, main in mains}
    municipality = next((m for m in options if {norm(m['nl']), norm(m['fr'])} & main_keys), options[0])

    place = mains[0][0] if mains else municipality['nl']
    wanted = {norm(municipality['nl']), norm(municipality['fr']), norm(place)}
    lat, lng = next(((la, ln) for name, la, ln in geonames[zip_code] if norm(name) in wanted), geonames[zip_code][0][1:])

    # bpost spells the local name with its accents and casing (Blégny, Villers-le-Bouillet);
    # prefer it over Statbel's spelling whenever both name the same place.
    name_nl = place if norm(place) == norm(municipality['nl']) else municipality['nl']
    name_fr = place if norm(place) == norm(municipality['fr']) else municipality['fr']

    candidates = (
        [name_nl, name_fr, place]
        + [m[k] for m in options for k in ('nl', 'fr')]
        + localities.get(zip_code, [])
        + [name for name, _, _ in geonames[zip_code]]
    )

    names = []
    for name in candidates:
        if name and norm(name) not in {norm(n) for n in names}:
            names.append(name)

    writer.writerow([zip_code, place, name_nl, name_fr, '|'.join(names), f'{lat:.4f}', f'{lng:.4f}'])
