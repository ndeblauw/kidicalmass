<?php

namespace Database\Seeders;

use App\Models\PostalCode;
use Illuminate\Database\Seeder;

/**
 * Loads database/data/be-postcodes.csv (sources and licences in
 * be-postcodes.SOURCES.md). Columns: zip, name, name_nl, name_fr,
 * localities (pipe-separated), latitude, longitude.
 */
class PostalCodeSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/be-postcodes.csv');

        if (! is_readable($path)) {
            $this->command?->warn("Postcode dataset missing at {$path}; skipping.");

            return;
        }

        $handle = fopen($path, 'r');
        fgetcsv($handle, 0, ',', '"', ''); // header

        $rows = [];
        $now = now();

        while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            if (count($row) < 7 || $row[0] === '') {
                continue;
            }

            [$zip, $name, $nameNl, $nameFr, $localities, $latitude, $longitude] = $row;

            $rows[] = [
                'zip' => $zip,
                'name' => $name,
                'name_nl' => $nameNl,
                'name_fr' => $nameFr,
                'search_names' => PostalCode::searchNamesFor([$nameNl, $nameFr, $name, ...explode('|', $localities)]),
                'latitude' => (float) $latitude,
                'longitude' => (float) $longitude,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        fclose($handle);

        PostalCode::query()->delete();
        foreach (array_chunk($rows, 500) as $chunk) {
            PostalCode::insert($chunk);
        }
    }
}
