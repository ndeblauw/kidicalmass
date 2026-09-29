<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * French municipality names, keyed on the Dutch name. Only fills gaps: an
     * existing name_fr is never overwritten.
     *
     * @var array<string, string>
     */
    private const MUNICIPALITIES = [
        'Schaarbeek' => 'Schaerbeek',
        'Elsene' => 'Ixelles',
        'Brussel Stad' => 'Bruxelles-Ville',
        'Vorst' => 'Forest',
        'Anderlecht' => 'Anderlecht',
        'Molenbeek' => 'Molenbeek',
        'Jette' => 'Jette',
        'Etterbeek' => 'Etterbeek',
        'Ukkel' => 'Uccle',
        'Sint-Gillis' => 'Saint-Gilles',
        'Watermaal-Bosvoorde' => 'Watermael-Boitsfort',
        'Woluwe' => 'Woluwe',
        'Laken' => 'Laeken',
        'Koekelberg' => 'Koekelberg',
        'Evere-Haren' => 'Evere-Haren',
        'Neder-Over-Heembeek' => 'Neder-Over-Heembeek',
        'Luik' => 'Liège',
        'Tubeke' => 'Tubize',
        'Terhulpen' => 'La Hulpe',
        'Namen' => 'Namur',
        'Moeskroen' => 'Mouscron',
        'Bergen' => 'Mons',
        'Gent' => 'Gand',
        'Antwerpen' => 'Anvers',
        'Leuven' => 'Louvain',
        'Brugge' => 'Bruges',
    ];

    /**
     * The invisible region groups. Their English name_nl values are relied on
     * by the chapters index (region keys, colours), so name_nl stays untouched
     * and only the French name is filled.
     *
     * @var array<string, string>
     */
    private const REGIONS_FR = [
        'Belgium' => 'Belgique',
        'Brussels Capital Region' => 'Bruxelles',
        'Wallonia' => 'Wallonie',
        'Flanders' => 'Flandre',
    ];

    public function up(): void
    {
        foreach (self::MUNICIPALITIES + self::REGIONS_FR as $nameNl => $nameFr) {
            DB::table('groups')
                ->where('name_nl', $nameNl)
                ->whereNull('name_fr')
                ->update(['name_fr' => $nameFr]);
        }
    }

    public function down(): void
    {
        // Data fill only; the previous null values cannot be told apart from later edits.
    }
};
