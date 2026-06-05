<?php

namespace App\Support\Location;

class CurrentLocation
{
    /**
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
            'name' => (string) $data['name'],
        ];
    }
}
