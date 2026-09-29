<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Serves the public ride lookups, which all filter on type + published and a
     * begin_date range (upcoming rides on Home, parades per year in the stats).
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->index(['activity_type', 'is_published', 'begin_date'], 'activities_type_published_begin_index');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('activities_type_published_begin_index');
        });
    }
};
