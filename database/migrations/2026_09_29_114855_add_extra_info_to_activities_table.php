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
        Schema::table('activities', function (Blueprint $table) {
            $table->text('extra_info_nl')->nullable()->after('content_en');
            $table->text('extra_info_fr')->nullable()->after('extra_info_nl');
            $table->text('extra_info_en')->nullable()->after('extra_info_fr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['extra_info_nl', 'extra_info_fr', 'extra_info_en']);
        });
    }
};
