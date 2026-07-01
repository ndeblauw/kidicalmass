<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('year_stats', function (Blueprint $table) {
            $table->id();
            // One row per calendar year. The page's proof deck reads the most
            // recent year so adding next year's row rolls the whole deck forward.
            $table->unsignedSmallInteger('year')->unique();
            // Manually curated in the admin: there is no attendance tracking to
            // derive this from. Nullable so a year can exist before the count is in.
            $table->unsignedInteger('participants')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('year_stats');
    }
};
