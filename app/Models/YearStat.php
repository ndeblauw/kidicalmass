<?php

namespace App\Models;

use Database\Factories\YearStatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A curated, per-year impact figure for the "Steun ons" proof deck. Only the
 * participant and volunteer counts are stored here: local-group and ride totals are derived live
 * from their own tables. One row per calendar year.
 */
class YearStat extends Model
{
    /** @use HasFactory<YearStatFactory> */
    use HasFactory;

    protected $fillable = [
        'year',
        'participants',
        'volunteers',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'participants' => 'integer',
            'volunteers' => 'integer',
        ];
    }
}
