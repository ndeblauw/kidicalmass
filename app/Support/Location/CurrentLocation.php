<?php

namespace App\Support\Location;

use App\Models\PostalCode;

class CurrentLocation
{
    /**
     * The cookie keeps the name as it was when the place was picked. The label is
     * re-read for the current locale, so a Brussels cookie reads "Brussel" on the
     * NL site and "Bruxelles" on the FR one, and cookies from before the dataset
     * carried municipality names (4000 "Rocourt") read "Luik"/"Liège".
     *
     * @return array{zip: string, lat: float, lng: float, name: string}|null
     */
    public static function resolve(): ?array
    {
        $raw = request()->cookie(config('location.cookie'));

        if (! is_string($raw) || $raw === '') {
            return null;
        }

        $data = json_decode($raw, true);

        if (! is_array($data) || ! isset($data['zip'], $data['lat'], $data['lng'], $data['name'])) {
            return null;
        }

        return [
            'zip' => (string) $data['zip'],
            'lat' => (float) $data['lat'],
            'lng' => (float) $data['lng'],
            'name' => PostalCode::where('zip', (string) $data['zip'])->first()?->localizedName() ?? (string) $data['name'],
        ];
    }
}
