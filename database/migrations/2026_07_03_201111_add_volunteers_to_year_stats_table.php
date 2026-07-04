<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('year_stats', function (Blueprint $table) {
            $table->unsignedInteger('volunteers')->nullable()->after('participants');
        });
    }

    public function down(): void
    {
        Schema::table('year_stats', function (Blueprint $table) {
            $table->dropColumn('volunteers');
        });
    }
};
