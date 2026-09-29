<?php

use App\Models\PostalCode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the main municipality in Dutch and French plus a normalised list of every
     * name that should find the postcode. Existing rows get a search list built from
     * their one name, so search keeps working until PostalCodeSeeder reloads the
     * full dataset.
     */
    public function up(): void
    {
        Schema::table('postal_codes', function (Blueprint $table) {
            $table->string('name_nl')->nullable()->after('name');
            $table->string('name_fr')->nullable()->after('name_nl');
            $table->text('search_names')->nullable()->after('name_fr');
        });

        DB::table('postal_codes')->select(['id', 'name'])->orderBy('id')->each(function (object $row): void {
            DB::table('postal_codes')->where('id', $row->id)->update([
                'search_names' => PostalCode::searchNamesFor([$row->name]),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postal_codes', function (Blueprint $table) {
            $table->dropColumn(['name_nl', 'name_fr', 'search_names']);
        });
    }
};
