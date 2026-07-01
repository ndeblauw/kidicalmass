<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Group members are now public-by-default (opt-out): the team rosters on the
     * chapter and ride pages list members unless they opt out. The column shipped
     * default-false, so existing memberships predate any real opt-out choice —
     * backfill them to public so current rosters stay visible.
     */
    public function up(): void
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->boolean('is_public')->default(true)->change();
        });

        DB::table('group_user')->update(['is_public' => true]);
    }

    public function down(): void
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->change();
        });
    }
};
