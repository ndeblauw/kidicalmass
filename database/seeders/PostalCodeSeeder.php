<?php

namespace Database\Seeders;

use App\Models\PostalCode;
use Illuminate\Database\Seeder;

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
            if (count($row) < 4 || $row[0] === '') {
                continue;
            }

            $rows[] = [
                'zip' => $row[0],
                'name' => $row[1],
                'latitude' => (float) $row[2],
                'longitude' => (float) $row[3],
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
