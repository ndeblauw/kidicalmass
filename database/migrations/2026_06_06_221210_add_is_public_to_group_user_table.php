<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Volunteer roster opt-in (D-1, Decision C): a volunteer can choose to show
 * themselves on the public chapter page. Default false — the full roster stays
 * visible to logged-in volunteers regardless; this flag only governs public surfacing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_user', function (Blueprint $table): void {
            $table->boolean('is_public')->default(false)->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('group_user', function (Blueprint $table): void {
            $table->dropColumn('is_public');
        });
    }
};
