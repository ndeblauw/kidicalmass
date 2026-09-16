<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Activities: location becomes a localized field; add the en columns.
        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('location', 'location_nl');
            $table->string('location_fr')->nullable();
            $table->string('location_en')->nullable();
            $table->string('title_en')->nullable();
            $table->text('content_en')->nullable();
        });

        // Articles: add the en columns.
        Schema::table('articles', function (Blueprint $table) {
            $table->string('title_en')->nullable();
            $table->text('content_en')->nullable();
        });

        // Groups: name becomes a localized field.
        Schema::table('groups', function (Blueprint $table) {
            $table->renameColumn('name', 'name_nl');
            $table->string('name_fr')->nullable();
            $table->string('name_en')->nullable();
        });

        // Partners: name becomes a localized field; description gains an en column.
        Schema::table('partners', function (Blueprint $table) {
            $table->renameColumn('name', 'name_nl');
            $table->string('name_fr')->nullable();
            $table->string('name_en')->nullable();
            $table->text('description_en')->nullable();
        });

        // Press articles: add the en column.
        Schema::table('press_articles', function (Blueprint $table) {
            $table->string('title_en')->nullable();
        });

        // Quotes: quote and attribution become localized fields.
        Schema::table('quotes', function (Blueprint $table) {
            $table->renameColumn('quote', 'quote_nl');
            $table->renameColumn('attribution', 'attribution_nl');
            $table->text('quote_fr')->nullable();
            $table->text('quote_en')->nullable();
            $table->string('attribution_fr')->nullable();
            $table->string('attribution_en')->nullable();
        });

        // Team members: role becomes a localized field; bio gains an en column.
        Schema::table('team_members', function (Blueprint $table) {
            $table->renameColumn('role', 'role_nl');
            $table->string('role_fr')->nullable();
            $table->string('role_en')->nullable();
            $table->text('bio_en')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['bio_en', 'role_en', 'role_fr']);
            $table->renameColumn('role_nl', 'role');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['attribution_en', 'attribution_fr', 'quote_en', 'quote_fr']);
            $table->renameColumn('quote_nl', 'quote');
            $table->renameColumn('attribution_nl', 'attribution');
        });

        Schema::table('press_articles', function (Blueprint $table) {
            $table->dropColumn('title_en');
        });

        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn(['description_en', 'name_en', 'name_fr']);
            $table->renameColumn('name_nl', 'name');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'name_fr']);
            $table->renameColumn('name_nl', 'name');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['content_en', 'title_en']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['content_en', 'title_en', 'location_en', 'location_fr']);
            $table->renameColumn('location_nl', 'location');
        });
    }
};
