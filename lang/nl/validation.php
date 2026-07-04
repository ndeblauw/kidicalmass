<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Dutch validation lines
|--------------------------------------------------------------------------
|
| The site runs entirely in Dutch (app.locale = nl). Without this file the
| translator falls back to the raw key, so every form error rendered as
| "validation.email" / "validation.required". These lines cover the rules
| the public forms actually use; add more as new rules appear. Per-field
| overrides live in `custom`, friendly attribute names in `attributes`.
|
*/

return [
    'accepted' => 'Je moet :attribute aanvaarden.',
    'array' => ':attribute moet een keuze zijn.',
    'email' => ':attribute is geen geldig e-mailadres.',
    'in' => 'De gekozen :attribute is ongeldig.',
    'integer' => ':attribute moet een getal zijn.',
    'max' => [
        'array' => ':attribute mag niet meer dan :max keuzes bevatten.',
        'numeric' => ':attribute mag niet groter zijn dan :max.',
        'string' => ':attribute mag niet langer zijn dan :max tekens.',
    ],
    'min' => [
        'array' => 'Kies minstens :min keuze.',
        'numeric' => ':attribute moet minstens :min zijn.',
        'string' => ':attribute moet minstens :min tekens bevatten.',
    ],
    'required' => 'Vul :attribute in.',
    'string' => ':attribute moet tekst zijn.',
    'unique' => ':Attribute is al in gebruik.',

    /*
    |--------------------------------------------------------------------------
    | Custom validation language lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'email' => [
            'required' => 'Vul je e-mailadres in.',
            'email' => 'Dit lijkt geen geldig e-mailadres. Kijk even na op een typfout.',
            'max' => 'Dat e-mailadres is wel erg lang. Klopt het wel?',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom validation attributes
    |--------------------------------------------------------------------------
    |
    | Friendly, lowercase Dutch names so a generic message reads naturally,
    | e.g. "Vul je e-mailadres in." instead of "Vul email in.".
    |
    */

    'attributes' => [
        'email' => 'je e-mailadres',
        'name' => 'je naam',
        'place' => 'je gemeente of postcode',
        'motivation' => 'je motivatie',
        'message' => 'je bericht',
        'phone' => 'je telefoonnummer',
    ],
];
