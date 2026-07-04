<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description_fr');
        });

        // Backfill the four partners the page previously named in static copy,
        // so the bound cards section is never empty on existing databases.
        DB::table('partners')->whereNull('group_id')->where('name', 'like', '%Brussel Mobiliteit%')->update(['category' => 'institutioneel']);
        DB::table('partners')->whereNull('group_id')->where('name', 'like', '%Brussel Stad%')->update(['category' => 'institutioneel']);
        DB::table('partners')->whereNull('group_id')->where('name', 'like', 'Gemeente Schaarbeek%')->update(['category' => 'institutioneel']);
        DB::table('partners')->whereNull('group_id')->where('name', 'like', '%Clean Cities%')->update(['category' => 'bondgenoot']);
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
