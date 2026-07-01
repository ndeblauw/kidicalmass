<?php

use App\Enums\ActivityType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_published')->default(false);
            $table->string('title_nl');
            $table->string('title_fr');
            $table->text('content_nl')->nullable();
            $table->text('content_fr')->nullable();
            $table->enum('activity_type', array_keys(ActivityType::getOptionsArray()))->default(ActivityType::KIDICALMASS->value);
            $table->dateTime('begin_date');
            $table->string('location')->nullable();
            $table->string('distance')->nullable();
            $table->string('komoot_url')->nullable();
            $table->string('commute_link')->nullable();
            $table->string('postal_code')->nullable();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('organizer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('duration_minutes')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_group', function (Blueprint $table) {
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_group');
        Schema::dropIfExists('activities');
    }
};
